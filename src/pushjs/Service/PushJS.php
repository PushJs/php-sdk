<?php

namespace pushjs\Service;

use pushjs\Library\Http\ConnectionManager;
use pushjs\Library\Http\HttpsClient;

class PushJS
{
    private $key;

    private $host;

    private $port;

    private $secure;

    private $httpClient;

    private $connectionManager;

    public function __construct(string $apiKey, string $host = '', int $port = 0, bool $secure = false)
    {
        $this->key = $apiKey;
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        $this->httpClient = new HttpsClient($host, $port, $secure);
        $this->connectionManager = new ConnectionManager($this->httpClient);
    }

    public function connect(): bool
    {
        $this->connectionManager->handshake();
        return true;
    }
}
