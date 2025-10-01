<?php
// payment-callback.php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/pesapal-config.php');

// Get parameters from PesaPal callback
$order_tracking_id = $_GET['OrderTrackingId'] ?? '';
$order_merchant_reference = $_GET['OrderMerchantReference'] ?? '';

if (!empty($order_tracking_id) && !empty($order_merchant_reference)) {
    // Check payment status
    $payment_status = checkPesapalPaymentStatus($order_tracking_id);
    
    if ($payment_status) {
        $status = $payment_status['status_code'] ?? '';
        $payment_method = $payment_status['payment_method'] ?? '';
        
        // Update order in database
        $order_query = mysqli_query($con, "SELECT * FROM orders WHERE merchant_reference='$order_merchant_reference'");
        
        if (mysqli_num_rows($order_query) > 0) {
            $order = mysqli_fetch_array($order_query);
            $order_id = $order['id'];
            
            // Map PesaPal status to our system
            $order_status = 'Pending';
            $payment_status_db = 'pending';
            
            if ($status == '1') {
                $order_status = 'Confirmed';
                $payment_status_db = 'completed';
            } elseif ($status == '2') {
                $order_status = 'Cancelled';
                $payment_status_db = 'failed';
            }
            
            // Update order
            mysqli_query($con, 
                "UPDATE orders 
                 SET orderStatus='$order_status', payment_status='$payment_status_db', pesapal_tracking_id='$order_tracking_id'
                 WHERE id='$order_id'");
            
            // Insert into payments table
            mysqli_query($con,
                "INSERT INTO payments (order_id, payment_method, amount, status, transaction_id, transaction_reference)
                 VALUES ('$order_id', '$payment_method', '{$order['amount']}', '$payment_status_db', '$order_tracking_id', '$order_merchant_reference')");
            
            // Add to order track history
            mysqli_query($con,
                "INSERT INTO ordertrackhistory (orderId, status, remark)
                 VALUES ('$order_id', '$order_status', 'Payment processed via PesaPal')");
            
            // Clear cart if payment successful
            if ($status == '1') {
                unset($_SESSION['cart']);
            }
            
            // Redirect to appropriate page
            if ($status == '1') {
                header("Location: order-success.php?order_id=$order_id");
            } else {
                header("Location: order-failed.php?order_id=$order_id&reason=payment_failed");
            }
            exit();
        }
    }
}

// If we get here, something went wrong
header("Location: order-failed.php?reason=unknown");
exit();
?>