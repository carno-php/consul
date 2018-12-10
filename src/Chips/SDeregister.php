<?php
/**
 * Service deregister
 * User: moyo
 * Date: 18/09/2017
 * Time: 10:26 PM
 */

namespace Carno\Consul\Chips;

use Carno\Consul\APIs\AgentServiceDeregister;
use Carno\Consul\Contracts\Defaults;
use Carno\Consul\Types\Result;
use Carno\Consul\Types\Service;
use function Carno\Coroutine\async;
use function Carno\Coroutine\msleep;
use Carno\Promise\Promised;
use Throwable;

trait SDeregister
{
    /**
     * @return Promised
     */
    public function deregister() : Promised
    {
        return async(function (Service $service) {
            for (;;) {
                if (!$service->connected()) {
                    logger('consul')->info('Service has not been registered .. skip', ['svc' => $service->id()]);
                    return;
                }

                /**
                 * @var Result $result
                 */

                try {
                    $result = yield (new AgentServiceDeregister($service->hosting()))->service($service)->result();
                } catch (Throwable $e) {
                    logger('consul')->warning('Service deregistering error', [
                        'svc' => $service->id(),
                        'error' => sprintf('%s::%s', get_class($e), $e->getMessage()),
                    ]);
                    goto RETRYING;
                }

                if ($result->success()) {
                    return;
                }

                RETRYING:

                yield msleep($sleep = rand(Defaults::ERROR_RETRY_MIN, Defaults::ERROR_RETRY_MAX));

                logger('consul')->notice('Service deregister retrying', [
                    'svc' => $service->id(),
                    'delay' => $sleep,
                ]);
            }
        }, null, $this);
    }

    /**
     * @param Result $result
     * @return Result
     */
    public function deregistered(Result $result) : Result
    {
        $this->kaShutdown();

        return $result;
    }
}
