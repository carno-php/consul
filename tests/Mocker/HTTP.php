<?php
/**
 * HTTP mocker
 * User: moyo
 * Date: 2018-12-08
 * Time: 22:56
 */

namespace Carno\Consul\Tests\Mocker;

use Carno\HTTP\Contracts\Client;
use Carno\Promise\Promise;
use Carno\Promise\Promised;
use Psr\Http\Message\RequestInterface as Request;

class HTTP implements Client
{
    public function perform(Request $request, Promised $canceller = null) : Promised
    {
        return Promise::resolved(Rules::request($request));
    }

    public function close() : Promised
    {
        return Promise::resolved();
    }

    public function closed() : Promised
    {
        return Promise::resolved();
    }
}
