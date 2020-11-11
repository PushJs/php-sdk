<?php

namespace pushjs\Library\Client;

use pushjs\Library\Enum\UpcHttpRequestMode;
use pushjs\Library\Enum\UpcHttpRequestParam;
use pushjs\Library\Enum\UpcMessageId;
use pushjs\Library\Http\ConnectionManager;
use pushjs\Library\Querybuilder\HttpQueryBuilder;
use pushjs\Library\Upcbuilder\UpcBuilder;

class ClientManager
{
    private $connectionManager;

    private $queryBuilder;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        $this->queryBuilder = new HttpQueryBuilder();
    }

    public function setAttribute(string $name, string $value)
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

//        $upc = $this->connectionManager->getHttpClient()->poll();
//
//        return $this->upcReader->read($upc);
//
//        return (string) $xml->xpath('/root/U/L/A')[5] === 'SUCCESS';
    }
}