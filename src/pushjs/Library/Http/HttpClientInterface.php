<?php

namespace pushjs\Library\Http;

interface HttpClientInterface
{
    public function send(string $data);
}
