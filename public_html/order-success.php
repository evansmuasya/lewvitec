<?php
// order-success.php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$order_query = mysqli_query($con, "SELECT * FROM orders WHERE id='$order_id'");
$order = mysqli_fetch_array($order_query);

if (!$order) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-container {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin: 50px auto;
            max-width: 600px;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body class="cnt-home">
<header class="header-style-1">
    <?php include('includes/top-header.php');?>
    <?php include('includes/main-header.php');?>
    <?php include('includes/menu-bar.php');?>
</header>

<div class="body-content outer-top-xs">
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Order Confirmed!</h2>
            <p>Thank you for your purchase. Your order has been received and is being processed.</p>
            
            <div class="order-details">
                <h4>Order Details</h4>
                <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['orderDate'])); ?></p>
                <p><strong>Total Amount:</strong> Kes. <?php echo number_format($order['amount'], 2); ?></p>
                <p><strong>Status:</strong> <?php echo $order['orderStatus']; ?></p>
            </div>
            
            <p>You will receive an email confirmation shortly with your order details.</p>
            
            <div class="action-buttons" style="margin-top: 30px;">
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                <a href="order-details.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-primary">View Order Details</a>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>
</body>
</html>