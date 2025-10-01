<?php
include('includes/config.php');
include('pesapal_config.php');

// Read incoming IPN request
$input = file_get_contents("php://input");
$ipn_data = json_decode($input, true);

if (!$ipn_data || !isset($ipn_data['OrderTrackingId']) || !isset($ipn_data['OrderMerchantReference'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid IPN payload"]);
    exit;
}

$tracking_id = $ipn_data['OrderTrackingId'];
$merchant_reference = $ipn_data['OrderMerchantReference'];

// === STEP 1: Request Auth Token ===
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

$resp = curl_exec($ch);
$auth = json_decode($resp, true);
$token = $auth['token'] ?? '';
curl_close($ch);

if (!$token) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to get auth token"]);
    exit;
}

// === STEP 2: Query Transaction Status ===
$status_url = PESAPAL_BASE_URL . "/api/Transactions/GetTransactionStatus?orderTrackingId=" . urlencode($tracking_id);

$ch = curl_init($status_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

// === STEP 3: Extract values ===
$order_status_raw  = strtoupper($data['status'] ?? 'UNKNOWN');
$status_code       = $data['payment_status_code'] ?? null;
$status_desc       = strtoupper($data['payment_status_description'] ?? '');
$payment_method    = $data['payment_method'] ?? '';
$amount            = $data['amount'] ?? 0;
$confirmation_code = $data['confirmation_code'] ?? '';

// === STEP 4: Normalize orderStatus ===
if ($order_status_raw === "COMPLETED" || $order_status_raw === "SUCCESS" 
    || $status_desc === "COMPLETED" || $status_desc === "SUCCESS" 
    || $status_code == 200) {
    $final_status = "COMPLETED";
} elseif ($order_status_raw === "FAILED" || $status_desc === "FAILED" || $status_code == 400) {
    $final_status = "FAILED";
} else {
    $final_status = "PENDING";
}

// === STEP 5: Update Database ===
$order_id_res = mysqli_query($con, "SELECT id FROM orders WHERE merchant_reference = '" . mysqli_real_escape_string($con, $merchant_reference) . "'");
$order_row = mysqli_fetch_assoc($order_id_res);
$order_id = $order_row['id'] ?? null;

if ($order_id) {
    $query = "UPDATE orders 
              SET orderStatus=?, pesapal_tracking_id=?, payment_method=?, amount_paid=?, confirmation_code=? 
              WHERE id=?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", 
            $final_status, $tracking_id, $payment_method, $amount, $confirmation_code, $order_id
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        file_put_contents('pesapal_ipn_debug.log', "DB Error: " . mysqli_error($con) . PHP_EOL, FILE_APPEND);
    }
}

// === STEP 6: Respond to PesaPal (ACK) ===
http_response_code(200);
echo json_encode(["status" => "ok"]);

// === STEP 7: Log for debugging ===
$log_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tracking_id' => $tracking_id,
    'merchant_reference' => $merchant_reference,
    'normalized_status' => $final_status,
    'raw_response' => $data
];
file_put_contents('pesapal_ipn_debug.log', json_encode($log_data) . PHP_EOL, FILE_APPEND);
?>
