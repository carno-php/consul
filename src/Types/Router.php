<?php
/**
 * Service router command (from watcher)
 * User: moyo
 * Date: 25/09/2017
 * Time: 10:30 AM
 */

namespace Carno\Consul\Types;

use Carno\Net\Endpoint;

class Router
{
    public const JOIN = 0xC1;
    public const LEAVE = 0xC2;

    /**
     * @var int
     */
    private $cmd = null;

    /**
     * @var Endpoint
     */
    private $endpoint = null;

    /**
     * Router constructor.
     * @param int $cmd
     * @param Endpoint $endpoint
     */
    public function __construct(int $cmd, Endpoint $endpoint)
    {
        $this->cmd = $cmd;
        $this->endpoint = $endpoint;
    }

    /**
     * @return bool
     */
    public function joined() : bool
    {
        return $this->cmd === self::JOIN;
    }

    /**
     * @return bool
     */
    public function leaved() : bool
    {
        return $this->cmd === self::LEAVE;
    }

    /**
     * @return Endpoint
     */
    public function target() : Endpoint
    {
        return $this->endpoint;
    }
}
