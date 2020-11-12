<?php

namespace pushjs\Library\Client;

class Client
{
    private $id;

    private $attributes;

    public function __construct(int $id, array $attributes = [])
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }
}
