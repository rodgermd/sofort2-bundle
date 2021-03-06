<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 10:29
 */

namespace Sofort\Manager;

use Sofort\Api\SofortCreateTransactionApi;
use Sofort\Api\SofortNotificationApi;
use Sofort\Api\SofortRequestTransactionApi;
use Sofort\Event\PaymentEvent;
use Sofort\Event\SofortEvents;
use Sofort\Event\TransactionCreateEvent;
use Sofort\Event\TransactionDetailsEvent;
use Sofort\Exception\InsufficientCredentialsException;
use Sofort\Exception\SofortPaymentException;
use Sofort\Model\SofortPaymentRequestModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SofortManager
 *
 * @package Sofort\Manager
 */
class SofortManager
{
    /** @var string */
    protected $config_key;
    /** @var \Sofort\Api\SofortCreateTransactionApi */
    protected $createApi;
    /** @var \Sofort\Api\SofortCreateTransactionApi */
    protected $requestApi;
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var SofortRoutesManager */
    protected $routesManager;
    /** @var ValidatorInterface */
    protected $validator;

    /**
     * Object constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param SofortRoutesManager      $routesManager
     * @param ValidatorInterface       $validator
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, SofortRoutesManager $routesManager, ValidatorInterface $validator)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->routesManager   = $routesManager;
        $this->validator       = $validator;
    }

    /**
     * Sets config key
     *
     * @param string $key
     */
    public function setConfigKey($key)
    {
        $this->config_key = $key;
    }

    /**
     * Makes pay request
     *
     * @param SofortPaymentRequestModel $model
     *
     * @throws \Sofort\Exception\SofortPaymentException
     * @throws \Sofort\Exception\InsufficientCredentialsException
     * @return TransactionCreateEvent
     */
    public function createTransaction(SofortPaymentRequestModel $model)
    {
        $this->validate($model);

        if (!$this->config_key) {
            throw new InsufficientCredentialsException();
        }
        $api = new SofortCreateTransactionApi($this->config_key);

        $this->routesManager->setTransactionUrls($api);

        $api
            ->setCustomerProtection(true)
            ->setAmount($model->getAmount())
            ->setSenderAccount($model->getBankCode(), $model->getAccountNumber(), $model->getName())
            ->setSenderCountryCode($model->getCountry())
            ->setEmailCustomer($model->getEmail())
            ->setPhoneCustomer($model->getPhone())
            ->setSenderBic($model->getBic())
            ->setSenderIban($model->getIban())
            ->setReason($model->getReason())
            ->setCurrencyCode($model->getCurrency())
            ->setUserVariable($model->getUserVariable());

        $api->sendRequest();

        if ($api->isError()) {
            throw new SofortPaymentException($api->getError());
        }

        $event = new TransactionCreateEvent($api->getTransactionId(), $api->getPaymentUrl());
        $this->eventDispatcher->dispatch(SofortEvents::CREATED, $event);

        return $event;
    }

    /**
     * Requests transaction details
     *
     * @param string $id
     *
     * @throws \Sofort\Exception\SofortPaymentException
     * @throws \Sofort\Exception\InsufficientCredentialsException
     * @return SofortRequestTransactionApi
     */
    public function requestTransaction($id)
    {

        if (!$this->config_key) {
            throw new InsufficientCredentialsException();
        }
        $api = new SofortRequestTransactionApi($this->config_key);

        $api->addTransaction($id);
        $api->sendRequest();

        if ($api->isError()) {
            throw new SofortPaymentException($api->getError());
        }

        $this->eventDispatcher->dispatch(SofortEvents::DETAILS, new TransactionDetailsEvent($api));

        return $api;
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
        $api     = new SofortNotificationApi($this->config_key);
        $content = $request->getContent();

        $event = new PaymentEvent($request);
        $event->setTransactionId($api->getNotification($content));

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
