<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 10:44
 */

namespace Sofort\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class PaymentController
 *
 * @package Sofort\Controller
 */
class PaymentController extends Controller
{
    /**
     * Payment success action
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/success/{id}", name="sofort.success")
     */
    public function successAction(Request $request)
    {
        return $this->getManager()->dispatchSuccess($request);
    }

    /**
     * Payment abort action
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/abort/{id}", name="sofort.abort")
     */
    public function abortAction(Request $request)
    {
        return $this->getManager()->dispatchAbort($request);
    }

    /**
     * Payment notification action
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/notification", name="sofort.notification")
     */
    public function notificationAction(Request $request)
    {
        return $this->getManager()->dispatchNotification($request);
    }

    /**
     * Gets manager
     *
     * @return \Sofort\Manager\SofortManager
     */
    protected function getManager()
    {
        return $this->container->get('sofort.manager');
    }
}
