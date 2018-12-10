<?php
/**
 * Service endpoints
 * User: moyo
 * Date: 25/09/2017
 * Time: 11:49 AM
 */

namespace Carno\Consul\Chips;

use Carno\Consul\Exception\NoneEndpointException;
use Carno\Net\Endpoint;

trait SEndpoints
{
    /**
     * @var Endpoint[]
     */
    private $endpoints = [];

    /**
     * @return Endpoint
     */
    public function endpoint() : Endpoint
    {
        if (is_null($endpoint = $this->endpoints[0] ?? null)) {
            throw new NoneEndpointException;
        } else {
            return $endpoint;
        }
    }

    /**
     * @return Endpoint|null
     */
    public function getEndpoint() : ?Endpoint
    {
        return $this->endpoints[array_rand($this->endpoints)] ?? null;
    }

    /**
     * @return Endpoint[]
     */
    public function getEndpoints() : array
    {
        return $this->endpoints;
    }

    /**
     * @param Endpoint ...$eps
     * @return self
     */
    public function addEndpoint(Endpoint ...$eps) : self
    {
        $this->endpoints = array_merge($this->endpoints, $eps);
        return $this;
    }

    /**
     * @param Endpoint ...$eps
     * @return self
     */
    public function setEndpoints(Endpoint ...$eps) : self
    {
        $this->endpoints = $eps;
        return $this;
    }
}
