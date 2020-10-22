<?php

namespace pushjs\Library\Client;

interface HttpClientInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function send($data);
}