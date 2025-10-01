<?php  
session_start();  
error_reporting(0);  
include('includes/config.php'); 
include('includes/pesapal-config.php');

// Update Cart Quantities  
if(isset($_POST['submit'])){     
    if(!empty($_SESSION['cart'])){     
        foreach($_POST['quantity'] as $key => $val){         
            if($val==0){             
                unset($_SESSION['cart'][$key]);         
            } else {             
                $_SESSION['cart'][$key]['quantity']=$val;          
            }     
        }         
        echo "<script>alert('Your Cart has been Updated');</script>";     
    } 
}  

// Remove Product from Cart  
if(isset($_POST['remove_code'])) {  
    if(!empty($_SESSION['cart'])){         
        foreach($_POST['remove_code'] as $key){                              
            unset($_SESSION['cart'][$key]);         
        }             
        echo "<script>alert('Your Cart has been Updated');</script>";     
    } 
}  

if (isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']);
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Get stock from DB
    $query = mysqli_query($con, "SELECT stockQuantity FROM products WHERE id='$id'");
    $row = mysqli_fetch_assoc($query);

    if ($row['stockQuantity'] >= $qty) {
        // Add to cart (or update cart session)
        $_SESSION['cart'][$id] = [
            "quantity" => $qty
        ];
    } else {
        echo "<script>alert('Sorry, only {$row['stockQuantity']} items left in stock.');</script>";
    }
}


// Combined Address Update (for logged in users)  
if(isset($_POST['address_update']) && isset($_SESSION['id'])) {  
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);  
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);  
    $email = mysqli_real_escape_string($con, $_POST['email']);  
    $phone = mysqli_real_escape_string($con, $_POST['phone']);  
    $address = mysqli_real_escape_string($con, $_POST['address']);  
    $city = mysqli_real_escape_string($con, $_POST['city']);  
    $state = mysqli_real_escape_string($con, $_POST['state']);  
    $pincode = mysqli_real_escape_string($con, $_POST['pincode']);  

    $query = mysqli_query($con, "UPDATE users SET  
        firstname='$first_name', lastname='$last_name', email='$email', phone='$phone',
        billingAddress='$address', billingCity='$city', billingState='$state', billingPincode='$pincode',  
        shippingAddress='$address', shippingCity='$city', shippingState='$state', shippingPincode='$pincode'  
        WHERE id='".$_SESSION['id']."'");  

    if($query) {  
        echo "<script>alert('Billing information has been updated successfully');</script>";  
        // Update session variables
        $_SESSION['username'] = $first_name . ' ' . $last_name;
        $_SESSION['login'] = $email;
    } else {
        echo "<script>alert('Error updating billing information: " . mysqli_error($con) . "');</script>";
    }  
}  

// Save billing info to session for guest users
if(isset($_POST['save_billing_info'])) {
    $_SESSION['guest_billing'] = array(
        'first_name' => mysqli_real_escape_string($con, $_POST['first_name']),
        'last_name' => mysqli_real_escape_string($con, $_POST['last_name']),
        'email' => mysqli_real_escape_string($con, $_POST['email']),
        'phone' => mysqli_real_escape_string($con, $_POST['phone']),
        'address' => mysqli_real_escape_string($con, $_POST['address']),
        'city' => mysqli_real_escape_string($con, $_POST['city']),
        'state' => mysqli_real_escape_string($con, $_POST['state']),
        'pincode' => mysqli_real_escape_string($con, $_POST['pincode'])
    );
    echo "<script>alert('Billing information has been saved');</script>";
}

