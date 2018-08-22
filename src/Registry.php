<?php
/**
 * Service register
 * User: moyo
 * Date: 24/08/2017
 * Time: 3:55 PM
 */

namespace Carno\Consul;

use Carno\Consul\APIs\AgentServiceRegister;
use Carno\Consul\Chips\AgentRequired;
use Carno\Consul\Contracts\Defaults;
use Carno\Consul\Types\Agent;
use Carno\Consul\Types\Result;
use Carno\Consul\Types\Service;
use function Carno\Coroutine\async;
use function Carno\Coroutine\msleep;
use Carno\Net\Address;
use Carno\Net\Endpoint;
use Throwable;

class Registry
{
    use AgentRequired;

    /**
     * @param Address $advertise
     * @param string $service
     * @param array $tags
     * @param int $heartbeat
     * @return Service
     */
    public function servicing(
        Address $advertise,
        string $service,
        array $tags = [],
        int $heartbeat = Defaults::HEARTBEAT
    ) : Service {
        $serviced = (new Service($service))
            ->setEndpoints(
                (new Endpoint($advertise))
                    ->relatedService($service)
                    ->setTags(...$tags)
                    ->resetID()
            )->setKeepalive($heartbeat)
        ;

        async(static function (Agent $agent, Service $service) {
            /**
             * @var Result $registered
             */
            for (;;) {
                try {
                    $registered = yield (new AgentServiceRegister($agent))->service($service)->result();
                } catch (Throwable $e) {
                    logger('consul')->warning('Service registering error', [
                        'svc' => $service->id(),
                        'error' => sprintf('%s::%s', get_class($e), $e->getMessage()),
                    ]);
                    goto RETRYING;
                }

                if ($registered->success()) {
                    break;
                }

                logger('consul')->info('Service registering failed', [
                    'svc' => $service->id(),
                    'agent' => $service->hosting(),
                ]);

                RETRYING:

                yield msleep($sleep = rand(Defaults::ERROR_RETRY_MIN, Defaults::ERROR_RETRY_MAX));

                logger('consul')->notice('Service register retrying', [
                    'svc' => $service->id(),
                    'delay' => $sleep,
                ]);
            }
        }, null, $this->agent, $serviced)->fusion();

        return $serviced;
    }
}
