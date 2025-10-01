<?php
include('includes/config.php');
$order_id = $_GET['order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Payment Successful</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3e63c9;
            --secondary-color: #f8f9fa;
            --success-color: #28a745;
            --text-color: #333;
            --light-text: #6c757d;
            --border-color: #eaeaea;
        }
        
        body { 
            font-family: 'Roboto', sans-serif; 
            background-color: #f9fafb; 
            padding-top: 20px;
        }
        
        .status-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--success-color);
        }
        
        .status-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-color);
        }
        
        .status-message {
            font-size: 1.2rem;
            color: var(--light-text);
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .order-id {
            font-weight: 600;
            color: var(--primary-color);
            background-color: var(--secondary-color);
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
        }
        
        .btn-primary { 
            background: var(--primary-color); 
            border-color: var(--primary-color); 
            padding: 12px 25px; 
            font-weight: 500; 
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            color: white;
            margin-top: 10px;
        }
        
        .btn-primary:hover {
            background: #2a4fa3;
            border-color: #2a4fa3;
            color: white;
        }
        
        .order-details {
            background-color: var(--secondary-color);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-container">
            <div class="status-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="status-title">Payment Successful!</h1>
            <p class="status-message">Your order has been paid successfully and is being processed.</p>
            
            <div class="order-id">Order #<?php echo htmlspecialchars($order_id); ?></div>
            
            <div class="order-details">
                <div class="detail-row">
                    <span>Order Status:</span>
                    <span style="color: var(--success-color); font-weight: 600;">Confirmed</span>
                </div>
                <div class="detail-row">
                    <span>Payment Method:</span>
                    <span>PesaPal</span>
                </div>
                <div class="detail-row">
                    <span>Order Date:</span>
                    <span><?php echo date("F j, Y, g:i a"); ?></span>
                </div>
            </div>
            
            <div class="actions">
                <a href="index.php" class="btn-primary">Continue Shopping</a>
                <a href="order-details.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn-primary" style="background: var(--light-text); border-color: var(--light-text);">View Order Details</a>
            </div>
        </div>
    </div>
</body>
</html>