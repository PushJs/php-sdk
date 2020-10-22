<?php

namespace pushjs\Service;

use pushjs\Exception\PhpunionplatformException;
use pushjs\Library\Client\HttpClientInterface;
use pushjs\Library\Querybuilder\HttpQueryBuilder;
use pushjs\Library\Enum\UpcHttpRequestMode;
use pushjs\Library\Enum\UpcHttpRequestParam;
use pushjs\Library\Enum\UpcMessageId;
use pushjs\Library\Upcbuilder\UpcBuilder;
use pushjs\Library\Upcreader\UpcReader;

class Unionplatform
{
    /**
     * @var string
     */
    private $sessionId;

    private $clientId;

    /**
     * @var int
     */
    private $requestNumber = 1;

    private $upcReader;

    private $httpClient;

    private $querybuilder;

    /**
     * UnionplatformClient constructor.
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->upcReader = new UpcReader();
        $this->httpClient = $client;
        $this->querybuilder = new HttpQueryBuilder();
    }

    public function connect(string $domain, string $key, bool $secure): bool
    {
        $this->handshake();
        $success = $this->setApiKey($key);

        if ($success) {
            echo 'CONNECTED';
        }

        return true;
    }

    public function setApiKey(string $key): bool
    {
        return $this->setClientAttribute('apikey', $key);
    }

    public function setClientAttribute(string $name, string $value): bool
    {
        $upc = new UpcBuilder(UpcMessageId::SET_CLIENT_ATTR);
        $upc->addArgument($this->clientId);
        $upc->addArgument('');
        $upc->addArgument($name);
        $upc->addArgument($value);

        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->getRequestNumber(),
            $this->sessionId
        );

        $this->httpClient->send($data);

        $upc = $this->poll();

        $xml = $this->upcReader->read($upc);

        return (string) $xml->xpath('/root/U/L/A')[5] === 'SUCCESS';
    }
    /**
     * This is the handshake as defined in the
     * documentation. send a 65 CLIENT HELLO
     *
     * @return string
     * @throws PhpunionplatformException
     */
    public function handshake()
    {
        $userAgent = ''
            . 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:47.0)'
            . 'Gecko/20100101 Firefox/47.0;2.1.1 (Build 856)';

        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_CLIENT_HELLO);

        $upc->addArgument('Orbiter');
        $upc->addArgument($userAgent);
        $upc->addArgument('1.10.3');

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND_RECEIVE,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->getRequestNumber()
        );

        try {
            $upc = $this->httpClient->send($data);
        } catch (\Exception $e) {
            return false;
        }

        // send through http client
        $xml = $this->upcReader->read($upc);

        $this->sessionId = (string) $xml->xpath('/root/U/L/A')[1];

        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_CLIENT_INFO);

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->getRequestNumber(),
            $this->sessionId
        );

        $upc = $this->httpClient->send($data);
        $upc .= $this->poll();

        $xml = $this->upcReader->read(
            $upc
        );

        $this->clientId = (string) $xml->xpath('/root/U/L/A')[2];

        return $upc;
    }

    /**
     * Create a room
     *
     * @param string $roomId
     * @return array
     * @throws PhpunionplatformException
     */
    public function createRoom($roomId)
    {
        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_CREATE_ROOM);
        $upc->addArgument($roomId);
        return $this->sendUpc($upc);
    }

    /**
     * Join a room
     *
     * @param string $roomId
     * @param string $password
     * @return array
     * @throws PhpunionplatformException
     */
    public function joinRoom($roomId, $password = '')
    {
        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_JOIN_ROOM);
        $upc->addArgument($roomId);
        return $this->sendUpc($upc);
    }

    /**
     * Send a message to a specific
     * user in a room
     *
     * @todo fix the includeSelf param, this causes an exception on the server
     *
     * @param string $roomId
     * @param string $message
     * @param bool $includeSelf
     * @param array $filters
     * @param array $params
     * @return array
     * @internal param int $userId
     */
    public function sendMessage(
        $roomId,
        $message,
        $includeSelf = false,
        array $filters = array(),
        array $params = array()
    ) {
        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_SEND_MESSAGE_TO_ROOMS);

        $upc->addArgument($message);
        $upc->addArgument($roomId);
        $upc->addArgument(($includeSelf) ? 'true' : 'false');
        $upc->addFilters($filters);

        if (count($params) > 0) {
            foreach ($params as $param) {
                $upc->addArgument($param);
            }
        }

        return $this->sendUpc($upc);
    }


    public function poll(): string
    {
        if (empty($this->sessionId)) {
            throw new PhpunionplatformException('Session id can not be empty');
        }

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_RECEIVE,
            [],
            $this->getRequestNumber(),
            $this->sessionId ?? ''
        );

        return $this->httpClient->send($data);
    }

    /**
     * Increment sequential for messaging
     * @return int
     */
    private function getRequestNumber()
    {
        $this->requestNumber += 1;
        return $this->requestNumber;
    }

    private function sendUpc(UpcBuilder $upc)
    {
        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_REQUEST_ID   => $this->getRequestNumber(),
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_SESSION_ID   => $this->sessionId,
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA         => $upc->getUpc()
            )
        );

        try {
            $upc = $this->httpClient->send($data);
        } catch (PhpunionplatformException $e) {
            return false;
        }

        return $this->upcReader->readUpc(
            $upc
        );
    }

    public static function getNullTerminateChar()
    {
        return chr(0);
    }
}
