<?php
/**
 * Service router/endpoints changed
 * User: moyo
 * Date: 25/09/2017
 * Time: 10:28 AM
 */

namespace Carno\Consul\Chips;

use Carno\Consul\Types\Router;
use Carno\Consul\Types\Service;
use Carno\Net\Endpoint;

trait WRouter
{
    /**
     * @var Endpoint[]
     */
    private $prevEndpoints = [];

    /**
     * @param Service $service
     * @return Router[]
     */
    protected function routing(Service $service) : array
    {
        $commands = [];

        $newEndpoints = [];

        foreach ($service->getEndpoints() as $endpoint) {
            $epn = $endpoint->id();

            $newEndpoints[$epn] = $endpoint;

            if (isset($this->prevEndpoints[$epn])) {
                // exists
                unset($this->prevEndpoints[$epn]);
                continue;
            } else {
                // join
                $commands[] = new Router(Router::JOIN, $endpoint);
            }
        }

        if ($this->prevEndpoints) {
            foreach ($this->prevEndpoints as $endpoint) {
                // leave
                $commands[] = new Router(Router::LEAVE, $endpoint);
            }
        }

        $this->prevEndpoints = $newEndpoints;

        return $commands;
    }
}
