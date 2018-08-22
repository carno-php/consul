<?php
/**
 * Service register
 * User: moyo
 * Date: 18/09/2017
 * Time: 10:26 PM
 */

namespace Carno\Consul\Chips;

use Carno\Consul\Results\Success;
use Carno\Consul\Types\Agent;
use Carno\Consul\Types\Result;

trait SRegister
{
    use SReady, SKeepalive;

    /**
     * @var Agent
     */
    private $providedAgent = null;

    /**
     * @var Agent
     */
    private $assignedAgent = null;

    /**
     * @param Agent $provided
     * @param Agent $assigned
     * @param Result $result
     * @return Result
     */
    public function registered(Agent $provided, Agent $assigned, Result $result) : Result
    {
        $this->providedAgent = $provided;
        $this->assignedAgent = $assigned;

        if ($result instanceof Success) {
            $this->kaStartup();
            $this->ready && $this->ready->pended() && $this->ready->resolve();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function connected() : bool
    {
        return !! $this->assignedAgent;
    }

    /**
     * @return Agent
     */
    public function agent() : Agent
    {
        return $this->providedAgent;
    }

    /**
     * @return Agent
     */
    public function hosting() : Agent
    {
        return $this->assignedAgent;
    }
}
