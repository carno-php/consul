<?php
/**
 * Program signature
 * User: moyo
 * Date: 22/11/2017
 * Time: 4:45 PM
 */

namespace Carno\Consul\Features;

class Signature
{
    /**
     * @return string
     */
    public static function gen() : string
    {
        return sprintf('%s@%s', 'php', gethostname());
    }
}
