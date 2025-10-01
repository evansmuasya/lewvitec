<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if user is logged in
if(strlen($_SESSION['login'])==0) {   
    header('location:login.php');
    exit();
}

// Check if cart is empty
if(empty($_SESSION['cart'])) {
    header('location:my-cart.php');
    exit();
}

// --- Safaricom Daraja Sandbox Credentials ---
define('CONSUMER_KEY', 'dKkpxFv6GMXUNlat2S1LVndVhTmuOqgfIyjwJAX7SESDjLbp');
define('CONSUMER_SECRET', 'OVPA6D34J0vff36M1C33TBaACpfI7BmJaanoAaVPAZWWm1Vp4TcoosM7PPdkhaVt');
define('SHORTCODE', '5485639'); // Default test paybill
define('PASSKEY', 'YOUR_PASSKEY'); // Sandbox passkey
define('ENV_BASE', 'https://sandbox.safaricom.co.ke');

// 1) Function to get access token
function getAccessToken() {
    $url = ENV_BASE . '/oauth/v1/generate?grant_type=client_credentials';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode(CONSUMER_KEY . ':' . CONSUMER_SECRET)
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    $obj = json_decode($res, true);
    return $obj['access_token'] ?? null;
}

// 2) Function to initiate STK Push
function stkPush($phoneNumber, $amount, $accountRef, $callbackUrl) {
    $token = getAccessToken();
    if (!$token) return ['error' => 'Failed to get token'];

    $timestamp = date('YmdHis');
    $password = base64_encode(SHORTCODE . PASSKEY . $timestamp);

    $body = [
        'BusinessShortCode' => SHORTCODE,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => (int)$amount,
        'PartyA' => $phoneNumber,
        'PartyB' => SHORTCODE,
        'PhoneNumber' => $phoneNumber,
        'CallBackURL' => $callbackUrl,
        'AccountReference' => $accountRef,
        'TransactionDesc' => 'Payment for order ' . $accountRef
    ];

    $url = ENV_BASE . '/mpesa/stkpush/v1/processrequest';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Calculate total amount from cart
$total = 0;
if(!empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $id => $qty) {
        $sql = mysqli_query($con,"SELECT productPrice FROM products WHERE id='$id'");
        $sql = mysqli_query($con,"SELECT shippingCharge FROM products WHERE id='$id'");
        $row = mysqli_fetch_array($sql);
        $total += $row['productPrice'] * $qty['quantity'] + $row['shippingCharge'];
    }
}

