<?php

require "vendor/autoload.php";

$host = '192.168.56.105';
$port = 9100;
$domain = 'bijons';
$password = 'undefined';
$channelId = 'BATTLE_CHANNEL';

$key = 'eba02592-2b87-437b-b363-766cbd87230e';

$pushJS = new \pushjs\Service\PushJS($key, $host, $port, false);

$pushJS->connect();

$pushJS->getClientManager()->setAttribute('name', 'PHP');
$pushJS->getClientManager()->setAttribute('channel', 'BATTLE');

$pushJS->getChannelManager()->createChannel($channelId);
$pushJS->getChannelManager()->joinChannel($channelId, 'undefined');

$pushJS->getEventManager()->dispatchEvent($channelId, 'CHAT_MESSAGE', json_encode([
        'text' => 'BOOM!',
        'foo' => true
    ])
);

$pushJS->getEventManager()->dispatchPrivateEvent(77, 'CHAT_MESSAGE', json_encode([
        'text' => 'BOOM!',
        'foo' => true
    ])
);

$pushJS->getEventManager()->dispatchFilteredEvent(77, 'CHAT_MESSAGE', json_encode([
        'text' => 'BOOM!',
        'foo' => true
    ])
);
