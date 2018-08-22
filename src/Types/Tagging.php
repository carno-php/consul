<?php
/**
 * Tagging ops
 * User: moyo
 * Date: 13/12/2017
 * Time: 11:28 AM
 */

namespace Carno\Consul\Types;

use Carno\Net\Chips\Endpoint\Tagging as Chip;

class Tagging
{
    use Chip;

    /**
     * Tagging constructor.
     * @param string ...$tags
     */
    public function __construct(string ...$tags)
    {
        $tags && $this->setTags(...$tags);
    }
}
