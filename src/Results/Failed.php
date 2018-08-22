<?php
/**
 * Result of failed
 * User: moyo
 * Date: 14/09/2017
 * Time: 3:54 PM
 */

namespace Carno\Consul\Results;

use Carno\Consul\Types\Result;

class Failed extends Result
{
    /**
     * @var bool
     */
    protected $success = false;

    /**
     * @var string
     */
    private $reason = null;

    /**
     * Failed constructor.
     * @param string $reason
     */
    public function __construct(string $reason = '')
    {
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function reason() : string
    {
        return $this->reason;
    }
}
