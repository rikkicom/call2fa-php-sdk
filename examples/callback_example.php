<?php

/**
 * Callback Handler Example
 * 
 * This example demonstrates how to handle callback notifications from Call2FA.
 * When you initiate a call with a callback URL, Call2FA will send a POST request
 * to that URL with the call results once the call is completed.
 * 
 * How it works:
 *   1. You provide a callback URL when initiating a call
 *   2. Call2FA makes the phone call
 *   3. After the call completes (answered, declined, or no answer), Call2FA sends
 *      a POST request with JSON data to your callback URL
 *   4. This script receives and processes that callback data
 * 
 * Deployment:
 *   1. Deploy this script to a publicly accessible web server
 *   2. Ensure the URL is accessible over HTTPS (recommended for security)
 *   3. Use this URL as the $callbackURL parameter when making calls
 *   4. Implement your business logic to handle different call outcomes
 * 
 * Example URL: https://yourdomain.com/callback_handler.php
 */

// Read the JSON data from the POST request body
// Call2FA sends the callback data as raw JSON in the request body
$json = file_get_contents('php://input');

// Decode the JSON into a PHP object
$data = json_decode($json);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    // Invalid JSON received - log the error and return error response
    error_log('Call2FA callback: Invalid JSON received - ' . json_last_error_msg());
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// The $data object contains three important fields:
//
// 1. call_id (string/int): 
//    - The unique identifier of the call
//    - Use this to match the callback with your original call request
//
// 2. error_info (string):
//    - Empty string: Call was successful, user answered and pressed a digit
//    - "busy": User declined the call
//    - "no_answer": User did not answer the call within the timeout period
//
// 3. ivr_answer (string/int):
//    - The digit pressed by the user (1-9) during the call
//    - Only present if the call was successful and user pressed a digit
//    - Use this to verify user interaction

/*
Example callback data for a successful call:
{
    "call_id": 112,
    "error_info": "",
    "ivr_answer": 1
}

Example callback data for a declined call:
{
    "call_id": 113,
    "error_info": "busy",
    "ivr_answer": null
}

Example callback data for no answer:
{
    "call_id": 114,
    "error_info": "no_answer",
    "ivr_answer": null
}
*/

// Display the received data (for debugging purposes)
// In production, you should log this data and implement your business logic
var_dump($data);

// Example of processing the callback data:
if ($data !== null) {
    // Extract the call ID for reference
    $callId = $data->call_id;
    
    // Check if the call was successful
    if (empty($data->error_info)) {
        // Call was successful - the user answered and pressed a digit
        $pressedDigit = $data->ivr_answer;
        
        // Add your success logic here:
        // - Mark verification as complete in your database
        // - Grant access to the user
        // - Send confirmation email/SMS
        // - Log the successful verification
        
        // Example:
        // updateUserVerificationStatus($callId, 'verified', $pressedDigit);
        
    } else {
        // Call failed - handle the error
        $errorReason = $data->error_info;
        
        if ($errorReason === 'busy') {
            // User declined the call
            // Add your logic here:
            // - Retry the call after some time
            // - Offer alternative verification methods
            // - Notify the user via another channel
            
        } elseif ($errorReason === 'no_answer') {
            // User did not answer within the timeout
            // Add your logic here:
            // - Schedule a retry
            // - Send SMS/email notification
            // - Mark as pending verification
        }
        
        // Log the failed verification
        // updateUserVerificationStatus($callId, 'failed', $errorReason);
    }
}

// Return a success response to Call2FA
// It's good practice to respond with HTTP 200 to acknowledge receipt
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['status' => 'received']);
