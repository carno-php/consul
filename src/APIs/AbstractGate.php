<?php
/**
 * Abstract gate
 * User: moyo
 * Date: 24/08/2017
 * Time: 4:56 PM
 */

namespace Carno\Consul\APIs;

use Carno\Consul\Results\Failed;
use Carno\Consul\Results\Success;
use Carno\Consul\Types\Agent;
use Carno\DNS\DNS;
use Carno\DNS\Result;
use Carno\HTTP\Client;
use Carno\HTTP\Options;
use Carno\HTTP\Standard\Request;
use Carno\HTTP\Standard\Response;
use Carno\HTTP\Standard\Streams\Body;
use Carno\HTTP\Standard\Uri;
use Carno\Promise\Promise;
use Carno\Promise\Promised;

abstract class AbstractGate
{
    // default to http
    private const SCHEME = 'http';

    // version 1
    private const VERSION = 'v1';

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $uri = '/';

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var int
     */
    protected $timeout = 5000;

    /**
     * provided agent
     * @var Agent
     */
    private $agent = null;

    /**
     * assigned agent
     * @var Agent
     */
    private $assigned = null;

    /**
     * @var Client[]
     */
    private $http = [];

    /**
     * @var string
     */
    private $payloadType = 'application/json';

    /**
     * @var string
     */
    private $payloadData = '{}';

    /**
     * AbstractAPI constructor.
     * @param Agent $agent
     */
    final public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return Agent
     */
    final protected function agent() : Agent
    {
        return $this->agent;
    }

    /**
     * @return Agent
     */
    final protected function assigned() : Agent
    {
        return $this->assigned ?? $this->agent;
    }

    /**
     * @return Promised
     */
    final private function assigning() : Promised
    {
        if ($this->assigned) {
            return Promise::resolved($this->assigned);
        }

        DNS::resolve($this->agent->host())->then(function (Result $result) {
            return $this->assigned = new Agent($result->random(), $this->agent->port());
        })->sync($resolving = Promise::deferred());

        return $resolving;
    }

    /**
     * @param Agent $agent
     * @return Client
     */
    final private function http(Agent $agent) : Client
    {
        return
            $this->http[$agent->host()] ??
            $this->http[$agent->host()] = new Client((new Options)->setTimeouts($this->timeout), $agent)
        ;
    }

    /**
     * @param string $key
     * @param string $replaced
     */
    final protected function setVars(string $key, string $replaced) : void
    {
        $this->uri = str_replace(sprintf(':%s', $key), $replaced, $this->uri);
    }

    /**
     * @param string $key
     * @param $val
     */
    final protected function setQuery(string $key, $val) : void
    {
        is_scalar($val) && $this->query[$key] = $val;
    }

    /**
     * @param array $data
     */
    final protected function setPayload(array $data) : void
    {
        $this->payloadData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return string
     */
    final protected function getPayload() : string
    {
        return $this->payloadData;
    }

    /**
     * @param string $raw
     * @return array
     */
    final protected function decodeResponse(string $raw) : array
    {
        return json_decode($raw, true);
    }

    /**
     * @param Promised $canceller
     * @return Promised|Response
     */
    final protected function perform(Promised $canceller = null) : Promised
    {
        $this->assigning()->sync($resolved = Promise::deferred());

        return $resolved->then(function (Agent $agent) use ($canceller) {
            $request = new Request(
                $this->method,
                new Uri(
                    self::SCHEME,
                    $agent->host(),
                    $agent->port(),
                    sprintf("/%s", self::VERSION) . $this->uri,
                    $this->query
                ),
                [
                    'Content-Type' => $this->payloadType,
                ],
                new Body($this->getPayload())
            );

            return $this->http($agent)->perform($request, $canceller);
        });
    }

    /**
     * @return Promised|Success|Failed
     */
    final protected function simpleHCodeResult()
    {
        return $this->perform()->then(static function (Response $response) {
            return
                $response->getStatusCode() === 200
                    ? new Success
                    : new Failed((string)$response->getBody())
                ;
        });
    }

    /**
     * @return mixed
     */
    abstract public function result();
}
