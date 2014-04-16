<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 10:29
 */

namespace Sofort\Manager;

use Sofort\Api\SofortApi;
use Sofort\Event\PaymentEvent;
use Sofort\Event\SofortEvents;
use Sofort\Exception\SofortPaymentException;
use Sofort\Model\PaymentRequestModel;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
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
    /** @var \Sofort\Api\SofortApi */
    protected $api;
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router */
    protected $router;
    protected $validator;

    /**
     * Object constructor
     *
     * @param SofortApi                $api
     * @param EventDispatcherInterface $eventDispatcher
     * @param Router                   $router
     * @param Validator                $validator
     */
    public function __construct(SofortApi $api, EventDispatcherInterface $eventDispatcher, Router $router, Validator $validator)
    {
        $this->api             = $api;
        $this->eventDispatcher = $eventDispatcher;
        $this->router          = $router;
        $this->validator       = $validator;

        $this->setDefaultRoutes();
    }

    /**
     * Sets default routes
     */
    public function setDefaultRoutes()
    {
        $this->api->setSuccessUrl($this->router->generate('sofort.success'));
        $this->api->setAbortUrl($this->router->generate('sofort.abort'));
        $this->api->setNotificationUrl($this->router->generate('sofort.notification'));
    }

    /**
     * Makes pay request
     *
     * @param PaymentRequestModel $model
     *
     * @throws \Sofort\Exception\SofortPaymentException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function pay(PaymentRequestModel $model)
    {
        $this->validate($model);

        $this->api
            ->setAmount($model->getAmount())
            ->setSenderAccount($model->getBankCode(), $model->getAccountNumber(), $model->getName())
            ->setSenderCountryCode($model->getCountry())
            ->setEmailCustomer($model->getEmail())
            ->setPhoneCustomer($model->getPhone())
            ->setSenderBic($model->getBic())
            ->setSenderIban($model->getIban())
            ->setReason($model->getReason())
            ->setCurrencyCode($model->getCurrency());

        $this->api->sendRequest();

        if ($this->api->isError()) {
            throw new SofortPaymentException($this->api->getError());
        }

        return new RedirectResponse($this->api->getPaymentUrl());
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