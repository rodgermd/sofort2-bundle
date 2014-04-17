<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 17.04.14
 * Time: 10:42
 */

namespace Sofort\Exception;

/**
 * Class InsufficientCredentialsException
 *
 * @package Sofort\Exception
 */
class InsufficientCredentialsException extends SofortPaymentException
{
    protected $message = 'Sofort config key is not defined';
}
