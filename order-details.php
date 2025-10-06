<?php 
session_start();
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">

	    <title>Order History</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/red.css">
	    <link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<!--<link rel="stylesheet" href="assets/css/owl.theme.css">-->
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">

		<!-- Demo Purpose Only. Should be removed in production -->
		<link rel="stylesheet" href="assets/css/config.css">

		<link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
		<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="assets/images/favicon.ico">
	<script language="javascript" type="text/javascript">
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
 if(popUpWin)
{
if(!popUpWin.closed) popUpWin.close();
}
popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+600+',height='+600+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}

</script>

	</head>
    <body class="cnt-home">
	
		
	
		<!-- ============================================== HEADER ============================================== -->
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>
<!-- ============================================== HEADER : END ============================================== -->
<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="index.php">Home</a></li>
				<li class='active'>Order Details</li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content outer-top-xs">
	<div class="container">
		<div class="row inner-bottom-sm">
			<div class="shopping-cart">
				<div class="col-md-12 col-sm-12 shopping-cart-table ">
	<div class="table-responsive">
<form name="orderDetails" method="post">	
<?php
// Check if form is submitted
if(isset($_POST['orderid']) && isset($_POST['email'])) {
    $orderid = $_POST['orderid'];
    $email = $_POST['email'];
    
    // Validate order exists and belongs to the email
    $ret = mysqli_query($con, "SELECT o.*, u.email, u.firstName, u.lastName 
                              FROM orders o 
                              JOIN users u ON o.userId = u.id 
                              WHERE o.id = '$orderid' AND u.email = '$email'");
    $num = mysqli_num_rows($ret);
    
    if($num > 0) {
        $order_data = mysqli_fetch_array($ret);
        ?>
        
        <!-- Order Summary -->
        <div class="order-summary" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h3>Order Summary</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Order ID:</strong> #<?php echo htmlentities($order_data['id']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlentities($order_data['orderDate']); ?></p>
                    <p><strong>Customer:</strong> <?php echo htmlentities($order_data['firstName'] . ' ' . $order_data['lastName']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlentities($order_data['email']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Payment Method:</strong> <?php echo htmlentities($order_data['paymentMethod']); ?></p>
                    <p><strong>Order Status:</strong> 
                        <span class="badge badge-<?php 
                            switch($order_data['orderStatus']) {
                                case 'COMPLETED': echo 'success'; break;
                                case 'Delivered': echo 'success'; break;
                                case 'Pending': echo 'warning'; break;
                                case 'FAILED': echo 'danger'; break;
                                default: echo 'secondary';
                            }
                        ?>">
                            <?php echo htmlentities($order_data['orderStatus'] ?: 'Pending'); ?>
                        </span>
                    </p>
                    <p><strong>Total Amount:</strong> Kes. <?php echo number_format($order_data['amount'], 2); ?></p>
                    <?php if(!empty($order_data['confirmation_code'])): ?>
                    <p><strong>Confirmation Code:</strong> <?php echo htmlentities($order_data['confirmation_code']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <h3>Order Items</h3>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="cart-romove item">#</th>
					<th class="cart-description item">Image</th>
					<th class="cart-product-name item">Product Name</th>
					<th class="cart-qty item">Quantity</th>
					<th class="cart-sub-total item">Price Per unit</th>
					<th class="cart-total item">Subtotal</th>
					<th class="cart-total last-item">Action</th>
				</tr>
			</thead><!-- /thead -->
			
			<tbody>
<?php
        // Get order items
        // Handle different productId formats in the orders table
        $productIds = array();
        
        if(!empty($order_data['productId'])) {
            // Check if productId contains serialized data
            if(strpos($order_data['productId'], 'a:') === 0) {
                // It's serialized data
                $cart_data = unserialize($order_data['productId']);
                if(is_array($cart_data)) {
                    $productIds = array_keys($cart_data);
                }
            } else if(strpos($order_data['productId'], ',') !== false) {
                // It's comma-separated product IDs
                $productIds = explode(',', $order_data['productId']);
            } else {
                // Single product ID
                $productIds = array($order_data['productId']);
            }
        }
        
        $cnt = 1;
        $total_amount = 0;
        
        foreach($productIds as $pid) {
            if(empty($pid)) continue;
            
            // Get product details
            $product_query = mysqli_query($con, "SELECT * FROM products WHERE id = '$pid'");
            if(mysqli_num_rows($product_query) > 0) {
                $product = mysqli_fetch_array($product_query);
                
                // Get quantity - this is complex due to data structure variations
                $quantity = 1;
                if(strpos($order_data['productId'], 'a:') === 0) {
                    $cart_data = unserialize($order_data['productId']);
                    if(isset($cart_data[$pid]['quantity'])) {
                        $quantity = $cart_data[$pid]['quantity'];
                    }
                } else {
                    $quantity = $order_data['quantity'];
                }
                
                $subtotal = $product['productPrice'] * $quantity;
                $total_amount += $subtotal;
?>
				<tr>
					<td><?php echo $cnt;?></td>
					<td class="cart-image">
						<a class="entry-thumbnail" href="product-details.php?pid=<?php echo $product['id'];?>">
						    <img src="admin/productimages/<?php echo $product['id'];?>/<?php echo $product['productImage1'];?>" alt="<?php echo htmlentities($product['productName']);?>" width="84" height="84" style="object-fit: cover;">
						</a>
					</td>
					<td class="cart-product-name-info">
						<h4 class='cart-product-description'>
                            <a href="product-details.php?pid=<?php echo $product['id'];?>">
                                <?php echo htmlentities($product['productName']);?>
                            </a>
                        </h4>
					</td>
					<td class="cart-product-quantity">
						<?php echo $quantity; ?>   
		            </td>
					<td class="cart-product-sub-total">Kes. <?php echo number_format($product['productPrice'], 2); ?>  </td>
					<td class="cart-product-grand-total">Kes. <?php echo number_format($subtotal, 2); ?></td>
					<td>
                        <a href="javascript:void(0);" onClick="popUpWindow('track-order.php?oid=<?php echo htmlentities($order_data['id']);?>');" title="Track order" class="btn btn-primary btn-sm">
                            Track Order
                        </a>
                    </td>
				</tr>
<?php 
                $cnt++;
            }
        }
        
        // Display total
        if($cnt > 1) {
?>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                    <td colspan="2"><strong>Kes. <?php echo number_format($total_amount, 2); ?></strong></td>
                </tr>
<?php
        } else {
?>
                <tr>
                    <td colspan="7" class="text-center">No products found in this order</td>
                </tr>
<?php
        }
?>
			</tbody><!-- /tbody -->
		</table><!-- /table -->
<?php
    } else {
        echo '<div class="alert alert-danger">Invalid order ID or email address. Please check your details and try again.</div>';
    }
} else {
    // Show search form if no data submitted
?>
        <div class="order-search-form" style="max-width: 500px; margin: 0 auto;">
            <h3>Find Your Order</h3>
            <p>Enter your order ID and email address to view order details.</p>
            
            <div class="form-group">
                <label for="orderid">Order ID *</label>
                <input type="text" class="form-control" id="orderid" name="orderid" required placeholder="Enter your order ID">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email address">
            </div>
            
            <button type="submit" class="btn btn-primary">View Order Details</button>
        </div>
<?php
}
?>
		
	</div>
</div>

		</div><!-- /.shopping-cart -->
		</div> <!-- /.row -->
		</form>
		<!-- ============================================== BRANDS CAROUSEL ============================================== -->
<?php echo include('includes/brands-slider.php');?>
<!-- ============================================== BRANDS CAROUSEL : END ============================================== -->	</div><!-- /.container -->
</div><!-- /.body-content -->
<?php include('includes/footer.php');?>

	<script src="assets/js/jquery-1.11.1.min.js"></script>
	
	<script src="assets/js/bootstrap.min.js"></script>
	
	<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>
	
	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="assets/js/jquery.rateit.min.js"></script>
    <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>

	<!-- For demo purposes – can be removed on production -->
	
	<script src="switchstylesheet/switchstylesheet.js"></script>
	
	<script>
		$(document).ready(function(){ 
			$(".changecolor").switchstylesheet( { seperator:"color"} );
			$('.show-theme-options').click(function(){
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
	<!-- For demo purposes – can be removed on production : End -->
</body>
</html>