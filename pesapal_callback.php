<?php
session_start();
include('includes/config.php');
include('pesapal_config.php');

// === STEP 1: Validate incoming request ===
if (!isset($_GET['OrderTrackingId']) || !isset($_GET['OrderMerchantReference'])) {
    die("Invalid callback request");
}

$tracking_id = $_GET['OrderTrackingId'];
$merchant_reference = $_GET['OrderMerchantReference'];

// === STEP 2: Get OAuth Token ===
$ch = curl_init(PESAPAL_BASE_URL . "/api/Auth/RequestToken");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "consumer_key"    => PESAPAL_CONSUMER_KEY,
    "consumer_secret" => PESAPAL_CONSUMER_SECRET
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$resp  = curl_exec($ch);
$auth  = json_decode($resp, true);
$token = $auth['token'] ?? '';
curl_close($ch);

if (!$token) {
    file_put_contents('pesapal_callback_debug.log', "Auth Failed: " . print_r($auth, true) . PHP_EOL, FILE_APPEND);
    die("Failed to obtain auth token from PesaPal.");
}

// === STEP 3: Query Transaction Status ===
$status_url = PESAPAL_BASE_URL . "/api/Transactions/GetTransactionStatus?orderTrackingId=" . urlencode($tracking_id);

$ch = curl_init($status_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$data     = json_decode($response, true);
curl_close($ch);

// === STEP 4: Extract values ===
$order_status      = strtoupper($data['status'] ?? 'UNKNOWN');
$status_code       = $data['payment_status_code'] ?? null;
$status_desc       = strtoupper($data['payment_status_description'] ?? '');
$payment_method    = $data['payment_method'] ?? '';
$amount            = $data['amount'] ?? 0;
$confirmation_code = $data['confirmation_code'] ?? '';

// === STEP 5: Get internal order ID ===
$order_id_res = mysqli_query($con, "SELECT id, productId, quantity, email, customerName FROM orders WHERE merchant_reference = '" . mysqli_real_escape_string($con, $merchant_reference) . "'");
$order_row    = mysqli_fetch_assoc($order_id_res);
$order_id     = $order_row['id'] ?? null;
$product_id   = $order_row['productId'] ?? null;
$quantity     = (int)($order_row['quantity'] ?? 0);
$customer_email = $order_row['billing_email'] ?? '';
$customer_name = $order_row['billing_first_name'] ?? 'Customer';

// === STEP 6: Normalize status ===
$success = ($order_status === "COMPLETED" || $order_status === "SUCCESS" || 
            $status_desc === "COMPLETED" || $status_desc === "SUCCESS" || 
            $status_code == 200);

$failed  = ($order_status === "FAILED" || $status_desc === "FAILED" || 
            $status_code == 400);

if ($success) {
    $final_status = "COMPLETED";
} elseif ($failed) {
    $final_status = "FAILED";
} else {
    $final_status = "PENDING";
}

// === STEP 7: Update orders table ===
if ($order_id) {
    $query = "UPDATE orders 
              SET orderStatus=?, pesapal_tracking_id=?, payment_method=?, amount_paid=?, confirmation_code=? 
              WHERE id=?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssdsi", 
            $final_status, 
            $tracking_id, 
            $payment_method, 
            $amount, 
            $confirmation_code, 
            $order_id
        );
        if (!mysqli_stmt_execute($stmt)) {
            file_put_contents('pesapal_callback_debug.log', "DB Exec Error: " . mysqli_stmt_error($stmt) . PHP_EOL, FILE_APPEND);
        }
        mysqli_stmt_close($stmt);
    } else {
        file_put_contents('pesapal_callback_debug.log', "DB Prepare Error: " . mysqli_error($con) . PHP_EOL, FILE_APPEND);
    }
}

// === STEP 8: If payment successful, reduce stock and send emails ===
if ($success && $product_id && $quantity > 0) {
    $updateProduct = mysqli_prepare($con, "UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?");
    if ($updateProduct) {
        mysqli_stmt_bind_param($updateProduct, "ii", $quantity, $product_id);
        if (!mys_stmt_execute($updateProduct)) {
            file_put_contents('pesapal_callback_debug.log', "Stock Update Error: " . mysqli_stmt_error($updateProduct) . PHP_EOL, FILE_APPEND);
        }
        mysqli_stmt_close($updateProduct);
    }
    
    // Send email notifications for successful payment
    sendOrderEmails($order_id, $customer_email, $customer_name, $amount, $tracking_id, $confirmation_code);
}

