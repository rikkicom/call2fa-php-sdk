<?php

/**
 * Basic Call Example
 * 
 * This example demonstrates how to initiate a standard verification call.
 * The user will receive a call and be prompted to press a digit (1-9) to confirm.
 * 
 * Usage:
 *   1. Update the $login and $password with your Call2FA API credentials
 *   2. Set the phone number in international format (e.g., +380631010121)
 *   3. Optionally set a callback URL to receive call results
 *   4. Run: php new_call.php
 */

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials - replace with your actual credentials from Call2FA dashboard
$login = '***';
$password = '***';

// Phone number to call (must be in international format with country code)
$callTo = '+380631010121';

// Callback URL where Call2FA will send the call results (optional)
// The callback will include: call_id, error_info, and ivr_answer
$callbackURL = 'http://example.com';

try {
    // Step 1: Initialize the Call2FA client with your credentials
    // This will automatically authenticate and obtain a JWT token
    $client = new Client($login, $password);

    // Step 2: Initiate the verification call
    // The system will call the phone number and prompt the user to press a digit
    $result = $client->call($callTo, $callbackURL);

    // Step 3: Display the call result
    print_r($result);

    // The result array contains:
    // - call_id: Unique identifier for this call (used to track status or retrieve info)
    /*
    Example result:
    Array
    (
        [call_id] => 112
    )
    */

    echo "Call initiated successfully! Wait for the call to be answered.";
} catch (ClientException $e) {
    // Handle any errors that occur during the call process
    // Common errors: invalid credentials, empty parameters, network issues
    echo "Error occurred:";
    echo "\n";
    echo $e;
}
