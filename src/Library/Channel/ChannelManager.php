<?php

namespace Pushjs\Library\Channel;

use Pushjs\Library\Enum\UpcHttpRequestMode;
use Pushjs\Library\Enum\UpcHttpRequestParam;
use Pushjs\Library\Enum\UpcMessageId;
use Pushjs\Library\Http\ConnectionManager;
use Pushjs\Library\Http\RequestNumber;
use Pushjs\Library\Querybuilder\HttpQueryBuilder;
use Pushjs\Library\Upcbuilder\UpcBuilder;
use Pushjs\Library\Upcreader\UpcReader;

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

    public function joinChannel(string $channelId, string $password = ''): bool
    {
        $builder = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);
        $builder->addArgument('PushJS');
        $builder->addArgument("JOIN_CHANNEL");
        $builder->addArgument("channelId|" . $channelId);

        if (!empty($password)) {
            $builder->addArgument("password|" . $password);
        }

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($builder->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);
        $pip = $this->connectionManager->getHttpClient()->poll($this->connectionManager->getRequestNumber(), $this->connectionManager->getSessionId());
echo "---- pip ---- \n";

        $xml = (new UpcReader())->read($pip);
var_dump($xml->xpath('/root/U')[1]->xpath('/L'));
        foreach ($xml->xpath('/root/U')[1]->xpath('/L/A') as $client) {
            echo (string) $client;
        }
        return true;
    }

    public function getClients(string $channelId)
    {
        $builder = new UpcBuilder(UpcMessageId::GET_CHANNEL_CLIENTS);
        $builder->addArgument('_eba02592-2b87-437b-b363-766cbd87230e_' . $channelId);

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($builder->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);

        $pip = $this->connectionManager->getHttpClient()->poll(
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        var_dump($pip);


        $xml = (new UpcReader())->read($pip);

        var_dump($xml);
    }
}
