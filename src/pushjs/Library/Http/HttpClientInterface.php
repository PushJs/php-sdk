<?php

namespace Pushjs\Library\Http;

interface HttpClientInterface
{
    public function send(string $data);

    public function poll(int $requestNumber, string $sessionId): string;
}
