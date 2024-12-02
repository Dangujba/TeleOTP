<?php
session_start();
/**
 * Class TeleOTP
 *
 * A class designed to manage Telegram-Gateway OTP functionality,
 * including sending messages, handling updates, and interacting
 * with OTP verification.
 *
 * @package TeleOTP Library
 * @author    Muhammad Baba Goni
 * @copyright 2024 StackView Studio
 * @license   MIT License
 * @version   1.0
 * @link      muhammadgoni51@gmail.com
 * @package   TeleOTP
 */

class TeleOTP {

    /**
     * Base URL for the Telegram Gateway API
     *
     * @var string
     */
    private $baseUrl = 'https://gatewayapi.telegram.org/';

    /**
     * Phone number to send OTP to
     *
     * @var string
     */
    private $phoneNumber;

    /**
     * API token for authentication
     *
     * @var string
     */
    private $token;

    /**
     * Endpoint for specific operations
     *
     * @var string
     */
    private $endPoint;

    /**
     * Send Verification Message Parameters
     *
     * @var array
     */
    private $verificationParams = [];

    /**
     * Request ID of the last OTP sent
     *
     * @var string|null
     */
    private $lastResponse = null;

  

    /**
     * Constructor to initialize the values.
     *
     * @param string $baseUrl  Base URL for the Telegram API (default: 'https://gatewayapi.telegram.org/')
     * @param string $endPoint Endpoint for the operation (optional).
     * @param string $token    API token for authentication (optional).
     */
    public function __construct($token = '') {
        $this->baseUrl = 'https://gatewayapi.telegram.org/';  // Default URL
        $this->token = $token;  // Default is empty string
        $this->verificationParams['code_length'] = 6;
    }

    /**
     * Set the API token for the instance.
     *
     * @param string $token API token to be set.
     * @return void
     */
    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * Get the API token for the instance.
     *
     * @return string The API token.
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Set the phone number for OTP operations.
     *
     * @param string $phoneNumber Phone number to send OTP to.
     * @return void
     */
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Get the phone number.
     *
     * @return string The phone number.
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * Set the API endpoint for the operation.
     *
     * @param string $endPoint The endpoint to be used.
     * @return void
     */
    public function setEndPoint($endPoint) {
        $this->endPoint = $endPoint;
    }

    /**
     * Get the endpoint for the operation.
     *
     * @return string The endpoint.
     */
    public function getEndPoint() {
        return $this->endPoint;
    }

    /**
     * Set the unique request ID for previous request (optional).
     *
     * @param string $requestId Request ID from the checkSendAbility method.
     * @return void
     */
    public function setRequestId($requestID){
        $this->verificationParams['request_id'] = $requestID;
    }

    public function getRequestId(){
        return isset($_SESSION['request_id']) ? $_SESSION['request_id'] : $this->verificationParams['request_id'];
    }

    /**
     * Set the code length for OTP.
     *
     * @param int $code The length of the OTP code (must be between 4 and 8).
     * @return void
     * @throws Exception If the code is invalid.
     */
    public function setCodeLength($code){
        if($code < 4 || $code > 8){
            throw new Exception("Invalid code, codes must be between 4 and 8 digits.");
        }
        $this->verificationParams['code_length'] = $code;
    }

    /**
     * Get the OTP code length.
     *
     * @return int The OTP code length.
     */
    public function getCodeLength(){
        return $this->verificationParams['code_length'];
    }

    /**
     * Set the verification code manually (optional).
     *
     * @param string $code Verification code.
     * @return void
     */
    public function setCode($code){
        $this->verificationParams['code'] = $code;
    }

    /**
     * Get the manually set verification code.
     *
     * @return string The verification code.
     */
    public function getCode(){
        return $this->verificationParams['code'];
    }

    /**
     * Set the Telegram channel username to send the code from (optional).
     *
     * @param string $senderUsername Telegram channel username.
     * @return void
     */
    public function setSenderUsername($username){
        $this->verificationParams['sender_username'] = $username;
    }

    /**
     * Get the Telegram channel username.
     *
     * @return string The sender username.
     */
    public function getSenderUsername(){
        return $this->verificationParams['sender_username'];
    }

    /**
     * Set the callback URL (optional).
     *
     * @param string $callbackUrl Callback URL for the response.
     * @return void
     */
    public function setCallbackUrl($callbackUrl){
        $this->verificationParams['callback_url'] = $callbackUrl;
    }

    /**
     * Get the callback URL.
     *
     * @return string The callback URL.
     */
    public function getCallbackUrl(){
        return $this->verificationParams['callback_url'];
    }

