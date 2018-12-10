<?php
/**
 * Registry test
 * User: moyo
 * Date: 2018-12-08
 * Time: 23:00
 */

namespace Carno\Consul\Tests;

use Carno\Consul\Registry;
use Carno\Consul\Types\Agent;
use Carno\Net\Address;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    public function testServicing()
    {
        $registry = new Registry(new Agent('127.0.0.1', 8500));

        $service = $registry->servicing($addr = new Address('127.0.0.1', 81), 'case1');

        $this->assertEquals('case1', $service->name());
        $this->assertEquals(0, $service->getVersion());
        $this->assertEquals($service->agent(), $service->hosting());
        $this->assertEquals($addr, $service->endpoint()->address());

        $dr = $service->deregister();
        $this->assertEquals(false, $dr->pended());
    }
}
