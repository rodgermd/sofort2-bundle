<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 15:07
 */

namespace Sofort\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AbstractSofortEventSubscriber
 *
 * @package Sofort\Event
 */
abstract class SofortAbstractEventSubscriber implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            SofortEvents::SUCCESS      => 'onSuccess',
            SofortEvents::ABORT        => 'onAbort',
            SofortEvents::NOTIFICATION => 'onNotification'
        );
    }

    /**
     * On success
     * Should modify the PaymentEvent response object
     *
     * @param PaymentEvent $event
     */
    abstract public function onSuccess(PaymentEvent $event);

    /**
     * On abort
     * Should modify the PaymentEvent response object
     *
     * @param PaymentEvent $event
     */
    abstract public function onAbort(PaymentEvent $event);

    /**
     * On notification
     * Should modify the PaymentEvent response object
     *
     * @param PaymentEvent $event
     */
    abstract public function onNotification(PaymentEvent $event);
}
