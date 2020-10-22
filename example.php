<?php

require "vendor/autoload.php";

use pushjs\service\Unionplatform;
use pushjs\library\client\HttpsClient;
use pushjs\library\client\HttpClient;

$host = '192.168.56.105';
$port = 9100;
$domain = 'example.com';

$room = 'systemChannel';

$key = '1111';

// create new http interface
$httpClient = new HttpClient($host, $port);

// create
$unionplatformService = new Unionplatform(
    $httpClient
);

// connect, shake hands and say hello
$unionplatformService->connect($domain, $key, false);


exit;
// create a room
$unionplatformService->createRoom($room);

// join it
$unionplatformService->joinRoom($room);

// send a room message, if the userid is set it will send only to that user
// the last two parameters (userId, params[]) are optional
$unionplatformService->sendMessage($room, 'NOTIFY', 17, array('hello my friend!'));
