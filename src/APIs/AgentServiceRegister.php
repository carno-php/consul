<?php
/**
 * /agent/service/register
 * User: moyo
 * Date: 24/08/2017
 * Time: 4:54 PM
 */

namespace Carno\Consul\APIs;

use Carno\Consul\Types\Result;
use Carno\Consul\Types\Service;
use Carno\Promise\Promised;

class AgentServiceRegister extends AbstractGate
{
    /**
     * @var string
     */
    protected $method = 'PUT';

    /**
     * @var string
     */
    protected $uri = '/agent/service/register';

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

        $endpoint = $service->getEndpoint();

        $this->setPayload([
            'ID' => $service->id(),
            'Name' => $service->name(),
            'Tags' => $endpoint->getTags(),
            'Address' => $endpoint->address()->host(),
            'Port' => $endpoint->address()->port(),
            'Check' => $service->getKeepalive(),
        ]);

        return $this;
    }

    /**
     * @return Promised|Result
     */
    public function result()
    {
        return $this->simpleHCodeResult()->then(function (Result $result) {
            return $this->service->registered($this->agent(), $this->assigned(), $result);
        });
    }
}
