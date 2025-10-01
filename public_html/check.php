<?php
// checkout.php

// Your Pesapal consumer key & secret (from dashboard)
$consumer_key    = "9D/HcSLmjiOBO5YnVjImAS5ALWbzFmx/";
$consumer_secret = "4QZPR7EkQeIMpIpIC+3BMNzK+Gw=";

// Use sandbox for testing, live for production
// Sandbox: https://cybqa.pesapal.com/pesapalv3/api/Payments/PostPesapalDirectOrderV4
// Live:    https://pay.pesapal.com/v3/api/Payments/PostPesapalDirectOrderV4
$api = "https://pay.pesapal.com/v3/api/Payments/PostPesapalDirectOrderV4";

// Callback URL (must be accessible online — use ngrok in local dev)
$callback_url = "https://4719f43804ec.ngrok-free.app/Lewvitec/pesapal/payment_callback.php";

// Unique order reference
$reference = 'ORD'.time();

// Build XML payment request
$post_xml = '<?xml version="1.0" encoding="utf-8"?>
<PesapalDirectOrderInfo 
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
    Amount="100.00" 
    Description="Payment for Order '.$reference.'" 
    Type="MERCHANT" 
    Reference="'.$reference.'" 
    FirstName="Evans" 
    LastName="Mulwa" 
    Email="muasyaevans55@gmail.com" 
    PhoneNumber="702379337" 
    Currency="KES"
    xmlns="http://www.pesapal.com"/>';

$post_xml = htmlentities($post_xml);

// OAuth parameters
$params = array(
    'oauth_callback' => $callback_url,
    'oauth_consumer_key' => $consumer_key,
    'oauth_nonce' => uniqid(mt_rand(), true),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_version' => '1.0',
    'pesapal_request_data' => $post_xml
);

// Sort parameters
ksort($params);
$encoded_params = [];
foreach ($params as $key => $value) {
    $encoded_params[] = rawurlencode($key) . '=' . rawurlencode($value);
}

// Base string for signing
$base_string = "POST&" . rawurlencode($api) . '&' . rawurlencode(implode('&', $encoded_params));

// Signing key
$signing_key = rawurlencode($consumer_secret) . '&';
$signature   = base64_encode(hash_hmac('sha1', $base_string, $signing_key, true));
$params['oauth_signature'] = $signature;

// Build OAuth header
$auth_header = "OAuth ";
$header_params = [];
foreach ($params as $key => $value) {
    if (strpos($key, 'oauth') === 0) {
        $header_params[] = $key . '="' . rawurlencode($value) . '"';
    }
}
$auth_header .= implode(', ', $header_params);

// Send cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: '.$auth_header,
    'Content-Type: application/x-www-form-urlencoded'
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "pesapal_request_data=".$post_xml);

$response = curl_exec($ch);
curl_close($ch);

// Decode Pesapal response (should contain redirect_url)
$data = json_decode($response, true);
$iframe_url = $data['redirect_url'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Pesapal</title>
</head>
<body>
<?php if ($iframe_url): ?>
    <iframe src="<?php echo htmlspecialchars($iframe_url); ?>" width="100%" height="700px" scrolling="no" frameborder="0"></iframe>
<?php else: ?>
    <p>Error loading Pesapal payment page:</p>
    <pre><?php echo htmlspecialchars($response); ?></pre>
<?php endif; ?>
</body>
</html>