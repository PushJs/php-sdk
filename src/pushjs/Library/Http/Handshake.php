<?php

namespace pushjs\Library\Http;

use pushjs\Library\Enum\UpcHttpRequestMode;
use pushjs\Library\Enum\UpcHttpRequestParam;
use pushjs\Library\Enum\UpcMessageId;
use pushjs\Library\Querybuilder\HttpQueryBuilder;
use pushjs\Library\Upcbuilder\UpcBuilder;
use pushjs\Library\Upcreader\UpcReader;

class Handshake
{
    private $queryBuilder;

    private $upcReader;

    private $httpClient;

    private $requestNumber;

    public function __construct(
        HttpQueryBuilder $queryBuilder,
        HttpClientInterface $httpClient,
        UpcReader $upcReader,
        RequestNumber $requestNumber
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->httpClient = $httpClient;
        $this->upcReader = $upcReader;
        $this->requestNumber = $requestNumber;
    }

    public function introduce(): \SimpleXMLElement
    {
        $userAgent = ''
            . 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:47.0)'
            . 'Gecko/20100101 Firefox/47.0;2.1.1 (Build 856)';

        $builder = new UpcBuilder(UpcMessageId::MESSAGE_ID_CLIENT_HELLO);

        $builder->addArgument('PushJS');
        $builder->addArgument($userAgent);
        $builder->addArgument('1.10.3');

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND_RECEIVE,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($builder->getUpc())
            ),
            $this->requestNumber->getRequestNumber()
        );

        $par = $this->httpClient->send($data);

        return $this->upcReader->read($par);
    }

    public function getClientInfo(string $sessionId): \SimpleXMLElement
    {
        $builder = new UpcBuilder(UpcMessageId::MESSAGE_ID_CLIENT_INFO);

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($builder->getUpc())
            ),
            $this->requestNumber->getRequestNumber(),
            $sessionId
        );

        $par = $this->httpClient->send($data);
        $par .= $this->httpClient->poll($this->requestNumber, $sessionId);

        return $this->upcReader->read($par);
    }

    public function getSessionId(\SimpleXMLElement $xml): string
    {
        return (string) $xml->xpath('/root/U/L/A')[1];
    }

    public function getClientId(\SimpleXMLElement $xml): string
    {
        return (string) $xml->xpath('/root/U/L/A')[2];
    }
}
