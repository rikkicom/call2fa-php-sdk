# PHP SDK for Call2FA

This is a library you can use for Rikkicom's service called Call2FA (a call as the second factor in an authorization pipeline)

## Installation

Just install from Packagist:

```
composer require rikkicom/call2fa
```

## Example

This simple code makes a new call to the +380631010121 number:

```php
<?php

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials
$login = '***';
$password = '***';

// Configuration for this call
$callTo = '+380631010121';
$callbackURL = 'http://example.com';

try {
    // Create the Call2FA client
    $client = new Client($login, $password);

    // Make a call
    $client->call($callTo, $callbackURL);

    echo "Wait the call!";
} catch (ClientException $e) {
    // Something went wrong, we recommend to log the error
    echo "Error:";
    echo "\n";
    echo $e;
}
```

More examples are in the `examples` folder.

- Documentation: https://api.rikkicom.io/docs/en/call2fa/
- Documentation (in Russian): https://api.rikkicom.io/docs/ru/call2fa/
