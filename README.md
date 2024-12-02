# TeleOTP Library

The **TeleOTP** is a PHP library designed to interact with a Telegram-based OTP (One-Time Password) gateway system, allowing for the generation, sending, and verification of OTPs via Telegram. This library offers a set of methods to manage OTP functionality, including API communication, request handling, and response parsing.

## Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
3. [Usage](#usage)
   - [Class Initialization](#class-initialization)
   - [Setting Parameters](#setting-parameters)
   - [Sending OTP](#sending-otp)
   - [Verifying OTP](#verifying-otp)
4. [Methods](#methods)
   - [Constructor](#constructor)
   - [Setters and Getters](#setters-and-getters)
   - [Sending and Verifying OTP](#sending-and-verifying-otp)
   - [Utility Methods](#utility-methods)
5. [Error Handling](#error-handling)
6. [License](#license)

## Overview

The **TeleOTP** library interacts with the Telegram OTP gateway API to facilitate sending and verifying OTPs. You can set parameters such as the phone number, code length, callback URL, and more. The library handles communication with the Telegram API and provides methods for both sending OTPs and verifying their validity.


### Updated `README.md` Example:

## Installation

You can install the `TeleOTP` library either manually or via Composer.

### Option 1: Install via Composer

The recommended way to install `TeleOTP` is through Composer. To do so, run the following command:

```bash
composer require dangujba/teleotp
```

This will download the package and its dependencies to your project.
Once you've installed your package via Composer, using it in your project is straightforward. Here's how you can set it up and use it:

### Steps for Using `TeleOTP` After Installing via Composer

1. **Install via Composer**  
   If you haven't already done this, install the package by running the following command in your project directory:

   ```bash
   composer require dangujba/teleotp 
   ```

  

2. **Include Composer's Autoloader**  
   Composer automatically generates an autoloader that can be included to access all installed packages. At the top of your PHP script, include the Composer autoloader:

   ```php
   require 'vendor/autoload.php';
   ```

   This will load all dependencies installed via Composer, including `TeleOTP`.

3. **Instantiate and Use `TeleOTP`**  
   After including the autoloader, you can instantiate the `TeleOTP` class and start using it:

   ```php
   use TeleOTP\TeleOTP; 

   // Instantiate the class with your API token
   $teleOtp = new TeleOTP('YOUR_API_TOKEN');

   // Example usage
   $teleOtp->sendOtp('PHONE_NUMBER'); // Replace with an actual phone number
   ```

   Ensure that `YOUR_API_TOKEN` is replaced with your actual API token for the service you’re using with `TeleOTP`.

### Example Code

```php
// Include Composer's autoloader
require 'vendor/autoload.php';

// Use the TeleOTP class (adjust the namespace as needed)
use TeleOTP\TeleOTP;

// Instantiate the TeleOTP class
$teleOtp = new TeleOTP('YOUR_API_TOKEN');

// Set code i.e your otp instead of letting telegram to generate it for you
$teleOtp->setCode('666666');

// Set callback url (optional)
$teleOtp->setCallbackUrl('https://github.com');

// Set custom payload (optional)
$body = [
   'id' => '1234',
   'purpose' => 'registration'
];
$teleOtp->setPayload($body);

// Example function to send OTP
$teleOtp->sendOTP('PHONE_NUMBER'); // Provide the phone number to send OTP to e.g +2341234567
```


### Option 2: Manual Installation

If you prefer not to use Composer, you can manually include the `TeleOTP.php` file in your project.

1. **Download or clone the repository:**

   You can either download the ZIP file from GitHub or clone the repository using Git:

   ```bash
   git clone https://github.com/your-username/teleotp.git
   ```

2. **Include the `TeleOTP.php` file:**

   Include the `TeleOTP.php` file in your project:

   ```php
   require_once 'path/to/TeleOTP.php';
   ```

   Replace `path/to` with the actual path to the `TeleOTP.php` file in your project.

3. **Instantiate the `TeleOTP` class:**

   Once included, instantiate the `TeleOTP` class and set up your API token:

   ```php
   $teleOtp = new TeleOTP('YOUR_API_TOKEN');
   ```

### Configuration

Make sure to replace `'YOUR_API_TOKEN'` with your actual API token provided by the (Telegram Gateway API)[https://gateway.telegram.org].

---

For more details on usage and available features, refer to the documentation or check the examples in the repository.
```

### Explanation:

- **Option 1 (Composer Installation):** Instructions on installing via Composer with the `composer require` command.
- **Option 2 (Manual Installation):** Instructions for cloning or downloading the repository and including the `TeleOTP.php` file manually.
- The rest of the file explains how to instantiate the class and configure the API token.

Ensure to replace `your-username/teleotp` with the actual name of your Composer package.
## Usage

### Class Initialization

To initialize the class, create a new instance of `TeleOTP`. Optionally, you can pass an API token for authentication.

```php
$teleOTP = new TeleOTP('YOUR_API_TOKEN');
```

### Setting Parameters

You can set various parameters using the provided setter methods.

```php
$teleOTP->setPhoneNumber('1234567890');
$teleOTP->setCodeLength(6);
$teleOTP->setCallbackUrl('https://example.com/callback');
```

### Sending OTP

To send an OTP, simply call the `sendOTP` method, passing in the phone number (if not already set).

```php
$response = $teleOTP->sendOTP('1234567890');
```

This will send an OTP to the specified phone number and return the API response.

### Verifying OTP

After receiving the OTP, you can verify it using the `validateCode` method. Pass the request ID and the code entered by the user.

```php
$verificationResult = $teleOTP->validateCode([
    'request_id' => 'some_request_id',
    'code' => '123456'
]);
```

## Methods

### Constructor

```php
public function __construct($token = '');
```

- **Parameters:**
  - `$token`: (string) The API token for authentication.

- Initializes the class with an optional API token.

### Setters and Getters

- **setToken($token)**
  - Sets the API token.
  
- **getToken()**
  - Returns the current API token.
  
- **setPhoneNumber($phoneNumber)**
  - Sets the phone number for OTP operations.
  
- **getPhoneNumber()**
  - Returns the phone number.
  
- **setCodeLength($code)**
  - Sets the OTP code length (between 4 and 8 digits).
  
- **getCodeLength()**
  - Returns the OTP code length.
  
- **setRequestId($requestID)**
  - Sets the request ID for tracking the OTP request.
  
- **getRequestId()**
  - Returns the current request ID.
  
- **setCallbackUrl($callbackUrl)**
  - Sets the callback URL for responses.
  
- **getCallbackUrl()**
  - Returns the callback URL.

### Sending and Verifying OTP

- **sendOTP($phoneNumber = '')**
  - Sends an OTP to the provided phone number (or the phone number set in the object).
  
- **validateCode(array $params)**
  - Verifies the OTP code entered by the user.
  - **Parameters:**
    - `$params`: (array) Contains the `request_id` and optional `code` for validation.
  - **Returns:**
    - `true` if the code is valid, otherwise a relevant status message (e.g., 'Expired', 'Invalid').

### Utility Methods

- **postRequest($request, $endPoint)**
  - Sends a POST request to the API endpoint with the specified parameters.
  
- **getResponse()**
  - Returns the last raw response received from the server.

- **getRawResponse()**
  - Returns the decoded response from the last API call as an associative array.

- **getRequestCost()**
  - Retrieves the cost of the last request from the API response.

- **getRemainingBalance()**
  - Retrieves the remaining balance from the last response.

- **getOTPStatus()**
  - Retrieves the status of the OTP, such as 'Sent', 'Read', or 'Revoked'.

## Error Handling

The library will throw exceptions for invalid inputs or missing required parameters. For example:

- `Exception("Phone Number is required!")` if no phone number is set or passed.
- `Exception("TTL must be between 60 and 86400 seconds.")` if the TTL is outside the valid range.

Make sure to wrap your method calls in try-catch blocks to handle any errors gracefully.

```php
try {
    $teleOTP->sendOTP('1234567890');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

## License

The TeleOTP library is released under the MIT License. See the LICENSE file for more information.


This documentation includes the main sections you would typically need for a GitHub project, including installation instructions, usage examples, method descriptions, and error handling. Feel free to modify it based on any additional functionality or details you might want to add.
