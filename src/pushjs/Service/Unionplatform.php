<?php

namespace PushJS\Service;

use PushJS\Exception\PhpunionplatformException;
use PushJS\Library\Client\HttpClientInterface;
use PushJS\Library\Querybuilder\HttpQueryBuilder;
use PushJS\Library\Enum\UpcHttpRequestMode;
use PushJS\Library\Enum\UpcHttpRequestParam;
use PushJS\Library\Enum\UpcMessageId;
use PushJS\Library\Upcbuilder\UpcBuilder;
use PushJS\Library\Upcreader\UpcReader;

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
        $success = $this->handshake();

        if (!$success) {
            return false;
        }

        $success = $this->setApiKey($key);

        if ($success) {
            echo "CONNECTED\n";
        }

        return true;
    }

    public function setApiKey(string $key): bool
    {
        return $this->setAnonymousClientAttribute('apikey', $key);
    }

    public function setAnonymousClientAttribute(string $name, string $value)
    {
        $upc = new UpcBuilder(UpcMessageId::SET_CLIENT_ATTR);
        $upc->addArgument($this->clientId);
        $upc->addArgument('');
        $upc->addArgument($name);
        $upc->addArgument($value);
        $upc->addArgument('');
        $upc->addArgument(4);

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

    public function setClientAttribute(string $name, string $value): bool
    {
        $upc = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);
        $upc->addArgument('PushJS');
        $upc->addArgument("SET_ATTRIBUTE");
        $upc->addArgument("name|" . $name);
        $upc->addArgument("value|"  .$value);


        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->getRequestNumber(),
            $this->sessionId
        );

        $this->httpClient->send($data);

        return true;

    }
    /**
     * This is the handshake as defined in the
     * documentation. send a 65 CLIENT HELLO
     *
     * @return string
     * @throws PhpunionplatformException
     */
    public function handshake(): bool
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

        return true;
    }


    public function createRoom($roomId)
    {
        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_CREATE_ROOM);
        $upc->addArgument($roomId);

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

        //return (string) $xml->xpath('/root/U/L/A')[5] === 'SUCCESS';
    }



    public function joinRoom(string $channelId, string $password)
    {


        $upc = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);
        $upc->addArgument('PushJS');
        $upc->addArgument("JOIN_CHANNEL");
        $upc->addArgument("channelId|" . $channelId);
        $upc->addArgument("password|" . $password);


        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->getRequestNumber(),
            $this->sessionId
        );

        $this->httpClient->send($data);
    }

    public function sendMessage(string $channelId, string $event, string $message) {

        $upc = new UpcBuilder(UpcMessageId::SEND_MODULE_MESSAGE);
        $upc->addArgument('PushJS');
        $upc->addArgument("SEND_MESSAGE");
        $upc->addArgument("channelId|" . $channelId);
        $upc->addArgument("message|"  . $message);
        $upc->addArgument("event|"  . $event);

        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
            ),
            $this->getRequestNumber(),
            $this->sessionId
        );

        $this->httpClient->send($data);

        return true;

    }

    public function longpoll()
    {
        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_RECEIVE,
            [],
            $this->getRequestNumber(),
            $this->sessionId ?? ''
        );

        return $this->httpClient->send($data);
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

    public static function getNullTerminateChar()
    {
        return chr(0);
    }
}
