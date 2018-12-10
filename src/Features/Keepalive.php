<?php
/**
 * Service keepalive (agent-check updater)
 * User: moyo
 * Date: 18/09/2017
 * Time: 4:18 PM
 */

namespace Carno\Consul\Features;

use Carno\Consul\APIs\AgentCheckUpdater;
use Carno\Consul\APIs\AgentServiceRegister;
use Carno\Consul\Contracts\Defaults;
use Carno\Consul\Results\Failed;
use Carno\Consul\Types\Result;
use Carno\Consul\Types\Service;
use function Carno\Coroutine\co;
use function Carno\Coroutine\go;
use function Carno\Coroutine\msleep;
use Carno\Timer\Timer;
use Generator;
use Throwable;

class Keepalive
{
    /**
     * @var Service
     */
    private $service = null;

    /**
     * @var string
     */
    private $identify = null;

    /**
     * @var string
     */
    private $daemon = null;

    /**
     * Keepalive constructor.
     * @param Service $service
     * @param int $heartbeat
     */
    public function __construct(Service $service, int $heartbeat)
    {
        $this->service = $service;
        $this->identify = sprintf('service:%s', $service->id());

        $this->start($heartbeat);
    }

    /**
     * @param int $heartbeat
     */
    private function start(int $heartbeat) : void
    {
        $this->daemon = Timer::loop($heartbeat * 1000, co(function () {
            yield $this->updating();
        }));

        logger('consul')->info(
            'Agent check updater started',
            ['id' => $this->identify, 'agent' => $this->service->hosting()]
        );

        go($this->updating());
    }

    /**
     * @return void
     */
    public function shutdown() : void
    {
        $this->daemon && Timer::clear($this->daemon);
        logger('consul')->info('Agent check updater stopped', ['id' => $this->identify]);
    }

    /**
     * @return Generator|void
     */
    private function updating()
    {
        /**
         * @var Result $krr
         */

        try {
            $krr = yield (new AgentCheckUpdater($this->service->hosting()))
                ->related($this->identify, AgentCheckUpdater::PASS)
                ->signature()
                ->result()
            ;
        } catch (Throwable $e) {
            $krr = new Failed(sprintf('%s::%s', get_class($e), $e->getMessage()));
        }

        if ($krr->success()) {
            logger('consul')->debug(
                'Agent check update success',
                ['id' => $this->identify, 'agent' => $this->service->hosting()]
            );
        } else {
            logger('consul')->warning(
                'Agent check update failed',
                ['id' => $this->identify, 'agent' => $this->service->hosting(), 'reason' => $krr->reason()]
            );

            yield msleep($sleep = rand(Defaults::ERROR_RETRY_MIN, Defaults::ERROR_RETRY_MAX));

            yield $this->recovering();
        }
    }

    /**
     * @return Generator|void
     */
    private function recovering()
    {
        /**
         * @var Service $service
         * @var Result $srr
         */

        try {
            $srr = yield (new AgentServiceRegister($this->service->agent()))
                ->service($this->service)
                ->result()
            ;
        } catch (Throwable $e) {
            $srr = new Failed(sprintf('%s::%s', get_class($e), $e->getMessage()));
        }

        if ($srr->success()) {
            logger('consul')->info(
                'Service recovering success',
                ['id' => $this->identify, 'agent' => $this->service->hosting()]
            );
        } else {
            logger('consul')->warning(
                'Service recovering failed',
                ['id' => $this->identify, 'agent' => $this->service->hosting(), 'reason' => $srr->reason()]
            );
        }
    }
}
