<?php

require "vendor/autoload.php";

use pushjs\service\Unionplatform;
use pushjs\library\client\HttpsClient;
use pushjs\library\client\HttpClient;

$host = '192.168.56.105';
$port = 9100;
$domain = 'bijons';
$password = 'undefined';
$channelId = 'BATTLE_CHANNEL';

$key = 'eba02592-2b87-437b-b363-766cbd87230e';

$client = new \pushjs\Service\PushJS($key, $host, $port, false);

$client->connect();

exit;

// connect, shake hands and say hello
$connected = $unionplatformService->connect($domain, $key, false);

if (!$connected) {
    echo 'can not connect to ' . $host . "\n";
    exit;
}

$unionplatformService->setClientAttribute('name', 'PHP');
$unionplatformService->setClientAttribute('id', 2);
$unionplatformService->setClientAttribute('channel', 'BATTLE');

$json = json_encode([
    'text' => 'BOOM!',
    'userId' => 2
]);

$unionplatformService->joinRoom($channelId, $password);
$unionplatformService->sendMessage($channelId, 'CHAT_MESSAGE', $json);



$i  = 0;
while ($i < 20) {
    $i++;
    $unionplatformService->longpoll();
    usleep(250000);
    flush();
}



exit;


// send a room message, if the userid is set it will send only to that user
// the last two parameters (userId, params[]) are optional
$unionplatformService->sendMessage($room, 'NOTIFY', 17, array('hello my friend!'));
