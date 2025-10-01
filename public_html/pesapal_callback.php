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
$order_id_res = mysqli_query($con, "SELECT id, productId, quantity FROM orders WHERE merchant_reference = '" . mysqli_real_escape_string($con, $merchant_reference) . "'");
$order_row    = mysqli_fetch_assoc($order_id_res);
$order_id     = $order_row['id'] ?? null;
$product_id   = $order_row['productId'] ?? null;
$quantity     = (int)($order_row['quantity'] ?? 0);

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

// === STEP 8: If payment successful, reduce stock ===
if ($success && $product_id && $quantity > 0) {
    $updateProduct = mysqli_prepare($con, "UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?");
    if ($updateProduct) {
        mysqli_stmt_bind_param($updateProduct, "ii", $quantity, $product_id);
        if (!mysqli_stmt_execute($updateProduct)) {
            file_put_contents('pesapal_callback_debug.log', "Stock Update Error: " . mysqli_stmt_error($updateProduct) . PHP_EOL, FILE_APPEND);
        }
        mysqli_stmt_close($updateProduct);
    }
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
?>
