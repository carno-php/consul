<?php
/**
 * Service watcher
 * User: moyo
 * Date: 22/09/2017
 * Time: 4:38 PM
 */

namespace Carno\Consul;

use Carno\Channel\Chan;
use Carno\Consul\APIs\AbstractEndpointsLister;
use Carno\Consul\APIs\CatalogServiceEndpoints;
use Carno\Consul\APIs\HealthServiceEndpoints;
use Carno\Consul\Chips\AgentRequired;
use Carno\Consul\Chips\GWatcher;
use Carno\Consul\Chips\WRouter;
use Carno\Consul\Exception\UnknownOptionsException;

class Discovery
{
    use AgentRequired, WRouter, GWatcher;

    /**
     * [catalog,health]
     */
    private const ENDPOINTS_API = 'health';

    /**
     * @param string $service
     * @param Chan $notify
     */
    public function watching(string $service, Chan $notify) : void
    {
        $ig = function () use ($service) {
            return $this->endpointsLister($service);
        };

        $do = function (AbstractEndpointsLister $lister) use ($notify) {
            yield $notify->send($this->routing(yield $lister->result()));
        };

        $this->nwProcess($notify->closed(), $ig, $do, 'Service watcher interrupted', ['svc' => $service]);
    }

    /**
     * @param string $service
     * @return AbstractEndpointsLister
     */
    private function endpointsLister(string $service) : AbstractEndpointsLister
    {
        switch (self::ENDPOINTS_API) {
            case 'catalog':
                return (new CatalogServiceEndpoints($this->agent))->service($service);
            case 'health':
                return (new HealthServiceEndpoints($this->agent))->service($service);
            default:
                throw new UnknownOptionsException;
        }
    }
}
