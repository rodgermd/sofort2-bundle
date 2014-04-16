<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 10:42
 */

namespace Sofort\Api;

use Sofort\SofortLibMultipay;

/**
 * Class SofortApi
 *
 * @package Sofort\Api
 */
class SofortApi extends SofortLibMultipay
{

    /**
     * Setter for Customer Protection
     * if possible for customers
     *
     * @param boolean $customerProtection (default true)
     *
     * @return $this
     */
    public function setCustomerProtection($customerProtection = true)
    {
        if (!array_key_exists('su', $this->_parameters) || !is_array($this->_parameters['su'])) {
            $this->_parameters['su'] = array();
        }

        $this->_parameters['su']['customer_protection'] = $customerProtection ? 1 : 0;

        return $this;
    }
}