<?php
/**
 * KVStore reader
 * User: moyo
 * Date: 13/10/2017
 * Time: 5:45 PM
 */

namespace Carno\Consul;

use Carno\Channel\Chan;
use Carno\Consul\APIs\KVGetting;
use Carno\Consul\Chips\AgentRequired;
use Carno\Consul\Chips\GWatcher;
use Carno\Consul\Chips\WValues;

class KVStore
{
    use AgentRequired, WValues, GWatcher;

    /**
     * keys=* means all keys in folder
     * keys=abc means keys prefix with "abc"
     * @param string $folder
     * @param string $keys
     * @param Chan $notify
     */
    public function watching(string $folder, string $keys, Chan $notify) : void
    {
        $path = $keys === '*' ? $folder : sprintf('%s/%s', $folder, $keys);

        $ig = function () use ($path) {
            return (new KVGetting($this->agent))->path($path);
        };

        $do = function (KVGetting $lister) use ($notify) {
            yield $notify->send($this->changed(yield $lister->result()));
        };

        $this->nwProcess($notify->closed(), $ig, $do, 'KVStore watcher interrupted', ['path' => $path]);
    }
}
