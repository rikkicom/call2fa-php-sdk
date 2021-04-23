<?php

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials
$login = '';
$password = '';

// Configuration for this call
$callTo = '+380631010121';
$code = '2310w';
$lang = 'uk'; // uk or ru

try {
    // Create the Call2FA client
    $client = new Client($login, $password);

    // Make a call
    $result = $client->callWithCode($callTo, $code, $lang);

    print_r($result);

    // Result looks like the following:

    /*

    (
        [call_id] => 112
    )

    */

    echo "Wait the call!";
} catch (ClientException $e) {
    // Something went wrong, we recommend to log the error
    echo "Error:";
    echo "\n";
    echo $e;
}
