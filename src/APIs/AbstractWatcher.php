<?php
/**
 * Abstract watcher
 * User: moyo
 * Date: 13/10/2017
 * Time: 6:21 PM
 */

namespace Carno\Consul\APIs;

use Carno\Consul\Chips\SVersions;
use Carno\HTTP\Standard\Response;
use Carno\Promise\Promise;
use Carno\Promise\Promised;

abstract class AbstractWatcher extends AbstractGate
{
    // service version index
    protected const INDEX_HEADER_KEY = 'X-Consul-Index';

    /**
     * 10m
     * @var int
     */
    protected $timeout = 600000;

    /**
     * wait time for http long polling
     * @var string
     */
    protected $blockingWait = '5m';

    /**
     * rand wait time for empty results (milliseconds) [min,max]
     * @var int[]
     */
    protected $emptyWait = [1000, 2000];

    /**
     * @var Promised
     */
    private $canceller = null;

    /**
     * @param int $index
     */
    protected function setVIndex(int $index) : void
    {
        $this->setQuery('wait', $this->blockingWait);
        $index && $this->setQuery('index', $index);
    }

    /**
     * @param SVersions $versions
     * @param Response $response
     */
    protected function assignVIndex($versions, Response $response) : void
    {
        $versions->setVersion($response->getHeaderLine(self::INDEX_HEADER_KEY));
    }

    /**
     * @param Promised $superior
     * @return static
     */
    public function setCanceller(Promised $superior) : self
    {
        $superior->then(function () {
            $this->canceller && $this->canceller->pended() && $this->canceller->resolve();
        });
        return $this;
    }

    /**
     * @return Promised
     */
    protected function getCanceller() : Promised
    {
        return $this->canceller = Promise::deferred();
    }
}
