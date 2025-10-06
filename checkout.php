<?php
session_start();
include('includes/config.php');
include('includes/pesapal-config.php');

// Check if user has items in cart
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['error'] = "Your cart is empty.";
    header("Location: my-cart.php");
    exit();
}

// Get user information based on login status
if (isset($_SESSION['id'])) {
    // Logged in user
    $user_id = $_SESSION['id'];
    $user_query = mysqli_query($con, "SELECT * FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_array($user_query);
    
    // Check if user has complete billing information
    $required_fields = ['firstname', 'lastname', 'email', 'phone', 'billingAddress', 'billingCity', 'billingState', 'billingPincode'];
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($user[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        $_SESSION['error'] = "Please complete your billing information before checkout. Missing: " . implode(', ', $missing_fields);
        header("Location: my-cart.php");
        exit();
    }
} else {
    // Guest user - get billing info from session
    if (!isset($_SESSION['guest_billing'])) {
        $_SESSION['error'] = "Please complete your billing information before checkout.";
        header("Location: my-cart.php");
        exit();
    }
    
    $guest_billing = $_SESSION['guest_billing'];
    
    // Check if guest has complete billing information
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'pincode'];
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($guest_billing[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        $_SESSION['error'] = "Please complete your billing information before checkout. Missing: " . implode(', ', $missing_fields);
        header("Location: my-cart.php");
        exit();
    }
    
    // Create user array with guest billing info for consistent usage
    $user = [
        'firstname' => $guest_billing['first_name'],
        'lastname' => $guest_billing['last_name'],
        'email' => $guest_billing['email'],
        'phone' => $guest_billing['phone'],
        'billingAddress' => $guest_billing['address'],
        'billingCity' => $guest_billing['city'],
        'billingState' => $guest_billing['state'],
        'billingPincode' => $guest_billing['pincode']
    ];
}

// Calculate total based on cart structure
$total = 0;
$cart_items = [];

// Get product details from database
foreach ($cart as $product_id => $item) {
    $product_query = mysqli_query($con, "SELECT * FROM products WHERE id='$product_id'");
    if ($product_query && mysqli_num_rows($product_query) > 0) {
        $product = mysqli_fetch_array($product_query);
        $subtotal = $item['quantity'] * $product['productPrice'] + $product['shippingCharge'];
        $total += $subtotal;
        
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['productName'],
            'price' => $product['productPrice'],
            'quantity' => $item['quantity'],
            'shipping' => $product['shippingCharge'],
            'subtotal' => $subtotal,
            'image' => $product['productImage1']
        ];
    }
}

// Store total in session for later use
$_SESSION['cart_total'] = $total;
$_SESSION['cart_items'] = $cart_items;

// Process checkout when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0; // 0 for guest users
    $amount = $total;
    $payment_method = 'pesapal';
    
    // Create order in database
    $productIds = array_column($cart_items, 'id');
    $quantities = array_column($cart_items, 'quantity');
    
    $productIdsStr = implode(',', $productIds);
    $quantitiesStr = implode(',', $quantities);
    
    // For guest users, store guest email in a separate field
    $email_field = isset($_SESSION['id']) ? $user['email'] : $user['email'] . ' (Guest)';
    
    $order_query = mysqli_query($con, "INSERT INTO orders (userId, productId, quantity, paymentMethod, amount, billing_email, billing_phone, billing_first_name, billing_last_name, city, county, postal_code, is_guest) 
                                      VALUES ('$userId', '$productIdsStr', '$quantitiesStr', '$payment_method', '$amount', '".$user['email']."', '".$user['phone']."', '".$user['firstname']."', '".$user['lastname']."', '".$user['billingCity']."', '".$user['billingState']."', '".$user['billingPincode']."', '".(isset($_SESSION['id']) ? 0 : 1)."')");

    if ($order_query) {
        $order_id = mysqli_insert_id($con);
        
        // Store order ID in session for PesaPal
        $_SESSION['order_id'] = $order_id;
        
        // Store billing information in session for PesaPal
        $_SESSION['billing_info'] = [
            'email_address' => $user['email'],
            'phone_number' => $user['phone'],
            'country_code' => 'KE',
            'first_name' => $user['firstname'],
            'last_name' => $user['lastname'],
            'line_1' => $user['billingAddress'],
            'city' => $user['billingCity'],
            'state' => $user['billingState'],
            'postal_code' => $user['billingPincode']
        ];
        
        // Store user type for later reference
        $_SESSION['user_type'] = isset($_SESSION['id']) ? 'registered' : 'guest';
        
        // Redirect to PesaPal payment request
        header("Location: pesapal_request.php");
        exit();
    } else {
        $_SESSION['error'] = "Error creating order: " . mysqli_error($con);
        header("Location: checkout.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Complete Your Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, #3e63c9 0%, #2a4fa3 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
        }
        
        .breadcrumb {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .breadcrumb a {
            color: #3e63c9;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .checkout-container {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }
        
        @media (max-width: 992px) {
            .checkout-container {
                flex-direction: column;
            }
        }
        
        .order-summary {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
        
        .payment-section {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
        
        .billing-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        h2 {
            color: #2a4fa3;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f6;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
            color: #495057;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eaecef;
        }
        
        .grand-total {
            font-size: 20px;
            font-weight: 700;
            color: #2a4fa3;
        }
        
        .payment-options {
            margin: 25px 0;
        }
        
        .payment-card {
            background: linear-gradient(135deg, #3e63c9 0%, #2a4fa3 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .payment-card i {
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .payment-card h3 {
            margin-bottom: 10px;
        }
        
        .btn-pay {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-pay:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .security-note {
            text-align: center;
            margin-top: 15px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .security-note i {
            color: #3e63c9;
            margin-right: 5px;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
            vertical-align: middle;
        }
        
        footer {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3e63c9;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .billing-info {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #3e63c9;
        }
        
        .billing-info h4 {
            color: #2a4fa3;
            margin-bottom: 10px;
        }
        
        .billing-info p {
            margin: 5px 0;
            color: #555;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }
        
        .user-type-badge {
            display: inline-block;
            background: #ffc107;
            color: #856404;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">Lewvitec</div>
            <div class="user-info">
                <i class="fas fa-user"></i> 
                <?php 
                if (isset($_SESSION['username'])) {
                    echo htmlspecialchars($_SESSION['username']);
                } else {
                    echo 'Guest';
                    echo '<span class="user-type-badge">Guest Checkout</span>';
                }
                ?>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; <a href="my-cart.php">Cart</a> &gt; Checkout
        </div>
        
        <h1>Checkout <?php echo !isset($_SESSION['id']) ? '<span class="user-type-badge">Guest Checkout</span>' : ''; ?></h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="checkout-container">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Shipping</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <?php 
                                $image_path = "admin/productimages/{$item['id']}/{$item['image']}";
                                if (file_exists($image_path)) {
                                    echo '<img src="' . $image_path . '" alt="' . htmlspecialchars($item['name']) . '" class="product-image">';
                                }
                                echo htmlspecialchars($item['name']); 
                                ?>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>Kes. <?php echo number_format($item['price'], 2); ?></td>
                            <td>Kes. <?php echo number_format($item['shipping'], 2); ?></td>
                            <td>Kes. <?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: 700;">Grand Total:</td>
                            <td class="grand-total">Kes. <?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="billing-section">
                    <h3>Billing Information</h3>
                    <div class="billing-info">
                        <h4>PesaPal Payment Details</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['billingAddress']); ?></p>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($user['billingCity']); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($user['billingState']); ?></p>
                        <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($user['billingPincode']); ?></p>
                        <p><strong>Country:</strong> Kenya</p>
                        <?php if (!isset($_SESSION['id'])): ?>
                        <p><strong>Account Type:</strong> <span class="user-type-badge">Guest Checkout</span></p>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted"><i class="fas fa-info-circle"></i> This information will be used for PesaPal payment processing.</p>
                </div>
            </div>
            
            <div class="payment-section">
                <h2>Payment Method</h2>
                
                <div class="payment-options">
                    <div class="payment-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Pay with PesaPal</h3>
                        <p>Secure payment with Kenya's leading payment gateway</p>
                        <p>Supports M-Pesa, Airtel Money, Visa, and MasterCard</p>
                    </div>
                    
                    <form method="POST" id="checkout-form">
                        <button type="submit" class="btn-pay">
                            <i class="fas fa-lock"></i> PAY NOW - Kes. <?php echo number_format($total, 2); ?>
                        </button>
                    </form>
                    
                    <div class="security-note">
                        <i class="fas fa-shield-alt"></i> Your payment information is encrypted and secure
                    </div>
                </div>
                
                <div class="loading" id="loading">
                    <div class="loading-spinner"></div>
                    <p>Redirecting to PesaPal...</p>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Lewvitec. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('checkout-form').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
        });
    </script>
</body>
</html>