    /**
     * Set the payload (optional).
     *
     * @param string $payload Additional data to send with the OTP.
     * @return void
     */
    public function setPayload($payload = "custom_payload"){
        $this->verificationParams['payload'] = $payload;
    }

    /**
     * Get the payload.
     *
     * @return string The payload.
     */
    public function getPayload(){
        return $this->verificationParams['payload'];
    }

    /**
     * Set the time-to-live (TTL) for OTP in seconds (optional).
     *
     * @param int $ttl TTL in seconds (must be between 60 and 86400).
     * @return void
     */
    public function setTtl($ttl){
        if ($ttl >= 60 && $ttl <= 86400) {
            $this->verificationParams['ttl'] = $ttl;
        } else {
            throw new Exception("TTL must be between 60 and 86400 seconds.");
        }
    }

    /**
     * Get the TTL for OTP.
     *
     * @return int The TTL in seconds.
     */
    public function getTtl(){
        return $this->verificationParams['ttl'];
    }

    /**
     * Checks the ability to send a request based on the phone number.
     * 
     * This method validates the phone number, either using the one provided or the one stored in the object. 
     * It then sends a request to check the ability to send a request, and if successful, returns the `request_id`.
     * 
     * @param string $phoneNumber The phone number to check. If not provided, it uses the stored phone number.
     * 
     * @return string|null Returns the `request_id` if the request is successful, or `null` if no `request_id` is found.
     * 
     * @throws Exception Throws an exception if no valid phone number is provided.
     *
     */
    public function checkAbility($phoneNumber = ''){
        $number = '';

        if(empty($phoneNumber) && empty($this->getPhoneNumber())){
            throw new Exception("Phone Number is required!");
        }

        if(!empty($phoneNumber)){
            $number = $phoneNumber;
        } else {
            $number = $this->getPhoneNumber();
        }

        $response = json_decode($this->postRequest(['phone_number' => $phoneNumber], 'checkSendAbility'), true);

        if(isset($response['result']['request_id'])){
            return true;
        }
        return false;
    }


    /**
     * Send OTP to the specified phone number.
     *
     * @param string $phoneNumber The phone number to send OTP to (optional).
     * @return mixed The response from the API.
     * @throws Exception If the phone number is not provided.
     */
    public function sendOTP($phoneNumber = ''){
        $number = '';
    
        // Validate that a phone number is provided
        if (empty($phoneNumber) && empty($this->getPhoneNumber())) {
            throw new Exception("Phone Number is required!");
        }
    
        // Determine the phone number to use
        $number = !empty($phoneNumber) ? $phoneNumber : $this->getPhoneNumber();
    
        // Prepare the request data
        $data = array_merge([
            'phone_number' => $number,
        ], $this->verificationParams);
    
        // Send the request and store the response
        $this->lastResponse = $this->postRequest($data, 'sendVerificationMessage');
    
        // Decode the response into an associative array
        $getResponse = json_decode($this->lastResponse, true);
    
        // Safely handle expected keys from the response
        if (isset($getResponse['ok']) && $getResponse['ok'] === true) {
        $result = $getResponse['result'];

        // Safely handle expected keys from the response
        //$this->lastRequestId = $result['request_id'];
        $_SESSION['request_id'] = $result['request_id'] ?? null;
        $this->verificationParams['phone_number'] = $result['phone_number'] ?? null;
        }
    
        return $this->lastResponse;
    }
    
    /**
     * Verifies the code entered by the user for a specific request ID.
     * 
     * This method checks the verification status for a given request ID and code. If no code is provided,
     * it will only verify the status using the request ID. It handles different verification statuses such as 
     * valid code, invalid code, expired code, or exceeded attempts, and returns appropriate responses.
     * 
     * @param string $requestId The request ID for which the verification is being performed. If not provided,
     *                          it will use the request ID stored in the object.
     * @param string|null $code The verification code entered by the user. If null, it will skip the code verification.
     * 
     * @return mixed Returns:
     * - `true` if the code is valid,
     * - `false` if the code is invalid,
     * - `"Expired"` if the code has expired,
     * - `"Number of attempts exceeded"` if the number of attempts has exceeded the allowed limit,
     * - The raw response if the verification status is not found.
     * 
     * @throws Exception Throws an exception if the request ID is empty or invalid.
     */
    public function validateCode(array $params) {
        // Define the endpoint for the verification status
        $endpoint = 'checkVerificationStatus';
    
        // Validate the presence of request_id, either from the parameter or the object method
        $requestId = !empty($params['request_id']) ? $params['request_id'] : $this->getRequestId();
        if (empty($requestId)) {
            throw new Exception("Invalid Request ID: Request ID is required");
        }
    
        // Prepare the request data
        $data = [
            'request_id' => $requestId
        ];
    
        // Include the code in the request if provided
        if (!empty($params['code'])) {
            $data['code'] = $params['code'];
        }
    
        // Send the request and decode the JSON response
        $response = $this->postRequest($data, $endpoint);
        $result = json_decode($response, true);
    
        // Check if the response is valid and contains verification status
        if (isset($result['result']['verification_status']['status'])) {
            $status = $result['result']['verification_status']['status'];
    
            // Handle different verification statuses
            switch ($status) {
                case 'code_valid':
                    return 'valid';
                case 'code_invalid':
                    return 'invalid';
                case 'expired':
                    return "Expired";
                case 'code_max_attempts_exceeded':
                    return "Number of attempts exceeded";
                default:
                    return "Unknown status: $status";
            }
        }
    
        // Return raw response if verification status is not found
        return $response;
    }

