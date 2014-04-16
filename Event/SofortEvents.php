<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 10:44
 */

namespace Sofort\Event;

/**
 * Class SofortEvents
 *
 * @package Sofort\Event
 */
class SofortEvents
{
    const SUCCESS      = 'sofort.success';
    const ABORT        = 'sofort.abort';
    const NOTIFICATION = 'sofort.notification';
    const DETAILS      = 'sofort.transaction.details';
}
