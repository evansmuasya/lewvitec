<?php
session_start();
require_once('includes/config.php');
require_once('includes/pesapal-config.php');
require_once('pesapal/OAuth.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: checkout.php");
    exit();
}

// Get form data
$payment_method = $_POST['payment_method'];
$order_tracking_id = $_POST['order_tracking_id'];
$amount = $_POST['amount'];

// Get user details
$user_id = $_SESSION['id'];
$user_query = mysqli_query($con, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_array($user_query);

// Get cart items
$cart_items = $_SESSION['cart'];
$product_ids = array_keys($cart_items);

// Create order in database
$order_query = mysqli_query($con, 
    "INSERT INTO orders (userId, productId, quantity, paymentMethod, orderStatus, merchant_reference, amount) 
     VALUES ('$user_id', '" . implode(',', $product_ids) . "', '" . count($cart_items) . "', 
     '$payment_method', 'Pending', '$order_tracking_id', '$amount')");

if ($order_query) {
    $order_id = mysqli_insert_id($con);
    
    // Process based on payment method
    if ($payment_method == 'pesapal') {
        // Initialize Pesapal payment
        $consumer_key = PESAPAL_CONSUMER_KEY;
        $consumer_secret = PESAPAL_CONSUMER_SECRET;
        
        // Generate unique reference
        $pesapal_merchant_reference = $order_tracking_id;
        $pesapal_desc = "Payment for Order #$order_tracking_id";
        
        // Callback and IPN URLs
        $callback_url = CALLBACK_URL;
        $ipn_url = IPN_URL;
        
        // Initialize OAuth
        $token = $params = NULL;
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        
        // Set transaction parameters
        $params = array(
            'oauth_callback' => $callback_url,
            'pesapal_request_data' => array(
                'Amount' => $amount,
                'Description' => $pesapal_desc,
                'Type' => 'MERCHANT',
                'Reference' => $pesapal_merchant_reference,
                'FirstName' => $user['name'],
                'LastName' => '',
                'Email' => $user['email'],
                'PhoneNumber' => $user['contactno'],
                'Currency' => 'KES'
            )
        );
        
        // Create OAuth request
        $request = OAuthRequest::from_consumer_and_token(
            $consumer, 
            $token, 
            'GET', 
            PESAPAL_IFRAME_URL, 
            $params
        );
        
        $request->sign_request($signature_method, $consumer, $token);
        $iframe_src = $request->to_url();
        
        // Redirect to Pesapal
        header("Location: $iframe_src");
        exit();
    }
} else {
    // Order creation failed
    $_SESSION['error'] = "Failed to create order. Please try again.";
    header("Location: checkout.php");
    exit();
}
?>