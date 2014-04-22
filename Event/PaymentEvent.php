<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 10:56
 */

namespace Sofort\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentEvent
 *
 * @package Sofort\Event
 */
class PaymentEvent extends Event
{
    /** @var \Symfony\Component\HttpFoundation\Response */
    protected $response;
    /** @var \Symfony\Component\HttpFoundation\Request */
    protected $request;
    /** @var string  */
    protected $transactionId;

    /**
     * Object constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->transactionId = $request->get('id');
    }

    /**
     * Gets response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets Response
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Gets request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets TransactionId
     *
     * @param string $transactionId
     *
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Gets TransactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}
