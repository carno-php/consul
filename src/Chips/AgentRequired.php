<?php
/**
 * Agent required
 * User: moyo
 * Date: 22/09/2017
 * Time: 4:44 PM
 */

namespace Carno\Consul\Chips;

use Carno\Consul\Types\Agent;

trait AgentRequired
{
    /**
     * @var Agent
     */
    private $agent = null;

    /**
     * Register constructor.
     * @param Agent $agent
     */
    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }
}
