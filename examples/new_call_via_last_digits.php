<?php

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials
$login = '***';
$password = '***';

// Configuration for this call
$callTo = '+380631010121';
$poolID = '4';

try {
    // Create the Call2FA client
    $client = new Client($login, $password);

    // Make a call
    $result = $client->callViaLastDigits($callTo, $poolID);

    print_r($result);

    // Result looks like the following:

    /*

    (
        [call_id] => 112
        [number] => 0443561427
        [code] => 1427
    )

    */

    echo "Wait the call!";
} catch (ClientException $e) {
    // Something went wrong, we recommend to log the error
    echo "Error:";
    echo "\n";
    echo $e;
}
