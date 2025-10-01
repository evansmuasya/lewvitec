<?php
session_start();
require_once('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .error-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
        }
        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include('includes/top-header.php'); ?>
<?php include('includes/main-header.php'); ?>

<div class="container">
    <div class="error-container">
        <i class="fas fa-times-circle error-icon"></i>
        <h2>Payment Failed</h2>
        <p>We're sorry, but your payment could not be processed.</p>
        <p>This could be due to insufficient funds, incorrect payment details, or a temporary issue with the payment gateway.</p>
        
        <div class="mt-4">
            <a href="checkout.php" class="btn btn-primary">Try Again</a>
            <a href="contact.php" class="btn btn-outline-primary">Contact Support</a>
            <a href="index.php" class="btn btn-link">Continue Shopping</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>