<?php
/**
 * Service keepalive
 * User: moyo
 * Date: 18/09/2017
 * Time: 10:28 PM
 */

namespace Carno\Consul\Chips;

use Carno\Consul\Contracts\Defaults;
use Carno\Consul\Features\Keepalive;

trait SKeepalive
{
    /**
     * @var int
     */
    private $keepalive = null;

    /**
     * @var Keepalive
     */
    private $daemon = null;

    /**
     * @param int $heartbeat
     * @return self
     */
    public function setKeepalive(int $heartbeat) : self
    {
        $this->keepalive = $heartbeat;
        return $this;
    }

    /**
     * @return array
     */
    public function getKeepalive() : array
    {
        return $this->keepalive ? [
            'TTL' => sprintf('%ds', $this->keepalive + Defaults::KA_TTL_REDUNDANCY),
            'DeregisterCriticalServiceAfter' => sprintf('%dm', Defaults::KA_CRITICAL_TIMEOUT),
        ] : [];
    }

    /**
     */
    protected function kaStartup() : void
    {
        $this->daemon && $this->daemon->shutdown();
        $this->keepalive && $this->daemon = new Keepalive($this, $this->keepalive);
    }

    /**
     */
    protected function kaShutdown() : void
    {
        $this->daemon && $this->daemon->shutdown();
    }
}
