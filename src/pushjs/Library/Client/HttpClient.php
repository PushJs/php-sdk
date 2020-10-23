<?php

namespace pushjs\Library\Client;

use pushjs\Exception\PhpunionplatformException;

class HttpClient implements HttpClientInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var int
     */
    private $port;

    /**
     * HttpClient constructor.
     * @param $host
     * @param $port
     * @param $domain
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param $data
     * @return mixed
     * @throws PhpunionplatformException
     */
    public function send($data): string
    {
        $socket = stream_socket_client("tcp://" . $this->host . ":" . $this->port, $errno, $errstr, 8);

        if (!$socket) {
            throw new PhpunionplatformException('Socket connection failed');
        }

        $headers = "POST / HTTP/1.1\r\n";
        $headers .= "Host: " . $this->domain . ":" . $this->port."\r\n";
        $headers .= "Content-Type: application/text/html; charset=utf8;\r\n";
        $headers .= "Content-Length: ".strlen($data)."\r\n";
        $headers .= "Connection: keep-alive\r\n";
        $headers .= "\r\n";
        $headers .= $data;
        $headers .= $this->getNullTerminateChar();

        fwrite($socket, $headers);

        $buffer = '';

        while (!feof($socket)) {
            $buffer .= fread($socket, 1024);
        }

        if (!empty($buffer)) {
            $parts = explode("\r\n\r\n", $buffer);
            return $parts[1];
        }

        return '';
    }

    /**
     * @return string
     */
    private function getNullTerminateChar()
    {
        return chr(0);
    }
}
