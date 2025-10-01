<?php
// debug-payment.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');
include('includes/pesapal-config.php');

echo "<h2>PesaPal Payment Debug</h2>";

// Test authentication first
echo "<h3>1. Testing Authentication</h3>";
$headers = getPesaPalAuthHeaders();
if($headers) {
    echo "✓ Authentication successful!<br>";
    echo "Token: " . substr(str_replace('Authorization: Bearer ', '', $headers[0]), 0, 50) . "...<br>";
} else {
    echo "✗ Authentication failed!<br>";
    exit;
}

// Test payment initiation with sample data
echo "<h3>2. Testing Payment Initiation</h3>";

$merchantReference = 'TEST_' . time();
$amount = 100; // Small test amount
$userId = 1; // Test user ID

$api_url = PESAPAL_API_URL . '/api/Transactions/SubmitOrderRequest';

// Sample user data (replace with actual user data from your DB)
$userData = [
    'email' => 'test@example.com',
    'contactno' => '0712345678',
    'name' => 'Test User',
    'billingAddress' => 'Test Address',
    'billingCity' => 'Nairobi',
    'billingState' => 'Nairobi',
    'billingPincode' => '00100'
];

$payload = [
    'id' => $merchantReference,
    'currency' => 'KES',
    'amount' => $amount,
    'description' => 'Test Payment #' . $merchantReference,
    'callback_url' => PESAPAL_CALLBACK_URL,
    'notification_id' => PESAPAL_IPN_URL,
    'billing_address' => [
        'email_address' => $userData['email'],
        'phone_number' => $userData['contactno'],
        'country_code' => 'KE',
        'first_name' => $userData['name'],
        'middle_name' => '',
        'last_name' => '',
        'line_1' => $userData['billingAddress'],
        'line_2' => '',
        'city' => $userData['billingCity'],
        'state' => $userData['billingState'],
        'postal_code' => $userData['billingPincode'],
        'zip_code' => $userData['billingPincode']
    ]
];

echo "Payload being sent:<br>";
echo "<pre>" . json_encode($payload, JSON_PRETTY_PRINT) . "</pre>";

echo "API URL: " . $api_url . "<br>";
echo "Callback URL: " . PESAPAL_CALLBACK_URL . "<br>";
echo "IPN URL: " . PESAPAL_IPN_URL . "<br>";

// Make the API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in response

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers_response = substr($response, 0, $header_size);
$body = substr($response, $header_size);

if (curl_error($ch)) {
    echo "CURL Error: " . curl_error($ch) . "<br>";
}

curl_close($ch);

echo "<h4>Response Details:</h4>";
echo "HTTP Code: " . $http_code . "<br>";

if ($http_code == 200) {
    $result = json_decode($body, true);
    if (isset($result['redirect_url'])) {
        echo "✓ Payment initiation successful!<br>";
        echo "Redirect URL: <a href='" . $result['redirect_url'] . "' target='_blank'>" . $result['redirect_url'] . "</a><br>";
        echo "Merchant Reference: " . $merchantReference . "<br>";
    } else {
        echo "✗ Payment initiation failed - no redirect URL<br>";
        echo "Full response: <pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
    }
} else {
    echo "✗ Payment initiation failed with HTTP code: " . $http_code . "<br>";
    echo "Response headers: <pre>" . $headers_response . "</pre>";
    echo "Response body: <pre>" . $body . "</pre>";
    
    // Try to decode as JSON for better error message
    $error_data = json_decode($body, true);
    if ($error_data && isset($error_data['error'])) {
        echo "Error message: " . $error_data['error']['code'] . " - " . $error_data['error']['message'] . "<br>";
    }
}
?>