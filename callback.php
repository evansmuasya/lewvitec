<?php
// payment-callback.php
session_start();
include('includes/config.php');
include('includes/pesapal-config.php');
require_once 'includes/pesapal/Pesapal.php';
require_once 'includes/pesapal/Curl.php';

use BNjunge\PesapalCookout\Pesapal;

// Get the order tracking ID from PesaPal
$pesapalTrackingId = $_GET['OrderTrackingId'] ?? '';
$orderMerchantReference = $_GET['OrderMerchantReference'] ?? '';

if (!empty($pesapalTrackingId) && !empty($orderMerchantReference)) {
    try {
        // Use the PesaPal class to verify transaction status
        $transactionStatus = Pesapal::transactionStatus($pesapalTrackingId);
        
        // Handle object response
        if ($transactionStatus->success) {
            $status = $transactionStatus->message->payment_status_description;
            
            // Update order with PesaPal tracking ID and status
            $updateOrder = mysqli_query($con, 
                "UPDATE orders SET 
                 pesapal_tracking_id = '$pesapalTrackingId',
                 orderStatus = '$status',
                 payment_status = 'completed'
                 WHERE merchant_reference = '$orderMerchantReference'");
            
            // Update payment record
            $updatePayment = mysqli_query($con,
                "UPDATE payments SET 
                 status = 'completed',
                 transaction_id = '$pesapalTrackingId'
                 WHERE order_id = (SELECT id FROM orders WHERE merchant_reference = '$orderMerchantReference')");
            
            // Redirect to order confirmation page
            header("Location: order-confirmation.php?order_id=" . $orderMerchantReference . "&status=success");
            exit();
        } else {
            // Payment failed or pending
            header("Location: order-confirmation.php?order_id=" . $orderMerchantReference . "&status=failed");
            exit();
        }
    } catch (Exception $e) {
        error_log("PesaPal callback error: " . $e->getMessage());
        header("Location: order-confirmation.php?order_id=" . $orderMerchantReference . "&status=error");
        exit();
    }
} else {
    // Handle error case
    header("Location: payment-failed.php");
    exit();
}
?>
