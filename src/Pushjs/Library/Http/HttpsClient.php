<?php

namespace Pushjs\Library\Http;

use Pushjs\Exception\PushjsException;
use Pushjs\Library\Enum\UpcHttpRequestMode;
use Pushjs\Library\Querybuilder\HttpQueryBuilder;

class HttpsClient implements HttpClientInterface
{
    private $host;

    private $port;

    private $secure;

    private $domain;

    private $timeout = 5;

    public function __construct(string $host, int $port, bool $secure)
    {
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
    }

    public function send(string $data): string
    {
        try {
            $socket = $this->getSocket();
        } catch (\Throwable $e) {
            echo 'connection failed';
            exit;
        }

        $headers = "POST / HTTP/1.1\r\n";
        $headers .= "Host: ".$this->domain.":".$this->port."\r\n";
        $headers .= "Content-Type: application/text/html; charset=utf8;\r\n";
        $headers .= "Content-Length: ".strlen($data)."\r\n";
        $headers .= "Connection: close\r\n";
        $headers .= "\r\n";
        $headers .= $data;
        $headers .= ConnectionManager::getNullTerminateChar();

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

    public function poll(int $requestNumber, string $sessionId): string
    {
        if (empty($sessionId)) {
            throw new PushjsException('Session id can not be empty');
        }

        $builder = new HttpQueryBuilder();

        $data = $builder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_RECEIVE,
            [],
            $requestNumber,
            $sessionId
        );

        return $this->send($data);
    }

    private function getSocket()
    {
        $socket = @stream_socket_client(
            "tcp://" . $this->host . ":" . $this->port,
            $errorNr,
            $errorStr,
            $this->timeout,
            STREAM_CLIENT_CONNECT
        );

        if (empty($socket)) {
            throw new PushjsException('Connection timeout');
        }

        stream_set_timeout($socket, $this->timeout);
        stream_set_blocking($socket, true);

        if ($this->secure) {
            $success = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            if ($success === false) {
                throw new PushjsException('SSL negotiation failed');
            }
        }

        stream_set_blocking($socket, false);

        return $socket;
    }
}
