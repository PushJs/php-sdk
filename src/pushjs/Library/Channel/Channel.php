<?php

namespace PushJS\Library\Channel;

use PushJS\Library\Client\Client;

class Channel
{
    private $channelId;

    private $clients;

    private $attributes;

    private $channelManager;

    public function __construct(ChannelManager $channelManager, string $channelId, array $attributes = [])
    {
        $this->channelId = $channelId;
        $this->attributes = $attributes;
        $this->channelManager = $channelManager;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function setChannelId(string $channelId)
    {
        $this->channelId = $channelId;
    }

    public function getClients(): array
    {
        return $this->clients;
    }

    public function setClients($clients)
    {
        $this->clients = $clients;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function join(Client $client, string $password = ''): bool
    {
        $this->channelManager->joinChannel($this->channelId, $password);
        $this->clients[] = $client;

        return true;
    }
}