// === STEP 9: Log response for debugging ===
file_put_contents('pesapal_callback_debug.log', "Updating order #$order_id with: " . 
    json_encode([
        'status' => $final_status,
        'tracking_id' => $tracking_id,
        'payment_method' => $payment_method,
        'amount' => $amount,
        'confirmation_code' => $confirmation_code
    ]) . PHP_EOL, FILE_APPEND);

// === STEP 10: Redirect user ===
if ($success) {
    header("Location: order_success.php?order_id=" . $order_id);
    exit;
} elseif ($failed) {
    header("Location: order_failed.php?order_id=" . $order_id);
    exit;
} else {
    header("Location: order_pending.php?order_id=" . $order_id);
    exit;
}

/**
 * Send email notifications for successful order using PHP mail() function
 */
function sendOrderEmails($order_id, $customer_email, $customer_name, $amount, $tracking_id, $confirmation_code) {
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Lewvitec Sales <sales@lewvitec.com>" . "\r\n";
    $headers .= "Reply-To: sales@lewvitec.co.ke" . "\r\n";
    
    // Email to customer
    $customer_subject = "Order Confirmation - Your Order #$order_id has been confirmed";
    $customer_message = "
    <html>
    <head>
        <title>Order Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #3e63c9; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; color: #666; font-size: 14px; }
            .order-detail { margin-bottom: 10px; }
            .order-detail strong { display: inline-block; width: 150px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Order Confirmed!</h1>
            </div>
            <p>Dear $customer_name,</p>
            <p>Thank you for your order! Your payment has been successfully processed and your order is now confirmed.</p>
            
            <div class='content'>
                <h3>Order Details:</h3>
                <div class='order-detail'><strong>Order ID:</strong> #$order_id</div>
                <div class='order-detail'><strong>Amount Paid:</strong> KSh " . number_format($amount, 2) . "</div>
                <div class='order-detail'><strong>Tracking ID:</strong> $tracking_id</div>
                <div class='order-detail'><strong>Confirmation Code:</strong> $confirmation_code</div>
                <div class='order-detail'><strong>Order Status:</strong> Confirmed</div>
            </div>
            
            <p>We will notify you when your order ships. If you have any questions about your order, please contact our support team.</p>
            
            <div class='footer'>
                <p>Best regards,<br><strong>Lewvitec Team</strong></p>
                <p>Email: sales@lewvitec.com</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email to admin
    $admin_subject = "New Order Received - Order #$order_id";
    $admin_message = "
    <html>
    <head>
        <title>New Order Notification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #28a745; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0; }
            .order-detail { margin-bottom: 10px; }
            .order-detail strong { display: inline-block; width: 150px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Order Notification</h1>
            </div>
            <p>A new order has been successfully paid and requires processing.</p>
            
            <div class='content'>
                <h3>Order Details:</h3>
                <div class='order-detail'><strong>Order ID:</strong> #$order_id</div>
                <div class='order-detail'><strong>Customer Name:</strong> $customer_name</div>
                <div class='order-detail'><strong>Customer Email:</strong> $customer_email</div>
                <div class='order-detail'><strong>Amount Paid:</strong> KSh " . number_format($amount, 2) . "</div>
                <div class='order-detail'><strong>Tracking ID:</strong> $tracking_id</div>
                <div class='order-detail'><strong>Confirmation Code:</strong> $confirmation_code</div>
            </div>
            
            <p>Please process this order promptly.</p>
        </div>
    </body>
    </html>
    ";
    
    // Send email to customer
    if (!empty($customer_email)) {
        $customer_sent = mail($customer_email, $customer_subject, $customer_message, $headers);
        if (!$customer_sent) {
            file_put_contents('pesapal_callback_debug.log', "Failed to send email to customer: $customer_email" . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents('pesapal_callback_debug.log', "Successfully sent email to customer: $customer_email" . PHP_EOL, FILE_APPEND);
        }
    }
    
    // Send email to admin
    $admin_sent = mail('lewvitec@gmail.com', $admin_subject, $admin_message, $headers);
    if (!$admin_sent) {
        file_put_contents('pesapal_callback_debug.log', "Failed to send email to admin" . PHP_EOL, FILE_APPEND);
    } else {
        file_put_contents('pesapal_callback_debug.log', "Successfully sent email to admin" . PHP_EOL, FILE_APPEND);
    }
}
?>