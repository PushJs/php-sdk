<?php

namespace pushjs\Library\Upcreader;

use pushjs\Service\Unionplatform;

class UpcReader
{
    public function __construct()
    {
    }

    /**
     * @param $serverUpc
     * @return array
     */
    public function readUpc2($serverUpc)
    {
        // wrap around a 'root' for easy reading through simplexml
        $xml = simplexml_load_string(trim('<root>'.$serverUpc.'</root>'));

        $upc = array(
            'responseNumber' => (int) preg_replace("/[^0-9]/", "", $xml->U->M),
            'responseValues' => array()
        );

        if (!empty($xml->U->L->A)) {
            foreach ($xml->U->L->A as $argument) {
                $upc['responseValues'][] = (string)$argument;
            }
        }

        return $upc;
    }

    public function read(string $pxp): \SimpleXMLElement
    {
        $pxp = str_replace(Unionplatform::getNullTerminateChar(), "", $pxp);
        $pxp = "<root>" . ($pxp) . "</root>";

        $xml = simplexml_load_string($pxp, \SimpleXMLElement::class);

        if (empty($xml)) {
            throw new \Exception('Invalid xml given');
        }

        return $xml;
    }
}
