<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 10:29
 */

namespace Sofort\Manager;

use Sofort\Api\SofortCreateTransactionApi;
use Sofort\Api\SofortRequestTransactionApi;
use Sofort\Event\PaymentEvent;
use Sofort\Event\SofortEvents;
use Sofort\Exception\SofortPaymentException;
use Sofort\Model\PaymentRequestModel;
use Sofort\Model\TransactionRequestModel;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator;

/**
 * Class SofortManager
 *
 * @package Sofort\Manager
 */
class SofortManager
{
    /** @var \Sofort\Api\SofortCreateTransactionApi */
    protected $createApi;
    /** @var \Sofort\Api\SofortCreateTransactionApi */
    protected $requestApi;
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router */
    protected $router;
    /** @var \Symfony\Component\Validator\Validator */
    protected $validator;

    /**
     * Object constructor
     *
     * @param SofortCreateTransactionApi  $createApi
     * @param SofortRequestTransactionApi $requestApi
     * @param EventDispatcherInterface    $eventDispatcher
     * @param Router                      $router
     * @param Validator                   $validator
     *
     * @internal param \Sofort\Api\SofortApi $api
     */
    public function __construct(SofortCreateTransactionApi $createApi, SofortRequestTransactionApi $requestApi, EventDispatcherInterface $eventDispatcher, Router $router, Validator $validator)
    {
        $this->createApi       = $createApi;
        $this->requestApi      = $requestApi;
        $this->eventDispatcher = $eventDispatcher;
        $this->router          = $router;
        $this->validator       = $validator;
    }

    /**
     * Makes pay request
     *
     * @param PaymentRequestModel $model
     *
     * @throws \Sofort\Exception\SofortPaymentException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createTransaction(PaymentRequestModel $model)
    {
        $this->validate($model);

        $this->createApi->setSuccessUrl($this->router->generate('sofort.success', array('id' => '-TRANSACTION-'), true));
        $this->createApi->setAbortUrl($this->router->generate('sofort.abort', array('id' => '-TRANSACTION-'), true));
        $this->createApi->setNotificationUrl($this->router->generate('sofort.notification', array(), true));

        $this->createApi
            ->setCustomerProtection(true)
            ->setAmount($model->getAmount())
            ->setSenderAccount($model->getBankCode(), $model->getAccountNumber(), $model->getName())
            ->setSenderCountryCode($model->getCountry())
            ->setEmailCustomer($model->getEmail())
            ->setPhoneCustomer($model->getPhone())
            ->setSenderBic($model->getBic())
            ->setSenderIban($model->getIban())
            ->setReason($model->getReason())
            ->setCurrencyCode($model->getCurrency());

        $this->createApi->sendRequest();

        if ($this->createApi->isError()) {
            throw new SofortPaymentException($this->createApi->getError());
        }

        return new RedirectResponse($this->createApi->getPaymentUrl());
    }

    /**
     * Requests transaction details
     *
     * @param string $id
     */
    public function requestTransaction($id)
    {
        $this->requestApi->addTransaction($id);
        $this->requestApi->sendRequest();

        if ($this->requestApi->isError()) {
            throw new SofortPaymentException($this->requestApi->getError());
        }

        $this->eventDispatcher->dispatch(SofortEvents::DETAILS, $this->requestApi);
    }

    /**
     * Dispatches success event
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchSuccess(Request $request)
    {
        $event = $this->prepareEvent($request);
        $this->eventDispatcher->dispatch(SofortEvents::SUCCESS, $event);

        return $event->getResponse();
    }

    /**
     * Dispatches abort event
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchAbort(Request $request)
    {
        $event = $this->prepareEvent($request);
        $this->eventDispatcher->dispatch(SofortEvents::ABORT, $event);

        return $event->getResponse();
    }

    /**
     * Dispatches notification event
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchNotification(Request $request)
    {
        $event = $this->prepareEvent($request);
        $this->eventDispatcher->dispatch(SofortEvents::NOTIFICATION, $event);

        return $event->getResponse();
    }

    /**
     * Prepares event
     *
     * @param Request $request
     *
     * @return \Sofort\Event\PaymentEvent
     */
    protected function prepareEvent(Request $request)
    {
        return new PaymentEvent($request);
    }

    /**
     * Validates object
     *
     * @param mixed $object
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     */
    protected function validate($object)
    {
        $errors = $this->validator->validate($object);
        if ($errors->count()) {
            throw new ValidatorException($errors->__toString());
        }
    }
}
