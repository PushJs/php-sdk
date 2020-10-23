<?php

require "vendor/autoload.php";

use pushjs\service\Unionplatform;
use pushjs\library\client\HttpsClient;
use pushjs\library\client\HttpClient;

$host = '192.168.56.105';
$port = 9100;
$domain = '192.168.56.105';

$room = 'BATTLE_CHANNEL';

$key = 'eba02592-2b87-437b-b363-766cbd87230e';
//$key = '1111';

// create new http interface
$httpClient = new HttpClient($host, $port);

// create
$unionplatformService = new Unionplatform(
    $httpClient
);

// connect, shake hands and say hello
$unionplatformService->connect($domain, $key, false);
$unionplatformService->setClientAttribute('name', 'php client');
$unionplatformService->setClientAttribute('id', 2);
$unionplatformService->setClientAttribute('channel', 'BATTLE');

$json = json_encode([
    'text' => 'BOOM!',
    'userId' => 2
]);

$unionplatformService->joinRoom($room);
$unionplatformService->sendMessage($room, $json, true, [], ["henlo"]);

$i  = 0;
while ($i < 20) {
    $i++;
    $unionplatformService->longpoll();
    usleep(250000);
}



exit;


// send a room message, if the userid is set it will send only to that user
// the last two parameters (userId, params[]) are optional
$unionplatformService->sendMessage($room, 'NOTIFY', 17, array('hello my friend!'));
