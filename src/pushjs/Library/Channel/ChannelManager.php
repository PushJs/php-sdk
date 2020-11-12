<?php

namespace pushjs\Library\Channel;

use pushjs\Library\Enum\UpcHttpRequestMode;
use pushjs\Library\Enum\UpcHttpRequestParam;
use pushjs\Library\Enum\UpcMessageId;
use pushjs\Library\Http\ConnectionManager;
use pushjs\Library\Querybuilder\HttpQueryBuilder;
use pushjs\Library\Upcbuilder\UpcBuilder;

class ChannelManager
{
    private $connectionManager;

    private $queryBuilder;

    private $channels;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        $this->queryBuilder = new HttpQueryBuilder();
    }

    public function createChannel(string $channelId, array $attributes = []): Channel
    {
        $upc = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);
        $upc->addArgument('PushJS');
        $upc->addArgument("CREATE_CHANNEL");
        $upc->addArgument("name|" . $channelId);

        foreach ($attributes as $key => $value) {
            $upc->addArgument($key . "|" . $value);
        }

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);

        $channel = new Channel($this, $channelId, $attributes);
        $this->channels[] = $channel;

        return $channel;
    }

    public function joinChannel(string $channelId, string $password = '')
    {
        $upc = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);
        $upc->addArgument('PushJS');
        $upc->addArgument("JOIN_CHANNEL");
        $upc->addArgument("channelId|" . $channelId);

        if (!empty($password)) {
            $upc->addArgument("password|" . $password);
        }

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);
    }
}
