<?php
/**
 * /kv/:key
 * User: moyo
 * Date: 13/10/2017
 * Time: 6:17 PM
 */

namespace Carno\Consul\APIs;

use Carno\Consul\Types\KVs;
use Carno\HTTP\Standard\Response;
use Carno\Promise\Promised;

class KVGetting extends AbstractWatcher
{
    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $uri = '/kv/:path';

    /**
     * custom path
     * @var string
     */
    private $path = null;

    /**
     * @param string $path
     * @return static
     */
    public function path(string $path) : self
    {
        $this->path = $path;
        $this->setVars('path', $path);
        $this->setQuery('recurse', true);
        return $this;
    }

    /**
     * @return Promised|KVs
     */
    public function result()
    {
        return $this->perform($this->getCanceller())->then(function (Response $response) {
            $kvs = new KVs(
                $this->path,
                $response->getStatusCode() === 200
                    ? $this->decodeResponse((string)$response->getBody())
                    : []
            );

            $this->assignVIndex($kvs, $response);
            $this->setVIndex($kvs->getVersion());

            return $kvs;
        });
    }
}
