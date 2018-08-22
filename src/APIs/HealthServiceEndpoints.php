<?php
/**
 * /health/service/:service
 * User: moyo
 * Date: 20/11/2017
 * Time: 6:08 PM
 */

namespace Carno\Consul\APIs;

class HealthServiceEndpoints extends AbstractEndpointsLister
{
    /**
     * @var string
     */
    protected $uri = '/health/service/:service';

    /**
     * @param array $endpoint
     * @return array
     */
    protected function genInfo(array $endpoint) : array
    {
        return [
            $endpoint['Service']['ID'],
            $endpoint['Service']['Service'],
            $endpoint['Service']['Address'],
            $endpoint['Service']['Port'],
            $endpoint['Service']['Tags']
        ];
    }

    /**
     * add some params in url
     */
    protected function addParams() : void
    {
        $this->setQuery('passing', 'true');
    }
}
