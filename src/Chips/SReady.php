<?php
/**
 * Service ready sta
 * User: moyo
 * Date: 2018/7/2
 * Time: 10:44 AM
 */

namespace Carno\Consul\Chips;

use Carno\Promise\Promise;
use Carno\Promise\Promised;

trait SReady
{
    /**
     * @var Promised
     */
    private $ready = null;

    /**
     * @return Promised
     */
    public function ready() : Promised
    {
        return $this->ready ?? $this->ready = Promise::deferred();
    }
}
