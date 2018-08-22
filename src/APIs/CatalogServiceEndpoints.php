<?php
/**
 * /catalog/service/:service
 * User: moyo
 * Date: 24/08/2017
 * Time: 5:27 PM
 */

namespace Carno\Consul\APIs;

class CatalogServiceEndpoints extends AbstractEndpointsLister
{
    /**
     * @var string
     */
    protected $uri = '/catalog/service/:service';

    /**
     * @param array $endpoint
     * @return array
     */
    protected function genInfo(array $endpoint) : array
    {
        return [
            $endpoint['ServiceID'],
            $endpoint['ServiceName'],
            $endpoint['ServiceAddress'],
            $endpoint['ServicePort'],
            $endpoint['ServiceTags']
        ];
    }

    /**
     * add some params in url
     */
    protected function addParams() : void
    {
        // do nothing
    }
}