    public function revokeCode(){
        $response = json_decode($this->postRequest(['request_id' => $this->getRequestId()], 'revokeVerificationMessage'), true);
        
        if(isset($response['result'])){
            return $response['result'];
        }
    }


    /**
     * Send a POST request to the API endpoint.
     *
     * @param array $request The request data.
     * @return mixed The API response.
     * @throws Exception If the endpoint is not set.
     */
    private function postRequest($request, $endPoint){

        if(empty($this->getEndPoint()) && empty($endPoint)){
            throw new Exception("Invalid Endpoint: No Endpoint provided");
        }

        $url = $this->baseUrl . $endPoint;

        $headers = [];

        if (!empty($this->token)) {
            // Add token to header (Authorization: Bearer <token>)
            $headers[] = 'Authorization: Bearer ' . $this->token;
           // $headers[] = 'Content-Type: application/json';
        } else {
            // Use token as a URL parameter (access_token=<token>)
            $url .= (strpos($url, '?') === false ? '?' : '&') . 'access_token=' . $this->token;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Retrieves the last response.
     * 
     * This method returns the raw response as it was received from the server. It does not modify or decode the response.
     * 
     * @return string|null The raw response from the server, or null if no response exists.
     */
    public function getResponse() {
        return $this->lastResponse;
    }
    
    /**
     * Retrieves the decoded response as an associative array.
     * 
     * This method decodes the last response JSON string into an associative array. If the last response is null,
     * it will simply return null.
     * 
     * @return array|null The decoded response as an associative array, or null if the response is null.
     */
    public function getRawResponse() {
        return $this->lastResponse !== null ? json_decode($this->lastResponse, true) : null;
    }
    
    /**
     * Retrieves the cost of the last request.
     * 
     * This method decodes the last response JSON and returns the value of the 'request_cost' field from the 'result' object. 
     * If the response or the field is not available, it will return null.
     * 
     * @return float|null The cost of the last request, or null if not available.
     */
    public function getRequestCost() {
        $get = json_decode($this->lastResponse, true);
        return isset($get['result']['request_cost']) ? $get['result']['request_cost'] : null;
    }
    
    /**
     * Retrieves the remaining balance from the last response.
     * 
     * This method decodes the last response JSON and returns the value of the 'remaining_balance' field from the 'result' object.
     * If the response or the field is not available, it will return null.
     * 
     * @return float|null The remaining balance, or null if not available.
     */
    public function getRemainingBalance() {
        $get = json_decode($this->lastResponse, true);
        return isset($get['result']['remaining_balance']) ? $get['result']['remaining_balance'] : null;
    }

        /**
     * Retrieves the status of the OTP message.
     * 
     * This method checks the delivery status of the OTP and returns a descriptive message.
     * It will handle statuses such as sent, read, revoked, or unknown.
     * 
     * @return string Returns a status message, or an error message if the status is not found.
     */
    public function getOTPStatus() {
        // Decode the last response to an associative array
        $get = json_decode($this->lastResponse, true);
    
        // Check if the 'result' and 'delivery_status' exist in the response
        if (isset($get['result']['delivery_status']['status'])) {
            // Retrieve the status
            $status = $get['result']['delivery_status']['status'];
    
            // Handle different status cases
            switch ($status) {
                case 'sent':
                    return 'OTP Sent';
                case 'read':
                    return 'OTP Read';
                case 'Revoked':
                    return 'OTP Revoked';
                default:
                    return "Unknown Status: " . $status;
            }
        } else {
            // If the delivery status is not found, return an error message
            return "Delivery status not found or invalid response.";
        }
    }




}

?>
