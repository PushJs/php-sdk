<?php

namespace pushjs\Library\Http;

use pushjs\Library\queryBuilder\HttpqueryBuilder;
use pushjs\Library\Upcreader\UpcReader;

class ConnectionManager
{
    private $httpClient;

    private $upcReader;

    private $queryBuilder;

    private $requestNumber;

    private $sessionId;

    private $clientId;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->upcReader = new UpcReader();
        $this->queryBuilder = new HttpqueryBuilder();
        $this->requestNumber = new RequestNumber();
    }

    public function handshake(): bool
    {
        $handshake = new Handshake($this->queryBuilder, $this->httpClient, $this->upcReader, $this->requestNumber);

        $par = $handshake->introduce();
        $this->sessionId = $handshake->getSessionId($par);

        $par = $handshake->getClientInfo($this->sessionId);
        $this->clientId = $handshake->getClientId($par);

        return true;
    }

    public function getRequestNumber(): int
    {
        return $this->requestNumber->getRequestNumber();
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public static function getNullTerminateChar()
    {
        return chr(0);
    }
}