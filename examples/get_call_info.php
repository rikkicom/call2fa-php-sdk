<?php

/**
 * Get Call Information Example
 * 
 * This example demonstrates how to retrieve detailed information about a specific call
 * using its call ID. This is useful for:
 *   - Checking the current status of a call
 *   - Getting the result of a completed call
 *   - Retrieving timestamps for when the call was created, answered, and finished
 *   - Understanding any errors that occurred during the call
 * 
 * Usage:
 *   1. Update the $login and $password with your Call2FA API credentials
 *   2. Set the $id to the call_id you want to query (obtained from previous call results)
 *   3. Run: php get_call_info.php
 */

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// API credentials - replace with your actual credentials from Call2FA dashboard
$login = '***';
$password = '***';

// The unique identifier of the call you want to retrieve information about
// This ID is returned when you initiate a call using any of the call methods
$id = '476247';

try {
    // Step 1: Initialize the Call2FA client with your credentials
    // This will automatically authenticate and obtain a JWT token
    $client = new Client($login, $password);

    // Step 2: Retrieve information about the specific call
    $result = $client->info($id);

    // Step 3: Display the complete call information
    print_r($result);

    // The result array contains comprehensive call details:
    // - id: The call identifier
    // - state: Current state of the call (e.g., 'finished', 'in_progress', 'failed')
    // - phone_number: The phone number that was called (partially masked for privacy)
    // - callback_url: The callback URL that was provided (if any)
    // - ivr_answer: The digit pressed by the user (1-9) if the call was answered
    // - is_called: Whether the call was successfully placed (1 = yes, 0 = no)
    // - is_callback_sent: Whether a callback was sent (if callback URL was provided)
    // - is_error: Whether an error occurred during the call
    // - error_info: Details about any error ('busy', 'no_answer', or empty if no error)
    // - created_at: When the call was initiated (ISO 8601 format)
    // - created_at_unix: Unix timestamp of creation
    // - finished_at: When the call completed (ISO 8601 format)
    // - finished_at_unix: Unix timestamp of completion
    // - called_at: When the call was placed (ISO 8601 format)
    // - called_at_unix: Unix timestamp when placed
    // - answer_at: When the call was answered (ISO 8601 format)
    // - answer_at_unix: Unix timestamp when answered
    // - region_code: Country code of the phone number (e.g., 'UA' for Ukraine)
    // - phone_number_raw: The complete unmasked phone number
    
    /*
    Example result:
    Array
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
    // Handle any errors that occur while retrieving call information
    // Common errors: invalid call ID, expired call data, authentication issues
    echo "Error occurred:";
    echo "\n";
    echo $e;
}
