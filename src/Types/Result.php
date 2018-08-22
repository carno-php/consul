<?php
/**
 * Type result
 * User: moyo
 * Date: 14/09/2017
 * Time: 3:36 PM
 */

namespace Carno\Consul\Types;

abstract class Result
{
    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @return bool
     */
    final public function success() : bool
    {
        return $this->success;
    }

    /**
     * @return bool
     */
    final public function failed() : bool
    {
        return ! $this->success;
    }
}
