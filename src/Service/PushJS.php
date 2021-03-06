<?php

namespace Pushjs\Service;

use Pushjs\Library\Channel\ChannelManager;
use Pushjs\Library\Client\Client;
use Pushjs\Library\Client\ClientManager;
use Pushjs\Library\Event\EventManager;
use Pushjs\Library\Http\ConnectionManager;
use Pushjs\Library\Http\HttpsClient;

class PushJS
{
    private $key;

    private $host;

    private $port;

    private $secure;

    private $httpClient;

    private $connectionManager;

    private $channelManager;

    private $clientManager;

    private $eventManager;

    private $client;

    public function __construct(string $apiKey, string $host = '', int $port = 0, bool $secure = true)
    {
        $this->key = $apiKey;
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        $this->httpClient = new HttpsClient($host, $port, $secure);
        $this->connectionManager = new ConnectionManager($this->httpClient);
        $this->channelManager = new ChannelManager($this->connectionManager);
        $this->clientManager = new ClientManager($this->connectionManager);
        $this->eventManager = new EventManager($this->connectionManager);
        $this->connect();
    }

    public function connect(): Client
    {
        $this->connectionManager->handshake();
        $this->clientManager->setAttribute('apikey', $this->key);

        $this->client = new Client($this->clientManager, $this->connectionManager->getClientId(), ['key' => $this->key]);
        return $this->client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getConnectionManager(): ConnectionManager
    {
        return $this->connectionManager;
    }

    public function getChannelManager(): ChannelManager
    {
        return $this->channelManager;
    }

    public function getClientManager(): ClientManager
    {
        return $this->clientManager;
    }

    public function getEventManager(): EventManager
    {
        return $this->eventManager;
    }
}
