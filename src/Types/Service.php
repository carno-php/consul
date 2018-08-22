<?php
/**
 * Type service
 * User: moyo
 * Date: 24/08/2017
 * Time: 4:08 PM
 */

namespace Carno\Consul\Types;

use Carno\Consul\Chips\SDeregister;
use Carno\Consul\Chips\SEndpoints;
use Carno\Consul\Chips\SRegister;
use Carno\Consul\Chips\SVersions;
use Carno\Consul\Contracts\Defaults;

class Service
{
    use SRegister, SDeregister, SEndpoints, SVersions;

    /**
     * @var string
     */
    private $name = null;

    /**
     * Service constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function id() : string
    {
        return sprintf('%s:%s-%s', Defaults::SVC_FLAG, $this->name, $this->endpoint()->id());
    }

    /**
     * @return string
     */
    public function name() : string
    {
        return $this->name;
    }
}
