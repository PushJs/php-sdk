<?php

namespace pushjs\Library\Http;

class RequestNumber
{
    private $requestNumber = 1;

    public function getRequestNumber()
    {
        $this->requestNumber += 1;
        return $this->requestNumber;
    }
}
