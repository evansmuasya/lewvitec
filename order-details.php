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

	    <title>Order Details</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/red.css">
	    <link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
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
					<div class="col-md-12">
						
						<?php
						// Check if order_id is provided via GET (from order-success.php)
						if(isset($_GET['order_id'])) {
							$order_id = intval($_GET['order_id']);
							
							// Get order details
							$order_query = mysqli_query($con, "SELECT o.*, u.email, u.firstName, u.lastName, u.contactNo 
															FROM orders o 
															JOIN users u ON o.userId = u.id 
															WHERE o.id = '$order_id'");
							
							if(mysqli_num_rows($order_query) > 0) {
								$order_data = mysqli_fetch_array($order_query);
								?>
								
								<!-- Order Summary -->
								<div class="order-summary" style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #007bff;">
									<h3 style="color: #007bff; margin-bottom: 20px;"><i class="fa fa-receipt"></i> Order Summary</h3>
									<div class="row">
										<div class="col-md-6">
											<p><strong>Order ID:</strong> #<?php echo htmlentities($order_data['id']); ?></p>
											<p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order_data['orderDate'])); ?></p>
											<p><strong>Customer:</strong> <?php echo htmlentities($order_data['firstName'] . ' ' . $order_data['lastName']); ?></p>
											<p><strong>Email:</strong> <?php echo htmlentities($order_data['email']); ?></p>
											<?php if(!empty($order_data['contactNo'])): ?>
											<p><strong>Phone:</strong> <?php echo htmlentities($order_data['contactNo']); ?></p>
											<?php endif; ?>
										</div>
										<div class="col-md-6">
											<p><strong>Payment Method:</strong> 
												<span class="badge badge-info">
													<?php echo htmlentities($order_data['paymentMethod'] ?: 'Not Specified'); ?>
												</span>
											</p>
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
											<p><strong>Total Amount:</strong> <span style="font-size: 1.2em; font-weight: bold; color: #28a745;">Kes. <?php echo number_format($order_data['amount'], 2); ?></span></p>
											<?php if(!empty($order_data['confirmation_code'])): ?>
											<p><strong>Confirmation Code:</strong> <code><?php echo htmlentities($order_data['confirmation_code']); ?></code></p>
											<?php endif; ?>
											<?php if(!empty($order_data['pesapal_tracking_id'])): ?>
											<p><strong>Tracking ID:</strong> <code><?php echo htmlentities($order_data['pesapal_tracking_id']); ?></code></p>
											<?php endif; ?>
										</div>
									</div>
									
									<!-- Billing Address -->
									<?php if(!empty($order_data['billing_first_name']) || !empty($order_data['city'])): ?>
									<hr>
									<div class="row">
										<div class="col-md-12">
											<h5 style="color: #6c757d;"><i class="fa fa-map-marker-alt"></i> Billing Address</h5>
											<p>
												<?php 
												$address_parts = [];
												if(!empty($order_data['billing_first_name'])) {
													$address_parts[] = $order_data['billing_first_name'] . ' ' . $order_data['billing_last_name'];
												}
												if(!empty($order_data['city'])) $address_parts[] = $order_data['city'];
												if(!empty($order_data['County'])) $address_parts[] = $order_data['County'];
												if(!empty($order_data['postal_code'])) $address_parts[] = $order_data['postal_code'];
												echo implode(', ', $address_parts);
												?>
											</p>
										</div>
									</div>
									<?php endif; ?>
								</div>

								<!-- Order Items -->
								<div class="order-items">
									<h3 style="margin-bottom: 20px; color: #495057;"><i class="fa fa-shopping-cart"></i> Order Items</h3>
									<div class="table-responsive">
										<table class="table table-bordered table-hover">
											<thead class="thead-light">
												<tr>
													<th>#</th>
													<th>Product Image</th>
													<th>Product Name</th>
													<th>Quantity</th>
													<th>Unit Price</th>
													<th>Subtotal</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php
												// Get order items
												$productIds = array();
												$cart_data = array();
												
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
														// For comma-separated, we assume quantity is in the main order quantity field
														foreach($productIds as $pid) {
															if(!empty($pid)) {
																$cart_data[$pid] = array('quantity' => $order_data['quantity']);
															}
														}
													} else {
														// Single product ID
														$productIds = array($order_data['productId']);
														$cart_data[$order_data['productId']] = array('quantity' => $order_data['quantity']);
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
														
														// Get quantity
														$quantity = 1;
														if(isset($cart_data[$pid]['quantity'])) {
															$quantity = $cart_data[$pid]['quantity'];
														}
														
														$unit_price = $product['productPrice'];
														$subtotal = $unit_price * $quantity;
														$total_amount += $subtotal;
														?>
														<tr>
															<td><?php echo $cnt; ?></td>
															<td>
																<a href="product-details.php?pid=<?php echo $product['id']; ?>">
																	<img src="admin/productimages/<?php echo $product['id']; ?>/<?php echo $product['productImage1']; ?>" 
																		 alt="<?php echo htmlentities($product['productName']); ?>" 
																		 width="60" height="60" 
																		 style="object-fit: cover; border-radius: 5px;">
																</a>
															</td>
															<td>
																<a href="product-details.php?pid=<?php echo $product['id']; ?>" style="color: #007bff; text-decoration: none;">
																	<strong><?php echo htmlentities($product['productName']); ?></strong>
																</a>
																<?php if($product['productPriceBeforeDiscount'] > $product['productPrice']): ?>
																<br><small class="text-muted">
																	<del>Kes. <?php echo number_format($product['productPriceBeforeDiscount'], 2); ?></del>
																</small>
																<?php endif; ?>
															</td>
															<td><?php echo $quantity; ?></td>
															<td>Kes. <?php echo number_format($unit_price, 2); ?></td>
															<td><strong>Kes. <?php echo number_format($subtotal, 2); ?></strong></td>
															<td>
																<a href="javascript:void(0);" 
																   onclick="popUpWindow('track-order.php?oid=<?php echo htmlentities($order_data['id']); ?>');" 
																   class="btn btn-primary btn-sm" 
																   title="Track order">
																	<i class="fa fa-truck"></i> Track
																</a>
															</td>
														</tr>
														<?php 
														$cnt++;
													}
												}
												
												if($cnt > 1) {
													?>
													<tr class="table-success">
														<td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
														<td colspan="2"><strong>Kes. <?php echo number_format($total_amount, 2); ?></strong></td>
													</tr>
													<?php
												} else {
													?>
													<tr>
														<td colspan="7" class="text-center text-muted py-4">
															<i class="fa fa-exclamation-circle fa-2x mb-2"></i><br>
															No products found in this order
														</td>
													</tr>
													<?php
												}
												?>
											</tbody>
										</table>
									</div>
								</div>
								
								<!-- Action Buttons -->
								<div class="action-buttons text-center mt-4">
									<a href="index.php" class="btn btn-primary">
										<i class="fa fa-shopping-bag"></i> Continue Shopping
									</a>
									<a href="my-orders.php" class="btn btn-outline-primary">
										<i class="fa fa-list"></i> View All Orders
									</a>
									<button onclick="window.print()" class="btn btn-outline-secondary">
										<i class="fa fa-print"></i> Print Order
									</button>
								</div>
								
								<?php
							} else {
								echo '<div class="alert alert-danger text-center">
										<i class="fa fa-exclamation-triangle fa-2x mb-3"></i><br>
										<h4>Order Not Found</h4>
										<p>The order ID you are looking for does not exist.</p>
										<a href="index.php" class="btn btn-primary">Return to Homepage</a>
									</div>';
							}
						} else {
							// Show search form if no order_id is provided
							?>
							<div class="order-search-form" style="max-width: 500px; margin: 50px auto; text-align: center;">
								<div class="card">
									<div class="card-body">
										<h3 class="card-title"><i class="fa fa-search"></i> Find Your Order</h3>
										<p class="card-text">Enter your order ID to view order details.</p>
										
										<form method="get" action="">
											<div class="form-group text-left">
												<label for="order_id"><strong>Order ID *</strong></label>
												<input type="text" class="form-control" id="order_id" name="order_id" 
													   required placeholder="Enter your order ID (e.g., 302)"
													   value="<?php echo isset($_GET['order_id']) ? htmlentities($_GET['order_id']) : ''; ?>">
												<small class="form-text text-muted">You can find your Order ID in your order confirmation email.</small>
											</div>
											
											<button type="submit" class="btn btn-primary btn-block">
												<i class="fa fa-eye"></i> View Order Details
											</button>
										</form>
										
										<hr>
										<p class="text-muted">
											<small>Don't have your Order ID? <a href="my-orders.php">View your order history</a></small>
										</p>
									</div>
								</div>
							</div>
							<?php
						}
						?>
						
					</div>
				</div>
			</div>
		</div>

		<!-- ============================================== BRANDS CAROUSEL ============================================== -->
		<?php include('includes/brands-slider.php'); ?>
		<!-- ============================================== BRANDS CAROUSEL : END ============================================== -->
		
		<?php include('includes/footer.php'); ?>

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
	</body>
</html>