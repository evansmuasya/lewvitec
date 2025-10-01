<?php
$order_id = $_GET['order_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Payment Pending</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #3e63c9;
            --secondary-color: #f8f9fa;
            --warning-color: #ffc107;
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
            color: var(--warning-color);
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
        
        .processing-info {
            background-color: #e7f3ff;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: left;
        }
        
        .processing-info h4 {
            margin-top: 0;
            color: var(--primary-color);
        }
        
        .progress-container {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin: 20px 0;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            width: 60%;
            background-color: var(--warning-color);
            border-radius: 4px;
            animation: progressAnimation 1.5s infinite alternate;
        }
        
        @keyframes progressAnimation {
            0% { width: 60%; }
            100% { width: 70%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-container">
            <div class="status-icon">
                <i class="fas fa-clock"></i>
            </div>
            <h1 class="status-title">Payment Processing</h1>
            <p class="status-message">Your payment is being processed. We'll update you once confirmation is received.</p>
            
            <div class="order-id">Order #<?php echo htmlspecialchars($order_id); ?></div>
            
            <div class="progress-container">
                <div class="progress-bar"></div>
            </div>
            
            <div class="processing-info">
                <h4><i class="fas fa-info-circle"></i> What's happening?</h4>
                <p>Your payment has been received and is being verified by our payment processor. This process usually takes a few minutes but can occasionally take longer depending on your payment method.</p>
                <p>You will receive an email confirmation once your payment is fully processed.</p>
            </div>
            
            <div class="actions">
                <a href="index.php" class="btn-primary">Return to Home</a>
                <a href="contact.php" class="btn-primary" style="background: var(--light-text); border-color: var(--light-text);">Contact Support</a>
            </div>
        </div>
    </div>
</body>
</html>
