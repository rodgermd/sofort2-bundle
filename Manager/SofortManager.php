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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * Object constructor
     *
     * @param SofortApi                $api
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(SofortApi $api, EventDispatcherInterface $eventDispatcher)
    {
        $this->api             = $api;
        $this->eventDispatcher = $eventDispatcher;
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
} 