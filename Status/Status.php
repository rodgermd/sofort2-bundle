<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 16:45
 */

namespace Sofort\Status;

/**
 * Class Status
 *
 * @package Sofort\Status
 */
class Status extends AbstractClassConstants
{
    const LOSS     = 'loss';
    const PENDING  = 'pending';
    const RECEIVED = 'received';
    const REFUNDED = 'refunded';

}
