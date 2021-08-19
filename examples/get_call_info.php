<?php

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials
$login = '***';
$password = '***';

// An ID of the call
$id = '476247';

try {
    // Create the Call2FA client
    $client = new Client($login, $password);

    // Get call info
    $result = $client->info($id);

    print_r($result);

    // Result looks like the following:

    /*

    (
        [id] => 476247
        [state] => finished
        [phone_number] => +380 XX XXX XXXX
        [callback_url] =>
        [ivr_answer] => 1
        [is_called] => 1
        [is_callback_sent] =>
        [is_error] =>
        [error_info] =>
        [created_at] => 2021-08-19T14:18:30.000000+0300
        [created_at_unix] => 1629371910
        [finished_at] => 2021-08-19T14:18:53.000000+0300
        [finished_at_unix] => 1629371933
        [called_at] => 2021-08-19T14:18:31.000000+0300
        [called_at_unix] => 1629371911
        [answer_at] => 2021-08-19T14:18:53.000000+0300
        [answer_at_unix] => 1629371933
        [region_code] => UA
        [phone_number_raw] => 380XXXXXXXXXX
    )

    */
} catch (ClientException $e) {
    // Something went wrong, we recommend to log the error
    echo "Error:";
    echo "\n";
    echo $e;
}
