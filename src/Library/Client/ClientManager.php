<?php

namespace Pushjs\Library\Client;

use Pushjs\Library\Enum\UpcHttpRequestMode;
use Pushjs\Library\Enum\UpcHttpRequestParam;
use Pushjs\Library\Enum\UpcMessageId;
use Pushjs\Library\Http\ConnectionManager;
use Pushjs\Library\Querybuilder\HttpQueryBuilder;
use Pushjs\Library\Upcbuilder\UpcBuilder;
use Pushjs\Library\Upcreader\UpcReader;

class ClientManager
{
    private $connectionManager;

    private $queryBuilder;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        $this->queryBuilder = new HttpQueryBuilder();
    }

    public function setAttribute(string $name, string $value): bool
    {
        $upc = new UpcBuilder(UpcMessageId::SET_CLIENT_ATTR);
        $upc->addArgument($this->connectionManager->getClientId());
        $upc->addArgument('');
        $upc->addArgument($name);
        $upc->addArgument($value);
        $upc->addArgument('');
        $upc->addArgument(4);

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);
        $pip = $this->connectionManager->getHttpClient()->poll($this->connectionManager->getRequestNumber(), $this->connectionManager->getSessionId());
        $xml = (new UpcReader())->read($pip);

        return (string) $xml->xpath('/root/U/L/A')[5] === 'SUCCESS';
    }
}