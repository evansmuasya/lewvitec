<?php
session_start();
require_once('includes/config.php');

$order_ref = $_GET['order'] ?? '';
$order = null;

if (!empty($order_ref)) {
    $order_query = mysqli_query($con, 
        "SELECT o.*, u.name, u.email 
         FROM orders o 
         JOIN users u ON o.userId = u.id 
         WHERE o.merchant_reference = '$order_ref'");
    $order = mysqli_fetch_array($order_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include('includes/top-header.php'); ?>
<?php include('includes/main-header.php'); ?>

<div class="container">
    <div class="success-container">
        <i class="fas fa-check-circle success-icon"></i>
        <h2>Payment Successful!</h2>
        <p>Thank you for your order. Your payment has been processed successfully.</p>
        
        <?php if ($order): ?>
        <div class="order-details">
            <h4>Order Details</h4>
            <p><strong>Order ID:</strong> <?php echo $order['merchant_reference']; ?></p>
            <p><strong>Amount Paid:</strong> Kes. <?php echo number_format($order['amount'], 2); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <p>We've sent a confirmation email to your registered email address.</p>
            <p>You can track your order in your account dashboard.</p>
        </div>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <a href="my-orders.php" class="btn btn-outline-primary">View My Orders</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>