// Process payment when form is submitted
if(isset($_POST['submit'])) {
    $user_id = $_SESSION['id'];
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    
    // Format phone number to 2547XXXXXXXX format if needed
    if (substr($phone, 0, 1) === '0') {
        $phone = '254' . substr($phone, 1);
    } elseif (substr($phone, 0, 3) !== '254') {
        $phone = '254' . $phone;
    }
    
    // Validate phone number
    if (strlen($phone) !== 12) {
        $error = "Please enter a valid phone number (e.g., 07XX XXX XXX)";
    } else {
        // --- Save order in DB ---
        foreach($_SESSION['cart'] as $id => $qty) {
            mysqli_query($con,"INSERT INTO orders(userId, productId, quantity) VALUES('$user_id', '$id', '".$qty['quantity']."')");
        }

        // --- Initiate MPesa STK Push ---
        $accountRef = "ORDER" . time(); // Unique reference
        $callbackUrl = "https://yourdomain.co.ke/mpesa/callback.php"; // Must be publicly accessible

        $mpesaResponse = stkPush($phone, $total, $accountRef, $callbackUrl);

        if (isset($mpesaResponse['error'])) {
            $error = "Failed to initiate payment: " . $mpesaResponse['error'];
        } elseif (isset($mpesaResponse['ResponseCode']) && $mpesaResponse['ResponseCode'] == '0') {
            $success = true;
            $message = "An M-Pesa payment prompt has been sent to your phone for KES " . number_format($total, 2) . ". Please enter your M-Pesa PIN to complete the payment.";
            
            // Clear cart after successful payment initiation
            unset($_SESSION['cart']);
        } else {
            $error = "Failed to initiate payment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment | Checkout</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #3e63c9;
            --secondary-color: #f8f9fa;
            --accent-color: #ff6b6b;
            --text-color: #333;
            --light-text: #6c757d;
            --border-color: #eaeaea;
        }
        
        body { 
            font-family: 'Roboto', sans-serif; 
            background-color: #f9fafb; 
        }
        
        .breadcrumb { 
            background: white; 
            padding: 15px 0; 
            margin-bottom: 20px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .payment-container { 
            background: white; 
            border-radius: 10px; 
            padding: 30px; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .mpesa-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .mpesa-logo i {
            font-size: 60px;
            color: #00A650; /* M-Pesa green */
        }
        
        .order-summary {
            background-color: var(--secondary-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            padding: 12px 15px;
            height: auto;
        }
        
        .btn-primary { 
            background: var(--primary-color); 
            border-color: var(--primary-color); 
            padding: 12px 25px; 
            font-weight: 500; 
            width: 100%;
        }
        
        .btn-primary:hover {
            background: #2a4fa3;
            border-color: #2a4fa3;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            text-align: center;
            margin: 20px 0;
        }
        
        .instructions {
            background-color: #f8fff8;
            border-left: 4px solid #00A650;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .success-alert {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .error-alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body class="cnt-home">
<header class="header-style-1">  
    <?php include('includes/top-header.php');?>  
    <?php include('includes/main-header.php');?>  
    <?php include('includes/menu-bar.php');?>  
</header>

<div class="breadcrumb">  
    <div class="container">  
        <div class="breadcrumb-inner">  
            <ul class="list-inline list-unstyled">  
                <li><a href="index.php">Home</a></li>  
                <li><a href="my-cart.php">Cart</a></li>
                <li><a href="checkout.php">Checkout</a></li>
                <li class='active'>M-Pesa Payment</li>  
            </ul>  
        </div>  
    </div>  
</div>

<div class="body-content outer-top-xs">  
    <div class="container">  
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="payment-container">
                    <div class="mpesa-logo">
                        <i class="fas fa-mobile-alt"></i>
                        <h2>M-Pesa Payment</h2>
                    </div>
                    
                    <?php if(isset($success) && $success): ?>
                        <div class="success-alert">
                            <i class="fas fa-check-circle"></i> 
                            <?php echo $message; ?>
                        </div>
                        
                        <div class="text-center">
                            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    <?php else: ?>
                        <?php if(isset($error)): ?>
                            <div class="error-alert">
                                <i class="fas fa-exclamation-circle"></i> 
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="order-summary">
                            <h4>Order Summary</h4>
                            <?php
                    $pdtid = array();  
                    $sql = "SELECT * FROM products WHERE id IN(";  
                    foreach($_SESSION['cart'] as $id => $value) { 
                        $sql .= $id . ","; 
                    }  
                    $sql = substr($sql, 0, -1) . ") ORDER BY id ASC";  
                    $query = mysqli_query($con, $sql);  
                    $totalprice = 0; 
                    
                    if(!empty($query)) {  
                        while($row = mysqli_fetch_array($query)) {  
                            $quantity = $_SESSION['cart'][$row['id']]['quantity'];  
                            $subtotal = $quantity * $row['productPrice'] + $row['shippingCharge'];  
                            $totalprice += $subtotal;  
                    ?>
                                    <div class="order-item">
                                        <div>
                                            <strong><?php echo $row['productName']; ?></strong>
                                            <div>Qty: <?php echo $qty['quantity']; ?></div>
                                        </div>
                                        <div>KES <?php echo number_format($subtotal, 2); ?></div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <div class="total-amount">
                                Total: KES <?php echo number_format($totalprice, 2); ?>
                            </div>
                        </div>
                        
                        <form method="post" id="mpesaForm">
                            <div class="form-group">
                                <label for="phone">M-Pesa Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g., 07XX XXX XXX" required value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>">
                                <small class="form-text text-muted">Enter your M-Pesa registered phone number</small>
                            </div>
                            
                            <div class="instructions">
                                <h5><i class="fas fa-info-circle"></i> Payment Instructions</h5>
                                <ol>
                                    <li>Enter your M-Pesa registered phone number</li>
                                    <li>Click "Initiate Payment" button</li>
                                    <li>Check your phone for an M-Pesa prompt</li>
                                    <li>Enter your M-Pesa PIN to complete payment</li>
                                </ol>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Initiate Payment
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    // Format phone number as user types
    $('#phone').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            if (value.startsWith('254')) {
                value = value.substring(3);
            }
            if (value.length <= 9) {
                $(this).val(value.replace(/(\d{3})(?=\d)/g, "$1 "));
            }
        }
    });
    
    // Validate form before submission
    $('#mpesaForm').on('submit', function() {
        var phone = $('#phone').val().replace(/\s/g, '');
        if (phone.length !== 9 || isNaN(phone)) {
            alert('Please enter a valid 9-digit phone number (without 254 prefix)');
            return false;
        }
        return true;
    });
});
</script>
</body>
</html>