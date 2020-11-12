<?php

require "vendor/autoload.php";

use \pushjs\Service\PushJS;

$host = '192.168.56.105';
$port = 9100;
$domain = 'bijons';
$password = 'undefined';
$channelId = 'BATTLE_CHANNEL';

$key = 'eba02592-2b87-437b-b363-766cbd87230e';

$pushJS = new PushJS($key, $host, $port, false);

$client = $pushJS->connect();

$pushJS->getClientManager()->setAttribute('name', 'PHP');
$pushJS->getClientManager()->setAttribute('channel', 'BATTLE');

$channel = $pushJS->getChannelManager()->createChannel($channelId);
$channel->join($client, 'undefined');

//$pushJS->getChannelManager()->joinChannel($channelId, 'undefined');

$pushJS->getEventManager()->dispatchEvent($channelId, 'CHAT_MESSAGE', json_encode([
        'text' => 'BOOM!',
        'foo' => true
    ])
);

$pushJS->getEventManager()->dispatchPrivateEvent(77, 'CHAT_MESSAGE', 'this is a private message');
$pushJS->getEventManager()->dispatchFilteredEvent($channelId, 'CHAT_MESSAGE', 'this should only go to to clients with attribute color and value red');
