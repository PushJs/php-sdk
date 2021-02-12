<?php

namespace Pushjs\Library\Client;

class Client
{
    private $clientId;

    private $clientManager;

    private $attributes;

    public function __construct(ClientManager $clientManager, int $clientId, array $attributes = [])
    {
        $this->clientManager = $clientManager;
        $this->clientId = $clientId;
        $this->attributes = $attributes;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function setAttribute(string $name, $value): bool
    {
        return $this->clientManager->setAttribute($name, $value);
    }

    public function getAttribute(string $name)
    {
        return $this->attributes[$name] ?? null;
    }
}
