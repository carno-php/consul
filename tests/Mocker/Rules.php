<?php
/**
 * Rules defined
 * User: moyo
 * Date: 2018-12-08
 * Time: 23:21
 */

namespace Carno\Consul\Tests\Mocker;

use Carno\HTTP\Standard\Response;
use Psr\Http\Message\RequestInterface;

class Rules
{
    private const RR = [
        [
            'PUT', 'http://127.0.0.1:8500/v1/agent/service/register',
            ['Name' => 'case1'],
            200, ''
        ],
        [
            'PUT', 'http://127.0.0.1:8500/v1/agent/check/pass/service:php:case1-',
            [],
            200, ''
        ],
        [
            'PUT', 'http://127.0.0.1:8500/v1/agent/service/deregister/php:case1-',
            [],
            200, ''
        ],
    ];

    public static function request(RequestInterface $req)
    {
        foreach (self::RR as $rule) {
            [$method, $uri, $matcher, $code, $payload] = $rule;
            if ($req->getMethod() !== $method) {
                continue;
            }
            $tar = (string)$req->getUri();
            if ($tar !== $uri && substr($tar, 0, strlen($uri)) !== $uri) {
                continue;
            }
            if ($matcher) {
                $data = json_decode($req->getBody(), true);
                foreach ($matcher as $key => $val) {
                    if (isset($data[$key]) && $data[$key] === $val) {
                        continue;
                    } else {
                        continue 2;
                    }
                }
            }
            return new Response($code, [], $payload);
        }

        return new Response(503, [], 'Rule not matched');
    }
}