// Calculate cart total and items for session
if(!empty($_SESSION['cart'])) {
    $pdtid = array();  
    $sql = "SELECT * FROM products WHERE id IN(";  
    foreach($_SESSION['cart'] as $id => $value) { 
        $sql .= $id . ","; 
    }  
    $sql = substr($sql, 0, -1) . ") ORDER BY id ASC";  
    $query = mysqli_query($con, $sql);  
    $totalprice = 0; 
    $totalqunty = 0;  
    
    if(!empty($query)) {  
        while($row = mysqli_fetch_array($query)) {  
            $quantity = $_SESSION['cart'][$row['id']]['quantity'];  
            $subtotal = $quantity * $row['productPrice'] + $row['shippingCharge'];  
            $totalprice += $subtotal;  
            $totalqunty += $quantity;  
            array_push($pdtid, $row['id']);  
        } 
        
        // Store cart info in session for checkout
        $_SESSION['cart_total'] = $totalprice;
        $_SESSION['cart_items'] = $pdtid;
        $_SESSION['cart_quantity'] = $totalqunty;
    }
}
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="utf-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">  
    <title>My Cart</title>  

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>	    
        .container {
            width: 100%;
            padding-right: 0;
            padding-left: 0;
            margin-right: auto;
            margin-left: auto;
        }
        
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
        
        .shopping-cart { 
            background: white; 
            border-radius: 10px; 
            padding: 25px; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        
        .table-bordered th { 
            background: var(--secondary-color); 
            font-weight: 600; 
            text-align: center;
        }
        
        .cart-grand-total { 
            font-size: 20px; 
            font-weight: 700; 
            color: var(--primary-color); 
            padding: 15px; 
            background-color: var(--secondary-color);
        }
        
        .btn-primary { 
            background: var(--primary-color); 
            border-color: var(--primary-color); 
            padding: 12px 25px; 
            font-weight: 500; 
        }
        
        .empty-cart { 
            text-align: center; 
            padding: 40px; 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        
        .address-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .billing-section {
            background: var(--secondary-color);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            font-weight: 500;
            color: var(--text-color);
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .guest-notice {
            background-color: #e7f3ff;
            border-left: 4px solid #3e63c9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>

    <link rel="shortcut icon" href="assets/images/favicon.ico">  
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
                <li class='active'>Shopping Cart</li>  
            </ul>  
        </div>  
    </div>  
</div>  

<div class="body-content outer-top-xs">  
    <div class="container">  
        <div class="row inner-bottom-sm">  
            <div class="shopping-cart">  

            <!-- ✅ MAIN CART FORM -->  
            <form name="cart" method="post">  

                <?php if(!empty($_SESSION['cart'])){ ?>  

                <!-- Guest User Notice -->
                <?php if(!isset($_SESSION['login'])) { ?>
                <div class="guest-notice">
                    <i class="fas fa-info-circle"></i> 
                    You're checking out as a guest. Please provide your billing information to proceed.
                </div>
                <?php } ?>

                <!-- Address Validation Warning -->
                <?php  
                $addressComplete = false;
                $missingFields = array();
                
                if(isset($_SESSION['login'])) {
                    // For logged in users
                    $query = mysqli_query($con, "SELECT * FROM users WHERE id='".$_SESSION['id']."'");  
                    $user = mysqli_fetch_array($query);
                    
                    $addressComplete = true;
                    
                    if(empty($user['firstname'])) { $addressComplete = false; $missingFields[] = 'first name'; }
                    if(empty($user['lastname'])) { $addressComplete = false; $missingFields[] = 'last name'; }
                    if(empty($user['email'])) { $addressComplete = false; $missingFields[] = 'email'; }
                    if(empty($user['phone'])) { $addressComplete = false; $missingFields[] = 'phone'; }
                    if(empty($user['billingAddress'])) { $addressComplete = false; $missingFields[] = 'address'; }
                    if(empty($user['billingCity'])) { $addressComplete = false; $missingFields[] = 'city'; }
                    if(empty($user['billingState'])) { $addressComplete = false; $missingFields[] = 'state'; }
                    if(empty($user['billingPincode'])) { $addressComplete = false; $missingFields[] = 'postal code'; }
                } else {
                    // For guest users
                    if(isset($_SESSION['guest_billing'])) {
                        $addressComplete = true;
                        $guestBilling = $_SESSION['guest_billing'];
                        
                        if(empty($guestBilling['first_name'])) { $addressComplete = false; $missingFields[] = 'first name'; }
                        if(empty($guestBilling['last_name'])) { $addressComplete = false; $missingFields[] = 'last name'; }
                        if(empty($guestBilling['email'])) { $addressComplete = false; $missingFields[] = 'email'; }
                        if(empty($guestBilling['phone'])) { $addressComplete = false; $missingFields[] = 'phone'; }
                        if(empty($guestBilling['address'])) { $addressComplete = false; $missingFields[] = 'address'; }
                        if(empty($guestBilling['city'])) { $addressComplete = false; $missingFields[] = 'city'; }
                        if(empty($guestBilling['state'])) { $addressComplete = false; $missingFields[] = 'state'; }
                        if(empty($guestBilling['pincode'])) { $addressComplete = false; $missingFields[] = 'postal code'; }
                    } else {
                        $addressComplete = false;
                        $missingFields = array('first name', 'last name', 'email', 'phone', 'address', 'city', 'state', 'postal code');
                    }
                }
                
                if(!$addressComplete) {
                    echo '<div class="address-warning">';
                    echo '<i class="fas fa-exclamation-triangle"></i> ';
                    echo 'Please complete your billing information before proceeding to checkout. ';
                    if(!empty($missingFields)) {
                        echo 'Missing: ' . implode(', ', $missingFields);
                    }
                    echo '</div>';
                }
                ?>

                <!-- Cart Table -->  
                <div class="col-md-12 col-sm-12 shopping-cart-table">  
                    <div class="table-responsive">  
                        <table class="table table-bordered">  
                            <thead>  
                                <tr>  
                                    <th>Remove</th>  
                                    <th>Image</th>  
                                    <th>Product Name</th>  
                                    <th>Quantity</th>  
                                    <th>Price Per unit</th>  
                                    <th>Shipping</th>  
                                    <th>Grandtotal</th>  
                                </tr>  
                            </thead>  
                            <tbody>  
                            <?php  
                            $pdtid = array();  
                            $sql = "SELECT * FROM products WHERE id IN(";  
                            foreach($_SESSION['cart'] as $id => $value) { 
                                $sql .= $id . ","; 
                            }  
                            $sql = substr($sql, 0, -1) . ") ORDER BY id ASC";  
                            $query = mysqli_query($con, $sql);  
                            $totalprice = 0; 
                            $totalqunty = 0;  
                            
                            if(!empty($query)) {  
                                while($row = mysqli_fetch_array($query)) {  
                                    $quantity = $_SESSION['cart'][$row['id']]['quantity'];  
                                    $subtotal = $quantity * $row['productPrice'] + $row['shippingCharge'];  
                                    $totalprice += $subtotal;  
                                    $totalqunty += $quantity;  
                                    array_push($pdtid, $row['id']);  
                            ?>  
                                <tr>  
                                    <td><input type="checkbox" name="remove_code[]" value="<?php echo htmlentities($row['id']);?>" /></td>  
                                    <td><img src="admin/productimages/<?php echo $row['id'];?>/<?php echo $row['productImage1'];?>" width="80" alt="<?php echo htmlentities($row['productName']); ?>"></td>  
                                    <td><a href="product-details.php?pid=<?php echo $row['id'];?>"><?php echo htmlentities($row['productName']);?></a></td>  
                                    <td><input type="number" min="0" value="<?php echo $quantity; ?>" name="quantity[<?php echo $row['id']; ?>]" size="2" style="width: 60px;"></td>  
                                    <td>Kes. <?php echo number_format($row['productPrice']); ?>.00</td>  
                                    <td>Kes. <?php echo number_format($row['shippingCharge']); ?>.00</td>  
                                    <td style="color: black; font-weight: bold;">Kes. <?php echo number_format($subtotal); ?>.00</td> 
                                </tr>  
                            <?php 
                                } 
                            } 
                            $_SESSION['pid'] = $pdtid; 
                            $_SESSION['tp'] = $totalprice;
                            ?>  
                            </tbody>  
                            <tfoot>  
                                <tr>  
                                    <td colspan="7">  
                                        <div class="shopping-cart-btn">  
                                           <button class="btn btn-primary" onclick="window.history.back();">Continue Shopping</button>

                                            <input type="submit" name="submit" value="Update Cart" class="btn btn-primary pull-right">  
                                        </div>  
                                    </td>  
                                </tr>  
                            </tfoot>  
                        </table>  
                    </div>  
                </div>  

                <!-- Billing Information Section for PesaPal -->  
                <div class="col-md-8 col-sm-12 billing-section">  
                    <h4 class="estimate-title">Billing Information for PesaPal Payment</h4>  
                    <p class="text-muted">This information is required for payment processing with PesaPal.</p>
                    <?php  
                    if(isset($_SESSION['login'])) {
                        // For logged in users
                        $query = mysqli_query($con, "SELECT * FROM users WHERE id='".$_SESSION['id']."'");  
                        $user = mysqli_fetch_array($query);  
                    } else {
                        // For guest users
                        $user = array(
                            'firstname' => isset($_SESSION['guest_billing']['first_name']) ? $_SESSION['guest_billing']['first_name'] : '',
                            'lastname' => isset($_SESSION['guest_billing']['last_name']) ? $_SESSION['guest_billing']['last_name'] : '',
                            'email' => isset($_SESSION['guest_billing']['email']) ? $_SESSION['guest_billing']['email'] : '',
                            'phone' => isset($_SESSION['guest_billing']['phone']) ? $_SESSION['guest_billing']['phone'] : '',
                            'billingAddress' => isset($_SESSION['guest_billing']['address']) ? $_SESSION['guest_billing']['address'] : '',
                            'billingCity' => isset($_SESSION['guest_billing']['city']) ? $_SESSION['guest_billing']['city'] : '',
                            'billingState' => isset($_SESSION['guest_billing']['state']) ? $_SESSION['guest_billing']['state'] : '',
                            'billingPincode' => isset($_SESSION['guest_billing']['pincode']) ? $_SESSION['guest_billing']['pincode'] : ''
                        );
                    }
                    ?>  
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">  
                                <label class="required-field">First Name</label>  
                                <input type="text" class="form-control" name="first_name" value="<?php echo htmlentities($user['firstname']);?>" required placeholder="e.g., John">  
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">  
                                <label class="required-field">Last Name</label>  
                                <input type="text" class="form-control" name="last_name" value="<?php echo htmlentities($user['lastname']);?>" required placeholder="e.g., Doe">  
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">  
                                <label class="required-field">Email Address</label>  
                                <input type="email" class="form-control" name="email" value="<?php echo htmlentities($user['email']);?>" required placeholder="e.g., customer@example.com">  
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">  
                                <label class="required-field">Phone Number</label>  
                                <input type="text" class="form-control" name="phone" value="<?php echo htmlentities($user['phone']);?>" required placeholder="e.g., 254700000000">  
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">  
                        <label class="required-field">Address</label>  
                        <textarea class="form-control" name="address" required placeholder="Enter your complete address"><?php echo htmlentities($user['billingAddress']);?></textarea>  
                    </div>  
                    
                    <div class="row">  
                        <div class="col-md-4">  
                            <div class="form-group">  
                                <label class="required-field">City</label>  
                                <input type="text" class="form-control" name="city" value="<?php echo htmlentities($user['billingCity']);?>" required placeholder="e.g., Nairobi">  
                            </div>  
                        </div>  
                        <div class="col-md-4">  
                            <div class="form-group">  
                                <label class="required-field">State/County</label>  
                                <input type="text" class="form-control" name="state" value="<?php echo htmlentities($user['billingState']);?>" required placeholder="e.g., Nairobi">  
                            </div>  
                        </div>  
                        <div class="col-md-4">  
                            <div class="form-group">  
                                <label class="required-field">Postal Code</label>  
                                <input type="text" class="form-control" name="pincode" value="<?php echo htmlentities($user['billingPincode']);?>" required placeholder="e.g., 00100">  
                            </div>  
                        </div>  
                    </div>  
                    
                    <input type="hidden" name="country_code" value="KE">
                </div>  

                <!-- Grand Total + Checkout -->  
                <div class="col-md-4 col-sm-12 cart-shopping-total">  
                    <table class="table table-bordered">  
                        <thead>  
                            <tr>  
                                <th>  
                                    <div class="cart-grand-total">  
                                        Grand Total <span class="inner-left-md">Kes. <?php echo number_format($totalprice); ?>.00</span>  
                                    </div>  
                                </th>  
                            </tr>  
                        </thead>  
                        <tbody>  
                            <tr>  
                                <td>  
                                    <div class="cart-checkout-btn pull-right">  
                                        <?php if(isset($_SESSION['login'])) { ?>
                                        <button type="submit" name="address_update" class="btn btn-primary">Update Billing Info</button>  
                                        <?php } else { ?>
                                        <button type="submit" name="save_billing_info" class="btn btn-primary">Save Billing Info</button>  
                                        <?php } ?>
                                        <button type="button" id="proceedToCheckout" class="btn btn-success btn-block" style="margin-top: 10px;" 
                                                <?php echo !$addressComplete ? 'disabled title="Please complete your billing information first"' : ''; ?>>  
                                            <i class="fas fa-lock"></i> PROCEED TO PAYMENT  
                                        </button>  
                                    </div>  
                                </td>  
                            </tr>  
                        </tbody>  
                    </table>  
                </div>  

                <?php } else { ?>  
                    <div class="empty-cart">  
                        <i class="fa fa-shopping-cart fa-3x text-muted"></i>  
                        <h3>Your shopping Cart is empty</h3>  
                        <p>Looks like you haven't added anything yet.</p>  
                        <a href="index.php" class="btn btn-primary">Continue Shopping</a>  
                    </div>  
                <?php } ?>  

            </form>  
            <!-- ✅ END MAIN FORM -->  

            </div>  
        </div>  
        <?php include('includes/brands-slider.php');?>  
    </div>  
</div>  

<?php include('includes/footer.php');?>  

<script src="assets/js/jquery-1.11.1.min.js"></script>  
<script src="assets/js/bootstrap.min.js"></script>  
<script>
$(document).ready(function() {
    // Handle checkout button click
    $('#proceedToCheckout').click(function() {
        // Validate all billing fields before proceeding
        var first_name = $('input[name="first_name"]').val().trim();
        var last_name = $('input[name="last_name"]').val().trim();
        var email = $('input[name="email"]').val().trim();
        var phone = $('input[name="phone"]').val().trim();
        var address = $('textarea[name="address"]').val().trim();
        var city = $('input[name="city"]').val().trim();
        var state = $('input[name="state"]').val().trim();
        var pincode = $('input[name="pincode"]').val().trim();
        
        var missingFields = [];
        if (!first_name) missingFields.push('first name');
        if (!last_name) missingFields.push('last name');
        if (!email) missingFields.push('email');
        if (!phone) missingFields.push('phone');
        if (!address) missingFields.push('address');
        if (!city) missingFields.push('city');
        if (!state) missingFields.push('state');
        if (!pincode) missingFields.push('postal code');
        
        if (missingFields.length > 0) {
            alert('Please complete all required billing fields before proceeding to checkout.\nMissing: ' + missingFields.join(', '));
            return false;
        }
        
        // Validate email format
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return false;
        }
        
        // Validate phone format (Kenyan format: 254xxxxxxxxx)
        var phoneRegex = /^254\d{9}$/;
        if (!phoneRegex.test(phone)) {
            alert('Please enter a valid Kenyan phone number starting with 254 followed by 9 digits (e.g., 254700000000).');
            return false;
        }
        
        // For guest users, save billing info first
        <?php if(!isset($_SESSION['login'])) { ?>
        $.ajax({
            url: 'my-cart.php',
            type: 'POST',
            data: {
                save_billing_info: 1,
                first_name: first_name,
                last_name: last_name,
                email: email,
                phone: phone,
                address: address,
                city: city,
                state: state,
                pincode: pincode
            },
            success: function() {
                // Redirect to checkout after billing info is saved
                window.location.href = 'checkout.php';
            }
        });
        <?php } else { ?>
        // For logged in users, update billing info first via AJAX
        $.ajax({
            url: 'my-cart.php',
            type: 'POST',
            data: {
                address_update: 1,
                first_name: first_name,
                last_name: last_name,
                email: email,
                phone: phone,
                address: address,
                city: city,
                state: state,
                pincode: pincode
            },
            success: function() {
                // Redirect to checkout after billing info update
                window.location.href = 'checkout.php';
            }
        });
        <?php } ?>
    });
});
</script>
</body>  
</html>