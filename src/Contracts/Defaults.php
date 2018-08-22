<?php
/**
 * Default opts
 * User: moyo
 * Date: 2018/6/28
 * Time: 3:52 PM
 */

namespace Carno\Consul\Contracts;

interface Defaults
{
    // default keepalive in seconds
    public const HEARTBEAT = 5;

    // TTL redundancy seconds
    public const KA_TTL_REDUNDANCY = 5;

    // service will de-register if critical-state last more then 1 minute
    public const KA_CRITICAL_TIMEOUT = 1;

    // retry delay in milliseconds
    public const ERROR_RETRY_MIN = 2000;
    public const ERROR_RETRY_MAX = 5000;

    // flag in svc
    public const SVC_FLAG = 'php';
}
