<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 17:25
 */

namespace Sofort\Event;


use Sofort\Api\SofortRequestTransactionApi;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class TransactionDetailsEvent
 *
 * @package Sofort\Event
 */
class TransactionDetailsEvent extends Event
{

    /** @var \Sofort\Api\SofortRequestTransactionApi */
    protected $api;

    /**
     * Object constructor
     *
     * @param SofortRequestTransactionApi $api
     */
    public function __construct(SofortRequestTransactionApi $api)
    {
        $this->api = $api;
    }

    /**
     * Gets data
     *
     * @return SofortRequestTransactionApi
     */
    public function getData()
    {
        return $this->api;
    }
} 