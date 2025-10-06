<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

// Debug function
function debug_data($data, $label = '') {
    echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd;'>";
    if ($label) echo "<strong>$label:</strong>\n";
    print_r($data);
    echo "</pre>";
}
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
						// Check if order_id is provided via GET
						if(isset($_GET['order_id'])) {
							$order_id = intval($_GET['order_id']);
							
							echo "<!-- Debug: Looking for order ID: $order_id -->";
							
							// Get order details with more flexible query
							$order_query = mysqli_query($con, "SELECT o.*, u.email, u.firstName, u.lastName, u.contactNo 
															FROM orders o 
															LEFT JOIN users u ON o.userId = u.id 
															WHERE o.id = '$order_id'");
							
							if(!$order_query) {
								echo '<div class="alert alert-danger">Database query error: ' . mysqli_error($con) . '</div>';
							}
							
							if(mysqli_num_rows($order_query) > 0) {
								$order_data = mysqli_fetch_array($order_query);
								
								// Debug: Show what we found
								// debug_data($order_data, "Order Data from Database");
								
								?>
								
								<!-- Order Summary -->
								<div class="order-summary" style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #007bff;">
									<h3 style="color: #007bff; margin-bottom: 20px;"><i class="fa fa-receipt"></i> Order Summary</h3>
									<div class="row">
										<div class="col-md-6">
											<p><strong>Order ID:</strong> #<?php echo htmlentities($order_data['id']); ?></p>
											<p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order_data['orderDate'])); ?></p>
											<?php if(!empty($order_data['firstName'])): ?>
											<p><strong>Customer:</strong> <?php echo htmlentities($order_data['firstName'] . ' ' . $order_data['lastName']); ?></p>
											<?php endif; ?>
											<?php if(!empty($order_data['email'])): ?>
											<p><strong>Email:</strong> <?php echo htmlentities($order_data['email']); ?></p>
											<?php endif; ?>
											<?php if(!empty($order_data['contactNo'])): ?>
											<p><strong>Phone:</strong> <?php echo htmlentities($order_data['contactNo']); ?></p>
											<?php endif; ?>
										</div>
										<div class="col-md-6">
											<p><strong>Payment Method:</strong> 
												<span class="badge badge-info">
													<?php echo htmlentities($order_data['paymentMethod'] ?: 'Processing'); ?>
												</span>
											</p>
											<p><strong>Order Status:</strong> 
												<span class="badge badge-<?php 
													switch($order_data['orderStatus']) {
														case 'COMPLETED': echo 'success'; break;
														case 'Delivered': echo 'success'; break;
														case 'Pending': echo 'warning'; break;
														case 'FAILED': echo 'danger'; break;
														case 'FAILED': echo 'danger'; break;
														default: echo 'secondary';
													}
												?>">
													<?php echo htmlentities($order_data['orderStatus'] ?: 'Pending'); ?>
												</span>
											</p>
											<p><strong>Total Amount:</strong> <span style="font-size: 1.2em; font-weight: bold; color: #28a745;">Kes. <?php echo number_format($order_data['amount'] ?: 0, 2); ?></span></p>
											<?php if(!empty($order_data['confirmation_code'])): ?>
											<p><strong>Confirmation Code:</strong> <code><?php echo htmlentities($order_data['confirmation_code']); ?></code></p>
											<?php endif; ?>
											<?php if(!empty($order_data['pesapal_tracking_id'])): ?>
											<p><strong>Tracking ID:</strong> <code><?php echo htmlentities($order_data['pesapal_tracking_id']); ?></code></p>
											<?php endif; ?>
										</div>
									</div>
								</div>

								<!-- Order Items -->
								<div class="order-items">
									<h3 style="margin-bottom: 20px; color: #495057;"><i class="fa fa-shopping-cart"></i> Order Items</h3>
									
									<?php
									// Get order items with better product detection
									$productIds = array();
									$cart_data = array();
									$has_products = false;
									
									// Debug the productId field
									// debug_data($order_data['productId'], "Raw productId from database");
									
									if(!empty($order_data['productId'])) {
										// Check if productId contains serialized data
										if(strpos($order_data['productId'], 'a:') === 0) {
											// It's serialized cart data
											$cart_data = @unserialize($order_data['productId']);
											if(is_array($cart_data)) {
												$productIds = array_keys($cart_data);
												$has_products = true;
											}
										} else if(strpos($order_data['productId'], ',') !== false) {
											// It's comma-separated product IDs
											$productIds = array_filter(explode(',', $order_data['productId']));
											if(!empty($productIds)) {
												$has_products = true;
												// For comma-separated, create cart data structure
												foreach($productIds as $pid) {
													$cart_data[$pid] = array('quantity' => 1);
												}
											}
										} else if(is_numeric($order_data['productId'])) {
											// Single product ID
											$productIds = array($order_data['productId']);
											$cart_data[$order_data['productId']] = array('quantity' => $order_data['quantity'] ?: 1);
											$has_products = true;
										}
									}
									
									// Debug parsed product data
									// debug_data($productIds, "Parsed Product IDs");
									// debug_data($cart_data, "Parsed Cart Data");
									
									if($has_products && !empty($productIds)) {
										?>
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
													</tr>
												</thead>
												<tbody>
													<?php
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
															
															$unit_price = $product['productPrice'] ?: 0;
															$subtotal = $unit_price * $quantity;
															$total_amount += $subtotal;
															?>
															<tr>
																<td><?php echo $cnt; ?></td>
																<td>
																	<?php 
																	$image_path = "admin/productimages/{$product['id']}/{$product['productImage1']}";
																	if(file_exists($image_path)): ?>
																		<img src="<?php echo $image_path; ?>" 
																			 alt="<?php echo htmlentities($product['productName']); ?>" 
																			 width="60" height="60" 
																			 style="object-fit: cover; border-radius: 5px;">
																	<?php else: ?>
																		<div style="width: 60px; height: 60px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
																			<i class="fa fa-image text-muted"></i>
																		</div>
																	<?php endif; ?>
																</td>
																<td>
																	<strong><?php echo htmlentities($product['productName']); ?></strong>
																	<?php if($product['productPriceBeforeDiscount'] > $product['productPrice']): ?>
																	<br><small class="text-muted">
																		<del>Kes. <?php echo number_format($product['productPriceBeforeDiscount'], 2); ?></del>
																	</small>
																	<?php endif; ?>
																</td>
																<td><?php echo $quantity; ?></td>
																<td>Kes. <?php echo number_format($unit_price, 2); ?></td>
																<td><strong>Kes. <?php echo number_format($subtotal, 2); ?></strong></td>
															</tr>
															<?php 
															$cnt++;
														} else {
															// Product not found in database
															echo "<!-- Debug: Product ID $pid not found in products table -->";
														}
													}
													?>
													<tr class="table-success">
														<td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
														<td><strong>Kes. <?php echo number_format($total_amount, 2); ?></strong></td>
													</tr>
												</tbody>
											</table>
										</div>
										<?php
									} else {
										// No products found in this order
										echo '<div class="alert alert-info">
												<i class="fa fa-info-circle"></i> 
												This order is being processed. Product details will appear here shortly.
											</div>';
										
										// Show debug info about why no products were found
										echo "<!-- Debug: has_products: " . ($has_products ? 'true' : 'false') . " -->";
										echo "<!-- Debug: productIds count: " . count($productIds) . " -->";
										echo "<!-- Debug: productId field: " . htmlentities(substr($order_data['productId'], 0, 100)) . " -->";
									}
									?>
								</div>
								
								<!-- Action Buttons -->
								<div class="action-buttons text-center mt-4">
									<a href="index.php" class="btn btn-primary">
										<i class="fa fa-shopping-bag"></i> Continue Shopping
									</a>
									<a href="my-orders.php" class="btn btn-outline-primary">
										<i class="fa fa-list"></i> View All Orders
									</a>
								</div>
								
								<?php
							} else {
								// Order not found
								echo '<div class="alert alert-danger text-center">
										<i class="fa fa-exclamation-triangle fa-2x mb-3"></i><br>
										<h4>Order Not Found</h4>
										<p>Order ID #' . htmlentities($order_id) . ' was not found in our system.</p>
										<p class="text-muted"><small>This could be because:</small></p>
										<ul class="text-left" style="display: inline-block;">
											<li>The order is still being processed</li>
											<li>The order ID is incorrect</li>
											<li>There was an error creating the order</li>
										</ul>
										<br>
										<a href="index.php" class="btn btn-primary">Return to Homepage</a>
										<a href="my-orders.php" class="btn btn-outline-secondary">Check Your Orders</a>
									</div>';
								
								// Debug: Show recent orders to help troubleshooting
								$recent_orders = mysqli_query($con, "SELECT id, orderDate, paymentMethod, orderStatus FROM orders ORDER BY id DESC LIMIT 5");
								if(mysqli_num_rows($recent_orders) > 0) {
									echo '<div class="mt-4 p-3 bg-light rounded">';
									echo '<h5>Recent Orders in System (for debugging):</h5>';
									echo '<table class="table table-sm">';
									echo '<tr><th>Order ID</th><th>Date</th><th>Payment</th><th>Status</th></tr>';
									while($recent = mysqli_fetch_array($recent_orders)) {
										echo '<tr>';
										echo '<td>' . $recent['id'] . '</td>';
										echo '<td>' . $recent['orderDate'] . '</td>';
										echo '<td>' . $recent['paymentMethod'] . '</td>';
										echo '<td>' . $recent['orderStatus'] . '</td>';
										echo '</tr>';
									}
									echo '</table>';
									echo '</div>';
								}
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
												<small class="form-text text-muted">You can find your Order ID in your order confirmation email or on the order success page.</small>
											</div>
											
											<button type="submit" class="btn btn-primary btn-block">
												<i class="fa fa-eye"></i> View Order Details
											</button>
										</form>
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
	</body>
</html>