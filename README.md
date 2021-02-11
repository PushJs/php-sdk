# pushjs.io php-sdk

## Introduction

For basic usage, register at https://pushjs.io.

## Examples
### The basic initialization

```php
<?php

require "vendor/autoload.php";

use \pushjs\Service\PushJS;

$pushJS = new PushJS('your-api-key', 'eu-1.pushjs.io', 9101);

```

### Dispatch event

Dispatching events in a channel means that every client in that particular channel will receive the event.

```php
$pushJS->getEventManager()->dispatchEvent($channelId, 'MY_EVENT', json_encode([
        'text' => 'BOOM!',
        'foo' => 'bar'
    ])
);
```

### Dispatch a private event

This event will only reach the client with a specific id given in the `$clientId`.

```php
$pushJS->getEventManager()->dispatchPrivateEvent(
    $clientId, 
    'MY_PRIVATE_MESSAGE_EVENT', 
    'this is a private message'
);
```

### Dispatch a filtered event

This event will only reach users with the attribute "red" and if it has the value "red".

```php
$pushJS->getEventManager()->dispatchFilteredEvent(
    $channelId, 
    'MY_FILTERED_MESSAGE_EVENT', 
    'this should only go to to clients with attribute color and value red',
    ['color' => 'red']
);
```
