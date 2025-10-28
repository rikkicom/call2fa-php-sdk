<?php

/**
 * Call with Code Example
 * 
 * This example demonstrates how to initiate a call that reads a verification code
 * to the user. The system will call the phone number and read the code digit by digit
 * in the specified language.
 * 
 * Use Case:
 *   Perfect for scenarios where you generate a code and need to deliver it via voice.
 *   The user doesn't need to press any buttons - they just listen to the code.
 * 
 * Usage:
 *   1. Update the $login and $password with your Call2FA API credentials
 *   2. Set the phone number in international format
 *   3. Generate or set the verification code you want to read
 *   4. Choose the language ('uk' for Ukrainian, 'ru' for Russian)
 *   5. Run: php new_call_with_code.php
 */

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials - replace with your actual credentials from Call2FA dashboard
$login = '';
$password = '';

// Phone number to call (must be in international format with country code)
$callTo = '+380631010121';

// Verification code to be read to the user
// Can contain digits and letters (e.g., '2310', '2310w', 'A1B2')
$code = '2310w';

// Language for the voice message
// Supported languages: 'uk' (Ukrainian), 'ru' (Russian)
$lang = 'uk';

try {
    // Step 1: Initialize the Call2FA client with your credentials
    // This will automatically authenticate and obtain a JWT token
    $client = new Client($login, $password);

    // Step 2: Initiate the call with the code
    // The system will call the number and read the code in the specified language
    $result = $client->callWithCode($callTo, $code, $lang);

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

    echo "Call with code initiated successfully! The user will hear the code: " . $code;
} catch (ClientException $e) {
    // Handle any errors that occur during the call process
    // Common errors: invalid credentials, empty parameters, unsupported language
    echo "Error occurred:";
    echo "\n";
    echo $e;
}
