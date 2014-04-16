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
    /** @var \Symfony\Component\HttpFoundation\Request  */
    protected $request;

    /**
     * Object constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->response = new Response();
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
} 