<?php
/**
 * KV store values changed
 * User: moyo
 * Date: 13/10/2017
 * Time: 6:01 PM
 */

namespace Carno\Consul\Chips;

use Carno\Consul\Types\KVs;

trait WValues
{
    /**
     * @var KVs
     */
    private $prev = null;

    /**
     * @param KVs $kvs
     * @return array
     */
    protected function changed(KVs $kvs) : array
    {
        $changes = [];

        $curr = (array) $kvs;
        $prev = $this->prev ? (array) $this->prev : [];

        $this->prev = $kvs;

        foreach ($curr as $k => $v) {
            if (isset($prev[$k])) {
                if ($prev[$k] != $v) {
                    $changes[$k] = $v;
                }
                unset($prev[$k]);
            } else {
                $changes[$k] = $v;
            }
        }

        if ($prev) {
            foreach ($prev as $k => $v) {
                $changes[$k] = null;
            }
        }

        return $changes;
    }
}
