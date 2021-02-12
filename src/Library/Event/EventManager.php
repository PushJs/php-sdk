<?php

namespace Pushjs\Library\Event;

use Pushjs\Library\Enum\UpcHttpRequestMode;
use Pushjs\Library\Enum\UpcHttpRequestParam;
use Pushjs\Library\Enum\UpcMessageId;
use Pushjs\Library\Http\ConnectionManager;
use Pushjs\Library\Querybuilder\HttpQueryBuilder;
use Pushjs\Library\Upcbuilder\UpcBuilder;

class EventManager
{
    private $connectionManager;

    private $queryBuilder;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        $this->queryBuilder = new HttpQueryBuilder();
    }

    public function dispatchEvent(string $channelId, string $event, string $message)
    {
        $builder = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);

        $builder->addArgument('PushJS');
        $builder->addArgument("SEND_MESSAGE");
        $builder->addArgument("channelId|" . $channelId);
        $builder->addArgument("message|"  . $message);
        $builder->addArgument("event|"  . $event);

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($builder->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);
    }

    public function dispatchPrivateEvent(int $clientId, string $event, string $message)
    {
        $builder = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);

        $builder->addArgument('PushJS');
        $builder->addArgument("PRIVATE_MESSAGE");
        $builder->addArgument("clientId|" . $clientId);
        $builder->addArgument("message|"  . $message);
        $builder->addArgument("event|"  . $event);

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($builder->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);
    }

    public function dispatchFilteredEvent(string $channelId, string $event, string $message, array $filters)
    {
        $builder = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);

        $builder->addArgument('PushJS');
        $builder->addArgument("FILTERED_MESSAGE");
        $builder->addArgument("filters|" . json_encode($filters));
        $builder->addArgument("channelId|" . $channelId);
        $builder->addArgument("message|"  . $message);
        $builder->addArgument("event|"  . $event);

        $data = $this->queryBuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($builder->getUpc())
            ),
            $this->connectionManager->getRequestNumber(),
            $this->connectionManager->getSessionId()
        );

        $this->connectionManager->getHttpClient()->send($data);
    }
}
