<?php
// callback_ipn.php
include('db.php');
include('pesapal_config.php');

// Get IPN notification (Pesapal will POST to this URL)
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data['order_tracking_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "failed", "message" => "Invalid IPN data"]);
    exit;
}

$order_tracking_id = $data['order_tracking_id'];   // Pesapal tracking ID
$merchant_reference = $data['merchant_reference']; // Your order ID
$status = $data['status'];                         // 'COMPLETED', 'FAILED', 'PENDING'

// Update orders table
$stmt = $conn->prepare("UPDATE orders SET pesapal_tracking_id=?, orderStatus=?, payment_status=? WHERE id=?");
$stmt->bind_param("sssi", $order_tracking_id, $status, $status, $merchant_reference);
$stmt->execute();

// Also update payments table
$stmt2 = $conn->prepare("INSERT INTO payments (order_id, payment_method, amount, status, transaction_id, transaction_reference) 
    VALUES (?, 'pesapal', ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE status=VALUES(status), transaction_id=VALUES(transaction_id), transaction_reference=VALUES(transaction_reference)");
$stmt2->bind_param("idsss", $merchant_reference, $data['amount'], $status, $order_tracking_id, $data['payment_method'] ?? '');
$stmt2->execute();

http_response_code(200);
echo json_encode(["status" => "ok", "message" => "IPN received"]);
?>
