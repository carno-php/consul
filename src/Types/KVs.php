<?php
/**
 * Key and values
 * User: moyo
 * Date: 16/10/2017
 * Time: 11:49 AM
 */

namespace Carno\Consul\Types;

use Carno\Consul\Chips\SVersions;
use ArrayObject;

class KVs extends ArrayObject
{
    use SVersions;

    /**
     * KVs constructor.
     * @param string $path
     * @param array $data
     */
    public function __construct(string $path, array $data)
    {
        foreach ($data as $kv) {
            $this->offsetSet($this->withoutPath($path, $kv['Key']), base64_decode($kv['Value']));
        }
    }

    /**
     * @param string $path
     * @param string $key
     * @return string
     */
    private function withoutPath(string $path, string $key) : string
    {
        return substr($key, strlen($path) + 1);
    }
}
