# pushjs.io php-sdk

## Introduction

For basic usage, register at https://pushjs.io. This client provides a basic functionality to communicate with Javascript realtime!
<br>The client can send messages in various formats (for example: JSON). 
<br><br>
Example: if a user has a browser open with the PushJS javascript client loaded, a backend PHP script can communicate with
<br>each other through our services. Yes, finally a PHP client that can communicate realtime to Javascript.

## Examples
### The basic initialization

```php
<?php

require "vendor/autoload.php";

use \PushJS\Service\PushJS;

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
