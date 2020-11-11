<?php

namespace pushjs\Library\Upcreader;

use pushjs\Library\Http\ConnectionManager;

class UpcReader
{
    public function __construct()
    {
    }

    public function read(string $par): \SimpleXMLElement
    {
        $par = "<root>" . trim($par, ConnectionManager::getNullTerminateChar()) . "</root>";

        $xml = simplexml_load_string($par, \SimpleXMLElement::class);

        if (empty($xml)) {
            throw new \Exception('Invalid xml given');
        }

        return $xml;
    }
}
