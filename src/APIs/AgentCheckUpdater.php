<?php
/**
 * /agent/check/[status]
 * User: moyo
 * Date: 18/09/2017
 * Time: 5:17 PM
 */

namespace Carno\Consul\APIs;

use Carno\Consul\Features\Signature;
use Carno\Consul\Results\Failed;
use Carno\Consul\Results\Success;
use Carno\Promise\Promised;

class AgentCheckUpdater extends AbstractGate
{
    public const PASS = 'pass';
    public const WARN = 'warn';
    public const FAIL = 'fail';

    /**
     * @var string
     */
    protected $method = 'PUT';

    /**
     * @var string
     */
    protected $uri = '/agent/check/:status/:cid';

    /**
     * @param string $cid
     * @param string $status
     * @return static
     */
    public function related(string $cid, string $status) : self
    {
        $this->setVars('cid', $cid);
        $this->setVars('status', $status);
        return $this;
    }

    /**
     * @return static
     */
    public function signature() : self
    {
        $this->setQuery('note', Signature::gen());
        return $this;
    }

    /**
     * @return Promised|Success|Failed
     */
    public function result()
    {
        return $this->simpleHCodeResult();
    }
}
