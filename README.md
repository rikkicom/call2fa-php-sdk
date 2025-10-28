# PHP SDK for Call2FA

A PHP library for Rikkicom's Call2FA service - a phone call-based two-factor authentication solution that adds an extra layer of security to your authorization pipeline.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [API Methods](#api-methods)
  - [Basic Call](#basic-call)
  - [Call with Code](#call-with-code)
  - [Call via Last Digits](#call-via-last-digits)
  - [Get Call Information](#get-call-information)
- [Callback Handling](#callback-handling)
- [Error Handling](#error-handling)
- [Examples](#examples)
- [Documentation](#documentation)

## Features

- 🔐 **Two-Factor Authentication** via phone calls
- 📞 **Multiple Call Types**: Standard calls, calls with verification codes, and last-digit verification
- 🌍 **Multi-language Support**: Ukrainian language support for voice messages and others
- 🔄 **Callback Integration**: Receive real-time updates on call status
- 📊 **Call Information Retrieval**: Query detailed information about completed calls
- ⚡ **Simple API**: Easy-to-use interface with comprehensive error handling
- 🔒 **Secure Authentication**: JWT-based API authentication

## Requirements

- PHP 7.0 or higher
- ext-json extension
- Composer for dependency management
- Valid Call2FA API credentials (login and password)

## Installation

Install the SDK via Composer:

```bash
composer require rikkicom/call2fa
```

## Quick Start

Here's a simple example to make a verification call:

```php
<?php

use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

require __DIR__ . '/vendor/autoload.php';

// API credentials
$login = 'your-api-login';
$password = 'your-api-password';

// Configuration for this call
$callTo = '+380631010121';
$callbackURL = 'https://example.com/callback';

try {
    // Create the Call2FA client
    $client = new Client($login, $password);

    // Make a call
    $result = $client->call($callTo, $callbackURL);
    
    echo "Call initiated! Call ID: " . $result['call_id'];
} catch (ClientException $e) {
    // Handle errors
    echo "Error: " . $e->getMessage();
}
```

## API Methods

### Basic Call

Initiate a standard verification call where the user presses a digit to confirm.

```php
$result = $client->call($phoneNumber, $callbackURL);

// Returns:
// [
//     'call_id' => 112
// ]
```

**Parameters:**
- `$phoneNumber` (string, required): The phone number to call (in international format, e.g., +380631010121)
- `$callbackURL` (string, optional): URL to receive callback notifications

### Call with Code

Make a call that reads a verification code to the user.

```php
$result = $client->callWithCode($phoneNumber, $code, $lang);

// Returns:
// [
//     'call_id' => 112
// ]
```

**Parameters:**
- `$phoneNumber` (string, required): The phone number to call
- `$code` (string, required): The verification code to be read (e.g., '2310')
- `$lang` (string, required): Language for voice message ('uk' for Ukrainian)

### Call via Last Digits

Initiate a call using the last digits verification method. The system provides a phone number, and the user must enter the last digits of that number as verification.

```php
// Using 4 digits (default)
$result = $client->callViaLastDigits($phoneNumber, $poolID);

// Using 6 digits
$result = $client->callViaLastDigits($phoneNumber, $poolID, true);

// Returns:
// [
//     'call_id' => 112,
//     'number' => '0443561427',
//     'code' => '1427'
// ]
```

**Parameters:**
- `$phoneNumber` (string, required): The phone number to call
- `$poolID` (string, required): The pool identifier for number selection
- `$useSixDigits` (bool, optional): Use 6 digits instead of 4 (default: false)

### Get Call Information

Retrieve detailed information about a specific call.

```php
$result = $client->info($callId);

// Returns detailed call information:
// [
//     'id' => 476247,
//     'state' => 'finished',
//     'phone_number' => '+380 XX XXX XXXX',
//     'callback_url' => '',
//     'ivr_answer' => 1,
//     'is_called' => 1,
//     'is_callback_sent' => '',
//     'is_error' => '',
//     'error_info' => '',
//     'created_at' => '2021-08-19T14:18:30.000000+0300',
//     'created_at_unix' => 1629371910,
//     'finished_at' => '2021-08-19T14:18:53.000000+0300',
//     'finished_at_unix' => 1629371933,
//     'called_at' => '2021-08-19T14:18:31.000000+0300',
//     'called_at_unix' => 1629371911,
//     'answer_at' => '2021-08-19T14:18:53.000000+0300',
//     'answer_at_unix' => 1629371933,
//     'region_code' => 'UA',
//     'phone_number_raw' => '380XXXXXXXXXX'
// ]
```

**Parameters:**
- `$callId` (string, required): The unique identifier of the call

## Callback Handling

When you provide a callback URL, Call2FA will send a POST request with call results:

```php
<?php

// Read the callback data
$json = file_get_contents('php://input');
$data = json_decode($json);

// Available fields:
// - call_id: ID of the call
// - error_info: Error details ("busy" if declined, "no_answer" if no answer, empty if successful)
// - ivr_answer: The digit pressed by the user (1-9)

if ($data !== null) {
    if (empty($data->error_info)) {
        echo "Call successful! User pressed: " . $data->ivr_answer;
    } else {
        echo "Call failed: " . $data->error_info;
    }
}
```

See the complete example in [examples/callback_example.php](examples/callback_example.php).

## Error Handling

The SDK throws `ClientException` for all errors. Always wrap your API calls in try-catch blocks:

```php
use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

try {
    $client = new Client($login, $password);
    $result = $client->call($phoneNumber, $callbackURL);
} catch (ClientException $e) {
    // Log the error
    error_log("Call2FA Error: " . $e->getMessage());
    
    // Handle the error appropriately
    // The exception message will contain details about what went wrong
}
```

**Common error scenarios:**
- Empty or invalid credentials
- Empty required parameters
- Network connectivity issues
- Invalid API responses
- Authentication failures

## Examples

The SDK includes several example files in the `examples` folder:

- [**new_call.php**](examples/new_call.php) - Basic call initiation
- [**new_call_with_code.php**](examples/new_call_with_code.php) - Call with verification code
- [**new_call_via_last_digits.php**](examples/new_call_via_last_digits.php) - Last digits verification
- [**get_call_info.php**](examples/get_call_info.php) - Retrieve call information
- [**callback_example.php**](examples/callback_example.php) - Handle callback notifications

To run an example:

```bash
cd examples
php new_call.php
```

**Note:** Remember to update the credentials in the example files before running them.

## Documentation

- [Official Documentation (English)](https://api.rikkicom.io/docs/en/call2fa/)
- [Documentation (Ukrainian)](https://api.rikkicom.io/docs/uk/call2fa/)
- [Documentation (Russian)](https://api.rikkicom.io/docs/ru/call2fa/)
