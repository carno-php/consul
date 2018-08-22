<?php
/**
 * Common service endpoints lister
 * User: moyo
 * Date: 20/11/2017
 * Time: 6:20 PM
 */

namespace Carno\Consul\APIs;

use Carno\Consul\Contracts\Defaults;
use Carno\Consul\Types\Service;
use function Carno\Coroutine\msleep;
use Carno\HTTP\Standard\Response;
use Carno\Net\Address;
use Carno\Net\Endpoint;
use Carno\Promise\Promised;

abstract class AbstractEndpointsLister extends AbstractWatcher
{
    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var Service
     */
    private $service = null;

    /**
     * @param string $service
     * @return static
     */
    public function service(string $service) : self
    {
        $this->service = new Service($service);
        $this->setVars('service', $service);
        $this->addParams();
        return $this;
    }

    /**
     * @return Promised|Service
     */
    public function result()
    {
        return $this->perform($this->getCanceller())->then(function (Response $response) {
            if ($response->getStatusCode() !== 200) {
                goto DO_WAIT;
            }

            // reset endpoints
            $this->service->setEndpoints();

            // reset version
            $this->assignVIndex($this->service, $response);
            $this->setVIndex($this->service->getVersion());

            // new endpoints
            $endpoints = $this->decodeResponse((string)$response->getBody());
            foreach ($endpoints as $endpoint) {
                list($id, $name, $host, $port, $tags) = $this->genInfo($endpoint);
                $this->service->addEndpoint(
                    (new Endpoint(new Address($host, $port)))
                        ->relatedService($name)
                        ->assignID($this->distinctID($name, $id))
                        ->setTags(...$tags)
                );
            }

            // continue to next
            if ($this->service->hasVersion()) {
                return $this->service;
            }

            DO_WAIT:

            // make sleep if no versions
            return msleep(rand(...$this->emptyWait), function () {
                return $this->service;
            });
        });
    }

    /**
     * @param string $service
     * @param string $id
     * @return string
     */
    private function distinctID(string $service, string $id) : string
    {
        $id = ltrim($id, sprintf('%s:', Defaults::SVC_FLAG));
        if (substr($id, 0, strlen($service)) === $service) {
            $id = substr($id, strlen($service));
            if (substr($id, 0, 1) === '-') {
                $id = substr($id, 1);
            }
        }
        return $id;
    }

    /**
     * returned syntax
     * [string $id, string $name, string $host, int $port, array $tags]
     * @param array $endpoint
     * @return array
     */
    abstract protected function genInfo(array $endpoint) : array;

    /**
     * triggered when service assigned
     */
    abstract protected function addParams() : void;
}
