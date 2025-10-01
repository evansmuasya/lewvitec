<?php
$order_id = $_GET['order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Payment Failed</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3e63c9;
            --secondary-color: #f8f9fa;
            --danger-color: #dc3545;
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
            color: var(--danger-color);
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
        
        .troubleshooting {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: left;
        }
        
        .troubleshooting h4 {
            margin-top: 0;
            color: #856404;
        }
        
        .troubleshooting ul {
            padding-left: 20px;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-container">
            <div class="status-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h1 class="status-title">Payment Failed</h1>
            <p class="status-message">Unfortunately, your payment was not successful. Please try again.</p>
            
            <div class="order-id">Order #<?php echo htmlspecialchars($order_id); ?></div>
            
            <div class="troubleshooting">
                <h4><i class="fas fa-lightbulb"></i> Troubleshooting Tips</h4>
                <ul>
                    <li>Check that your payment details are correct</li>
                    <li>Ensure you have sufficient funds in your account</li>
                    <li>Try using a different payment method</li>
                    <li>Contact your bank if the issue persists</li>
                </ul>
            </div>
            
            <div class="actions">
                <a href="checkout.php" class="btn-primary">Try Again</a>
                <a href="index.php" class="btn-primary" style="background: var(--light-text); border-color: var(--light-text);">Continue Shopping</a>
            </div>
        </div>
    </div>
</body>
</html>