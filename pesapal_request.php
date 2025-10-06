<?php
session_start();
include('includes/config.php');
include('pesapal_config.php');

// Check if required session data exists
if (!isset($_SESSION['order_id']) || !isset($_SESSION['cart_total']) || !isset($_SESSION['billing_info'])) {
    die("Invalid request. Please complete the checkout process first.");
}

$order_id = $_SESSION['order_id'];
$checkout_amount = $_SESSION['cart_total'];
$billing_info = $_SESSION['billing_info'];

// Fetch order info from database
$order_query = mysqli_query($con, "SELECT * FROM orders WHERE id = '$order_id'");
$order = mysqli_fetch_array($order_query);
if (!$order) {
    die("Order not found");
}

// Fetch user info
$user_id = $order['userId'];
$user_query = mysqli_query($con, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_array($user_query);

// Format amount for PesaPal (2 decimal places)
$checkout_amount = number_format(floatval($checkout_amount), 2, '.', '');

// Generate a unique ID for this payment request
$unique_order_id = 'ORD_' . $order_id . '_' . time();

// Prepare payload with billing information from session
$payload = [
    "id" => $unique_order_id,
    "currency" => "KES",
    "amount" => $checkout_amount,
    "description" => "Order #$order_id - " . $user['firstname'] . ' ' . $user['lastname'],
    "callback_url" => "https://lewvitic.co.ke/pesapal_callback.php",
    "notification_id" => PESAPAL_IPN_ID,
    "billing_address" => [
        "email_address" => $billing_info['email_address'],
        "phone_number" => $billing_info['phone_number'],
        "country_code" => $billing_info['country_code'],
        "first_name" => $billing_info['first_name'],
        "last_name" => $billing_info['last_name'],
        "line_1" => $billing_info['phone'],
        "city" => $billing_info['billingCity'] ?? '',
        "state" => $billing_info['billingState'] ?? '',
        "postal_code" => $billing_info['postal_code'] ?? ''
    ]
];

// Debug output (comment out in production)
/*
echo "<h3>PesaPal Request Details:</h3>";
echo "Order ID: " . $order_id . "<br>";
echo "Amount: Kes. " . $checkout_amount . "<br>";
echo "Unique Order ID: " . $unique_order_id . "<br>";
echo "Callback URL: " . PESAPAL_CALLBACK_URL . "<br>";
echo "<pre>Billing Info: " . print_r($billing_info, true) . "</pre>";
echo "<pre>Payload: " . print_r($payload, true) . "</pre>";
*/

// 1. Request OAuth token from PesaPal
$ch = curl_init(PESAPAL_BASE_URL . "/api/Auth/RequestToken");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "consumer_key" => PESAPAL_CONSUMER_KEY,
    "consumer_secret" => PESAPAL_CONSUMER_SECRET
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$resp = curl_exec($ch);
if (!$resp) {
    die("Auth request failed: " . curl_error($ch));
}

$auth = json_decode($resp, true);
$token = $auth['token'] ?? '';
$error = $auth['error'] ?? '';
curl_close($ch);

if (!$token) {
    die("Failed to get auth token from PesaPal. Error: " . $error . " Response: " . print_r($auth, true));
}

// 2. Send payment request to PesaPal
$ch = curl_init(PESAPAL_BASE_URL . "/api/Transactions/SubmitOrderRequest");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $token",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$response) {
    die("Payment request failed. No response from PesaPal.");
}

$data = json_decode($response, true);

// Store PesaPal tracking data in session for callback
if (isset($data['order_tracking_id'])) {
    $_SESSION['pesapal_tracking_id'] = $data['order_tracking_id'];
    $_SESSION['pesapal_merchant_reference'] = $unique_order_id;
    
    // Update order with PesaPal tracking ID
    mysqli_query($con, "UPDATE orders SET 
        pesapal_tracking_id = '" . $data['order_tracking_id'] . "',
        merchant_reference = '$unique_order_id'
        WHERE id = '$order_id'");
}

// Redirect user to PesaPal payment page
if (isset($data['redirect_url'])) {
    // Clear cart after successful payment initiation
    unset($_SESSION['cart']);
    
    header("Location: " . $data['redirect_url']);
    exit;
} else {
    // Log the error for debugging
    error_log("PesaPal payment request failed. Response: " . print_r($data, true));
    
    echo "<h3>Payment Request Failed</h3>";
    echo "<p>We encountered an issue while connecting to PesaPal. Please try again later.</p>";
    echo "<p>Error details: " . ($data['error'] ?? 'Unknown error') . "</p>";
    echo "<a href='checkout.php' class='btn btn-primary'>Return to Checkout</a>";
    
    // Debug info (remove in production)
    if (isset($data['error'])) {
        echo "<div style='background: #f8d7da; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h4>Debug Information:</h4>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        echo "</div>";
    }
}

// Function to log PesaPal requests for debugging
function logPesaPalRequest($order_id, $payload, $response, $status) {
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'order_id' => $order_id,
        'payload' => $payload,
        'response' => $response,
        'status' => $status
    ];
    
    file_put_contents('pesapal_requests.log', json_encode($log_data) . PHP_EOL, FILE_APPEND);
}

// Log this request
logPesaPalRequest($order_id, $payload, $data, isset($data['redirect_url']) ? 'success' : 'failed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to PesaPal...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 50px;
            text-align: center;
        }
        .loading {
            margin: 30px auto;
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3e63c9;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3e63c9;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php if (isset($data['redirect_url'])): ?>
    <h2>Redirecting to PesaPal...</h2>
    <div class="loading"></div>
    <p>Please wait while we redirect you to the secure payment page.</p>
    <script>
        setTimeout(function() {
            window.location.href = "<?php echo $data['redirect_url']; ?>";
        }, 2000);
    </script>
    <?php endif; ?>
</body>
</html>
