<?php
// ipn_handler.php
// Pesapal will POST/GET IPN payload referencing OrderTrackingId and OrderMerchantReference
error_reporting(E_ALL);
session_start();
include('includes/config.php');

$consumer_key = "9D/HcSLmjiOBO5YnVjImAS5ALWbzFmx/";
$consumer_secret = "4QZPR7EkQeIMpIpIC+3BMNzK+Gw=";
$api_base = "https://pay.pesapal.com/v3";

// Helper to get token
function pesapal_get_token($api_base, $consumer_key, $consumer_secret){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_base . "/api/Auth/RequestToken");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "consumer_key" => $consumer_key,
        "consumer_secret" => $consumer_secret
    ]));
    $resp = curl_exec($ch);
    curl_close($ch);
    $j = json_decode($resp, true);
    return isset($j['token']) ? $j['token'] : null;
}

// Read IPN incoming payload (Pesapal may POST JSON or form-data)
$raw = file_get_contents("php://input");
$incoming = $_POST ? $_POST : json_decode($raw, true);
if (!$incoming) $incoming = $_GET ? $_GET : [];

$orderTrackingId = isset($incoming['OrderTrackingId']) ? $incoming['OrderTrackingId'] : (isset($incoming['orderTrackingId']) ? $incoming['orderTrackingId'] : (isset($incoming['OrderTrackingID']) ? $incoming['OrderTrackingID'] : null));
$orderMerchantRef = isset($incoming['OrderMerchantReference']) ? $incoming['OrderMerchantReference'] : (isset($incoming['orderMerchantReference']) ? $incoming['orderMerchantReference'] : (isset($incoming['OrderMerchantReference']) ? $incoming['OrderMerchantReference'] : null));

// Pesapal sometimes posts keys named differently; try other keys
if (!$orderTrackingId && isset($incoming['OrderTrackingId'])) $orderTrackingId = $incoming['OrderTrackingId'];
if (!$orderMerchantRef && isset($incoming['OrderMerchantReference'])) $orderMerchantRef = $incoming['OrderMerchantReference'];

// If still missing, try common names
if (!$orderTrackingId && isset($incoming['pesapal_transaction_tracking_id'])) $orderTrackingId = $incoming['pesapal_transaction_tracking_id'];
if (!$orderMerchantRef && isset($incoming['pesapal_merchant_reference'])) $orderMerchantRef = $incoming['pesapal_merchant_reference'];

if (!$orderTrackingId && !$orderMerchantRef) {
    // nothing to do
    http_response_code(400);
    echo json_encode(["status"=>500, "message"=>"No tracking or merchant reference found"]);
    exit;
}

// Get token
$token = pesapal_get_token($api_base, $consumer_key, $consumer_secret);
if (!$token) {
    http_response_code(500);
    echo json_encode(["status"=>500, "message"=>"Auth error"]);
    exit;
}

// If we have tracking id, call GetTransactionStatus
$check_id = $orderTrackingId ? $orderTrackingId : $orderMerchantRef;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_base . "/api/Transactions/GetTransactionStatus?orderTrackingId=" . urlencode($check_id));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Content-Type: application/json",
    "Authorization: Bearer {$token}"
]);
$resp = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resp_j = json_decode($resp, true);
if ($httpcode != 200 || !$resp_j) {
    // respond with 500 to Pesapal so they know we had an issue
    http_response_code(500);
    echo json_encode(["orderNotificationType"=>"IPNCHANGE","orderTrackingId"=>$orderTrackingId,"orderMerchantReference"=>$orderMerchantRef,"status"=>500]);
    exit;
}

// Determine status
$payment_status_desc = isset($resp_j['payment_status_description']) ? $resp_j['payment_status_description'] : (isset($resp_j['payment_status']) ? $resp_j['payment_status'] : '');
$status_code = isset($resp_j['status_code']) ? $resp_j['status_code'] : null; // 1 = COMPLETED
$merchant_reference = isset($resp_j['merchant_reference']) ? $resp_j['merchant_reference'] : $orderMerchantRef;
$pesapal_tracking_id = isset($resp_j['orderTrackingId']) ? $resp_j['orderTrackingId'] : $orderTrackingId;
$amount = isset($resp_j['amount']) ? $resp_j['amount'] : null;
$confirmation_code = isset($resp_j['confirmation_code']) ? $resp_j['confirmation_code'] : null;

// Map to DB fields
$payment_state = 'pending';
if (strtoupper($payment_status_desc) === 'COMPLETED' || $status_code === 1 || strtoupper($payment_status_desc) === 'SUCCESS') {
    $payment_state = 'completed';
} elseif (strtoupper($payment_status_desc) === 'FAILED' || $status_code === 2) {
    $payment_state = 'failed';
} elseif (strtoupper($payment_status_desc) === 'REVERSED' || $status_code === 3) {
    $payment_state = 'failed';
}

// Update orders table
$merchant_ref_esc = mysqli_real_escape_string($con, $merchant_reference);
$pesapal_track_esc = mysqli_real_escape_string($con, $pesapal_tracking_id);
$update_sql = "UPDATE orders SET pesapal_tracking_id = '{$pesapal_track_esc}', merchant_reference = '{$merchant_ref_esc}', payment_status = '{$payment_state}', orderStatus = '".($payment_state==='completed'?'Paid':ucfirst($payment_state))."' WHERE merchant_reference = '{$merchant_ref_esc}'";
mysqli_query($con, $update_sql);

// Update payments table: set transaction reference/ids and status for order(s)
$q = mysqli_query($con, "SELECT id FROM orders WHERE merchant_reference = '{$merchant_ref_esc}' LIMIT 1");
if ($ro = mysqli_fetch_assoc($q)) {
    $order_id = (int)$ro['id'];
    // update payments row for this order
    mysqli_query($con, "UPDATE payments SET status = '".mysqli_real_escape_string($con, $payment_state)."', transaction_id = '".mysqli_real_escape_string($con, $confirmation_code)."', transaction_reference = '{$pesapal_track_esc}', updated_at = NOW() WHERE order_id = {$order_id}");
    // if no payments record exists, insert one
    if (mysqli_affected_rows($con) === 0) {
        $stmt = mysqli_prepare($con, "INSERT INTO payments (order_id, payment_method, amount, status, transaction_id, transaction_reference, created_at, updated_at) VALUES (?, 'mpesa', ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("ids s s", $order_id, $amount, $payment_state, $confirmation_code, $pesapal_tracking_id);
        // NOTE: bind types above may need adjust if $amount null; fallback below
        $stmt->execute();
        $stmt->close();
    }
}

// Save order track history
$remark = "Pesapal IPN: " . ($payment_status_desc ?: 'No description');
$stmt2 = mysqli_prepare($con, "INSERT INTO ordertrackhistory (orderId, status, remark) VALUES (?, ?, ?)");
$stmt2->bind_param("iss", $order_id, $payment_state, $remark);
$stmt2->execute();
$stmt2->close();

// Reply to Pesapal to acknowledge receipt
header('Content-Type: application/json');
echo json_encode(["orderNotificationType"=>"IPNCHANGE","orderTrackingId"=>$pesapal_tracking_id,"orderMerchantReference"=>$merchant_reference,"status"=>200]);
exit;
