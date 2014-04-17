<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 17.04.14
 * Time: 10:54
 */

namespace Sofort\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransactionCreateEvent
 *
 * @package Sofort\Event
 */
class TransactionCreateEvent extends Event
{
    /** @var RedirectResponse | Response */
    protected $response;
    /** @var string */
    protected $transactionId;

    /**
     * Object constructor
     *
     * @param string $transactionId
     * @param string $sofortUrl
     */
    public function __construct($transactionId, $sofortUrl)
    {
        $this->transactionId = $transactionId;
        $this->response      = new RedirectResponse($sofortUrl);
    }

    /**
     * Gets response
     *
     * @return RedirectResponse|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets response
     *
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Gets transaction id
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

} 