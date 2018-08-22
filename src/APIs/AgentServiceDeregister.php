<?php
/**
 * /agent/service/deregister
 * User: moyo
 * Date: 18/09/2017
 * Time: 6:01 PM
 */

namespace Carno\Consul\APIs;

use Carno\Consul\Types\Result;
use Carno\Consul\Types\Service;
use Carno\Promise\Promised;

class AgentServiceDeregister extends AbstractGate
{
    /**
     * @var string
     */
    protected $method = 'PUT';

    /**
     * @var string
     */
    protected $uri = '/agent/service/deregister/:sid';

    /**
     * @var Service
     */
    private $service = null;

    /**
     * @param Service $service
     * @return self
     */
    public function service(Service $service) : self
    {
        $this->service = $service;

        $this->setVars('sid', $service->id());

        return $this;
    }

    /**
     * @return Promised|Result
     */
    public function result()
    {
        return $this->simpleHCodeResult()->then(function (Result $result) {
            return $this->service->deregistered($result);
        });
    }
}
