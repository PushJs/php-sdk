<?php

namespace Pushjs\Library\Upcreader;

use Pushjs\Library\Http\ConnectionManager;

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
