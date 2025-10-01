<?php
// includes/pesapal-config.php

// PesaPal API credentials
define('PESAPAL_CONSUMER_KEY', '9D/HcSLmjiOBO5YnVjImAS5ALWbzFmx/');
define('PESAPAL_CONSUMER_SECRET', '4QZPR7EkQeIMpIpIC+3BMNzK+Gw=');

// PesaPal environment - set to true for live, false for demo
define('PESAPAL_LIVE', false);

// PesaPal API endpoints
if (PESAPAL_LIVE) {
    define('PESAPAL_API_ENDPOINT', 'https://pay.pesapal.com/v3');
} else {
    define('PESAPAL_API_ENDPOINT', 'https://cybqa.pesapal.com/pesapalv3');
}

// Generate authentication token
function getPesapalAuthToken() {
    $consumer_key = PESAPAL_CONSUMER_KEY;
    $consumer_secret = PESAPAL_CONSUMER_SECRET;
    $api_endpoint = PESAPAL_API_ENDPOINT;
    
    // Prepare request
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    $data = [
        'consumer_key' => $consumer_key,
        'consumer_secret' => $consumer_secret
    ];
    
    $ch = curl_init("$api_endpoint/api/Auth/RequestToken");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $response_data = json_decode($response, true);
        return $response_data['token'];
    } else {
        error_log("PesaPal Auth Error: $response");
        return false;
    }
}

// Generate iframe URL
function generatePesapalIframeUrl($params) {
    $token = getPesapalAuthToken();
    
    if (!$token) {
        return false;
    }
    
    $api_endpoint = PESAPAL_API_ENDPOINT;
    
    // Prepare order data
    $order_data = [
        'id' => $params['reference'],
        'currency' => 'KES',
        'amount' => $params['amount'],
        'description' => $params['description'],
        'callback_url' => $params['callback_url'],
        'notification_id' => '', // Optional: Your notification ID
        'branch' => '', // Optional
        'billing_address' => [
            'email_address' => $params['email'],
            'phone_number' => $params['phone_number'],
            'country_code' => 'KE',
            'first_name' => $params['first_name'],
            'last_name' => $params['last_name'] ?? '',
            'line_1' => '', // Optional
            'line_2' => '', // Optional
            'city' => '', // Optional
            'state' => '', // Optional
            'postal_code' => '', // Optional
            'zip_code' => '' // Optional
        ]
    ];
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $ch = curl_init("$api_endpoint/api/Transactions/SubmitOrderRequest");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $response_data = json_decode($response, true);
        return $response_data['redirect_url'];
    } else {
        error_log("PesaPal Order Error: $response");
        return false;
    }
}

// Check payment status
function checkPesapalPaymentStatus($order_tracking_id) {
    $token = getPesapalAuthToken();
    
    if (!$token) {
        return false;
    }
    
    $api_endpoint = PESAPAL_API_ENDPOINT;
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ];
    
    $ch = curl_init("$api_endpoint/api/Transactions/GetTransactionStatus?orderTrackingId=$order_tracking_id");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $response_data = json_decode($response, true);
        return $response_data;
    } else {
        error_log("PesaPal Status Check Error: $response");
        return false;
    }
}
?>