<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 16:46
 */

namespace Sofort\Status;

/**
 * Class StatusReason
 *
 * @package Sofort\Status
 */
class StatusReason extends AbstractClassConstants
{
    const NOT_CREDITED               = 'not_credited';
    const NOT_CREDITED_YET           = 'not_credited_yet';
    const CREDITED                   = 'credited';
    const PARTIALLY_CREDITED         = 'partially_credited';
    const OVERPAYMENT                = 'overpayment';
    const COMPENSATION               = 'compensation';
    const REFUNDED                   = 'refunded';
    const SOFORT_BANK_ACCOUNT_NEEDED = 'sofort_bank_account_needed';
}