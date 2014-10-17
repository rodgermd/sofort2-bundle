<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 17.04.14
 * Time: 17:11
 */

namespace Sofort\Manager;

use Sofort\Api\SofortCreateTransactionApi;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class SofortRoutesManager
 *
 * @package Sofort\Manager
 */
class SofortRoutesManager
{
    /** @var Router */
    protected $router;
    /** @var null|string */
    protected $defaultHost;

    /**
     * Object constructor
     *
     * @param Router $router
     * @param string $defaultHost
     */
    public function __construct(Router $router, $defaultHost = null)
    {
        $this->router      = $router;
        $this->defaultHost = $defaultHost;
    }

    /**
     * Sets transaction api urls
     *
     * @param SofortCreateTransactionApi $api
     */
    public function setTransactionUrls(SofortCreateTransactionApi $api)
    {
        $api->setSuccessUrl($this->generateSuccessUrl());
        $api->setAbortUrl($this->generateAbortUrl());
        $api->setNotificationUrl($this->generateNotificationUrl());
    }

    /**
     * Generates success url
     *
     * @return string
     */
    public function generateSuccessUrl()
    {
        return $this->updateUrl($this->router->generate('sofort.success', array('id' => '-TRANSACTION-'), true));
    }

    /**
     * Generates abort url
     *
     * @return string
     */
    public function generateAbortUrl()
    {
        return $this->updateUrl($this->router->generate('sofort.abort', array('id' => '-TRANSACTION-'), true));
    }

    /**
     * Generates notification url
     *
     * @return string
     */
    public function generateNotificationUrl()
    {
        return $this->updateUrl($this->router->generate('sofort.notification', array('id' => '-TRANSACTION-'), true));
    }

    /**
     * Updates url
     *
     * @param $url
     *
     * @return string
     */
    protected function updateUrl($url)
    {
        if ($this->defaultHost) {
            $url = str_replace($this->router->getContext()->getHost(), $this->defaultHost, $url);
        }

        return $url;
    }

}
