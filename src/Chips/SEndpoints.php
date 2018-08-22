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
     * @param Endpoint ...$eps
     * @return self
     */
    public function setEndpoints(Endpoint ...$eps) : self
    {
        $this->endpoints = $eps;
        return $this;
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
     * get first available endpoint
     * @return Endpoint
     */
    public function getEndpoint() : ?Endpoint
    {
        return $this->endpoints[0] ?? null;
    }

    /**
     * @return Endpoint[]
     */
    public function getEndpoints() : array
    {
        return $this->endpoints;
    }

    /**
     * similar with getEndpoint but will trigger exception if none
     * @return Endpoint
     */
    public function endpoint() : Endpoint
    {
        if (is_null($endpoint = $this->getEndpoint())) {
            throw new NoneEndpointException;
        } else {
            return $endpoint;
        }
    }
}
