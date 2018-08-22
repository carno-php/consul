<?php
/**
 * Generic watcher (for service and kv-store)
 * User: moyo
 * Date: 2018/7/2
 * Time: 11:49 AM
 */

namespace Carno\Consul\Chips;

use Carno\Channel\Exception\ChannelClosingException;
use Carno\Consul\APIs\AbstractWatcher;
use Carno\Consul\Contracts\Defaults;
use function Carno\Coroutine\go;
use function Carno\Coroutine\msleep;
use Carno\HTTP\Exception\RequestCancelledException;
use Carno\Promise\Promise;
use Carno\Promise\Promised;
use Closure;
use Throwable;

trait GWatcher
{
    /**
     * new watcher process
     * @param Promised $cc watch-canceller
     * @param Closure $ig instance-generator
     * @param Closure $do worker-do
     * @param string $em error-msg
     * @param array $ec error-context
     */
    protected function nwProcess(Promised $cc, Closure $ig, Closure $do, string $em, array $ec) : void
    {
        go(static function () use ($cc, $ig, $do, $em, $ec) {
            /**
             * @var Promised $ex
             */

            $ex = null;

            $cc->then(function () use (&$ex) {
                $ex && $ex->pended() && $ex->resolve();
            });

            for (;;) {
                if (!isset($lister)) {
                    $lister = $ig();
                    $lister instanceof AbstractWatcher && $lister->setCanceller($ex = Promise::deferred());
                }

                try {
                    yield $do($lister);
                } catch (ChannelClosingException | RequestCancelledException $e) {
                    break;
                } catch (Throwable $e) {
                    unset($lister);

                    logger('consul')->warning(
                        $em,
                        array_merge($ec, ['error' => sprintf('%s::%s', get_class($e), $e->getMessage())])
                    );

                    yield msleep(rand(Defaults::ERROR_RETRY_MIN, Defaults::ERROR_RETRY_MAX));

                    continue;
                }
            }
        });
    }
}
