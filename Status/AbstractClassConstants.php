<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 16:50
 */

namespace Sofort\Status;

/**
 * Class AbstractClassConstants
 *
 * @package Sofort\Status
 */
abstract class AbstractClassConstants
{
    /**
     * Gets class constants
     *
     * @return array
     */
    public static function getConstants()
    {
        $reflection = new \ReflectionClass(__CLASS__);

        return $reflection->getConstants();
    }
} 