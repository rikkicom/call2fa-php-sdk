<?php

/**
 * Call via Last Digits Example
 * 
 * This example demonstrates the last digits verification method. In this approach:
 *   1. The system calls the user from a specific phone number
 *   2. The user sees the incoming number on their phone
 *   3. The user must enter the last digits of that incoming number as verification
 * 
 * This method is useful when you want to verify that the user actually received
 * the call and saw the caller ID.
 * 
 * Usage:
 *   1. Update the $login and $password with your Call2FA API credentials
 *   2. Set the phone number to call in international format
 *   3. Set the pool ID (identifies which number pool to use)
 *   4. Choose between 4-digit or 6-digit verification
 *   5. Run: php new_call_via_last_digits.php
 */

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials - replace with your actual credentials from Call2FA dashboard
$login = '***';
$password = '***';

// Phone number to call (must be in international format with country code)
$callTo = '+380631010121';

// Pool ID identifies which number pool to use for the call
// You can obtain pool IDs from your Call2FA dashboard
$poolID = '4';

try {
    // Step 1: Initialize the Call2FA client with your credentials
    // This will automatically authenticate and obtain a JWT token
    $client = new Client($login, $password);

    // Step 2: Initiate the call using last digits verification
    
    // Option 1: Use 4-digit verification (default)
    // The user will need to enter the last 4 digits of the incoming phone number
    $result = $client->callViaLastDigits($callTo, $poolID);
    
    // Option 2: Use 6-digit verification for enhanced security
    // Uncomment the line below to require the last 6 digits instead
    // $result = $client->callViaLastDigits($callTo, $poolID, true);

    // Step 3: Display the call result
    print_r($result);

    // The result array contains:
    // - call_id: Unique identifier for this call
    // - number: The phone number that will call the user (this is what they'll see on caller ID)
    // - code: The last digits that the user should enter for verification
    /*
    Example result:
    Array
    (
        [call_id] => 112
        [number] => 0443561427
        [code] => 1427
    )
    */

    echo "Call initiated successfully! The user should enter code: " . $result['code'];
} catch (ClientException $e) {
    // Handle any errors that occur during the call process
    // Common errors: invalid credentials, invalid pool ID, empty parameters
    echo "Error occurred:";
    echo "\n";
    echo $e;
}
