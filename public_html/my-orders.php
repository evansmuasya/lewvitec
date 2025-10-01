<?php 
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{   
header('location:login.php');
}
else{
	if (isset($_GET['id'])) {
		mysqli_query($con,"delete from orders  where userId='".$_SESSION['id']."' and paymentMethod is null and id='".$_GET['id']."' ");
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

	    <title>Pending Order History</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

<style>        
/* Add the modified CSS here */ 
.container {
  width: 100%;
    padding-right: 0;
    padding-left: 0;
    margin-right: auto;
    margin-left: auto;
}
</style>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">

		<style>
			:root {
				--primary: #4eacfd;
				--secondary: #2c3e50;
				--accent: #e74c3c;
				--success: #28a745;
				--warning: #ffc107;
				--light: #f8f9fa;
				--dark: #343a40;
				--border: #dee2e6;
				--text: #333;
				--text-light: #6c757d;
				--shadow: 0 4px 12px rgba(0,0,0,0.08);
				--radius: 10px;
			}
			
			body {
				font-family: 'Roboto', sans-serif;
				color: var(--text);
				background-color: #f9f9f9;
				line-height: 1.6;
			}
			
			.orders-container {
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px;
			}
			
			.orders-header {
				background: linear-gradient(135deg, var(--primary), #2a5c9e);
				color: white;
				padding: 25px;
				border-radius: var(--radius);
				margin-bottom: 30px;
				box-shadow: var(--shadow);
			}
			
			.orders-header h1 {
				font-size: 28px;
				font-weight: 700;
				margin: 0 0 10px 0;
			}
			
			.breadcrumb {
				padding: 0;
				background: transparent;
				margin-bottom: 15px;
				color: rgba(255,255,255,0.8);
			}
			
			.breadcrumb a {
				color: rgba(255,255,255,0.9);
				text-decoration: none;
			}
			
			.order-card {
				background: white;
				border-radius: var(--radius);
				overflow: hidden;
				box-shadow: var(--shadow);
				margin-bottom: 25px;
				transition: transform 0.3s ease;
			}
			
			.order-card:hover {
				transform: translateY(-3px);
			}
			
			.order-header {
				background: var(--light);
				padding: 15px 20px;
				border-bottom: 1px solid var(--border);
				display: flex;
				justify-content: space-between;
				align-items: center;
				flex-wrap: wrap;
			}
			
			.order-id {
				font-weight: 600;
				color: var(--secondary);
				font-size: 16px;
			}
			
			.order-date {
				color: var(--text-light);
				font-size: 14px;
			}
			
			.order-status {
				background: var(--warning);
				color: var(--dark);
				padding: 4px 12px;
				border-radius: 20px;
				font-size: 12px;
				font-weight: 600;
			}
			
			.order-body {
				padding: 20px;
			}
			
			.order-product {
				display: flex;
				align-items: center;
				margin-bottom: 20px;
				padding-bottom: 20px;
				border-bottom: 1px solid var(--border);
			}
			
			.order-product:last-child {
				border-bottom: none;
				margin-bottom: 0;
				padding-bottom: 0;
			}
			
			.product-image {
				flex: 0 0 80px;
				margin-right: 20px;
			}
			
			.product-image img {
				width: 100%;
				height: auto;
				border-radius: 8px;
				object-fit: cover;
			}
			
			.product-details {
				flex: 1;
			}
			
			.product-name {
				font-size: 16px;
				font-weight: 600;
				margin-bottom: 5px;
			}
			
			.product-name a {
				color: var(--secondary);
				text-decoration: none;
				transition: color 0.3s;
			}
			
			.product-name a:hover {
				color: var(--primary);
			}
			
			.product-info {
				display: flex;
				flex-wrap: wrap;
				gap: 15px;
				margin-top: 10px;
			}
			
			.info-item {
				display: flex;
				flex-direction: column;
			}
			
			.info-label {
				font-size: 12px;
				color: var(--text-light);
				margin-bottom: 3px;
			}
			
			.info-value {
				font-size: 14px;
				font-weight: 600;
				color: var(--secondary);
			}
			
			.info-value.price {
				color: var(--primary);
			}
			
			.info-value.total {
				color: var(--success);
				font-size: 16px;
			}
			
			.order-footer {
				background: var(--light);
				padding: 15px 20px;
				border-top: 1px solid var(--border);
				display: flex;
				justify-content: space-between;
				align-items: center;
				flex-wrap: wrap;
			}
			
			.order-actions {
				display: flex;
				gap: 10px;
			}
			
			.btn {
				padding: 10px 20px;
				border-radius: 6px;
				font-weight: 600;
				cursor: pointer;
				transition: all 0.3s;
				text-decoration: none;
				display: inline-block;
				border: none;
				font-size: 14px;
			}
			
			.btn-primary {
				background: var(--primary);
				color: white;
			}
			
			.btn-primary:hover {
				background: #3a96e0;
				color: white;
			}
			
			.btn-danger {
				background: var(--accent);
				color: white;
			}
			
			.btn-danger:hover {
				background: #d62c1a;
				color: white;
			}
			
			.empty-orders {
				text-align: center;
				padding: 60px 20px;
				background: white;
				border-radius: var(--radius);
				box-shadow: var(--shadow);
			}
			
			.empty-icon {
				font-size: 60px;
				color: #ddd;
				margin-bottom: 20px;
			}
			
			.empty-orders h3 {
				font-size: 24px;
				color: var(--text-light);
				margin-bottom: 15px;
			}
			
			.empty-orders p {
				color: var(--text-light);
				margin-bottom: 25px;
			}
			
			.btn-continue-shopping {
				background: var(--primary);
				color: white;
				padding: 12px 30px;
				border-radius: 6px;
				font-weight: 600;
				text-decoration: none;
				display: inline-block;
				transition: background 0.3s;
			}
			
			.btn-continue-shopping:hover {
				background: #3a96e0;
				color: white;
			}
			
			.proceed-section {
				background: white;
				padding: 20px;
				border-radius: var(--radius);
				box-shadow: var(--shadow);
				margin-top: 30px;
				text-align: center;
			}
			
			.proceed-title {
				font-size: 18px;
				font-weight: 600;
				margin-bottom: 15px;
				color: var(--secondary);
			}
			
			@media (max-width: 768px) {
				.order-header {
					flex-direction: column;
					align-items: flex-start;
					gap: 10px;
				}
				
				.order-product {
					flex-direction: column;
					text-align: center;
				}
				
				.product-image {
					margin-right: 0;
					margin-bottom: 15px;
				}
				
				.product-info {
					justify-content: center;
				}
				
				.order-footer {
					flex-direction: column;
					gap: 15px;
				}
				
				.order-actions {
					width: 100%;
					justify-content: center;
				}
			}
		</style>
	</head>
    <body class="cnt-home">
		
		<!-- ============================================== HEADER ============================================== -->
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>
<!-- ============================================== HEADER : END ============================================== -->

<div class="orders-container">
	<div class="orders-header">
		<nav class="breadcrumb">
			<ul class="list-inline list-unstyled">
				<li><a href="#">Home</a></li>
				<li class='active'>Pending Orders</li>
			</ul>
		</nav>
		<h1>Pending Orders</h1>
		<p>Review and manage your pending orders</p>
	</div>

	<div class="orders-content">
		<form name="cart" method="post">
			<?php 
			$query=mysqli_query($con,"select products.productImage1 as pimg1,products.productName as pname,products.id as c,orders.productId as opid,orders.quantity as qty,products.productPrice as pprice,products.shippingCharge as shippingcharge,orders.paymentMethod as paym,orders.orderDate as odate,orders.id as oid from orders join products on orders.productId=products.id where orders.userId='".$_SESSION['id']."' and orders.paymentMethod is null");
			$cnt=1;
			$num=mysqli_num_rows($query);
			
			if($num>0) {
				while($row=mysqli_fetch_array($query)) {
					$qty = $row['qty'];
					$price = $row['pprice'];
					$shippcharge = $row['shippingcharge'];
					$total = ($qty * $price) + $shippcharge;
			?>
			
			<div class="order-card">
				<div class="order-header">
					<div class="order-id">Order #<?php echo $row['oid']; ?></div>
					<div class="order-date"><?php echo $row['odate']; ?></div>
					<div class="order-status">Pending Payment</div>
				</div>
				
				<div class="order-body">
					<div class="order-product">
						<div class="product-image">
							<img src="admin/productimages/<?php echo $row['opid'];?>/<?php echo $row['pimg1'];?>" alt="<?php echo $row['pname']; ?>">
						</div>
						
						<div class="product-details">
							<div class="product-name">
								<a href="product-details.php?pid=<?php echo $row['opid'];?>">
									<?php echo $row['pname']; ?>
								</a>
							</div>
							
							<div class="product-info">
								<div class="info-item">
									<span class="info-label">QUANTITY</span>
									<span class="info-value"><?php echo $qty; ?></span>
								</div>
								
								<div class="info-item">
									<span class="info-label">UNIT PRICE</span>
									<span class="info-value price">Rs. <?php echo $price; ?></span>
								</div>
								
								<div class="info-item">
									<span class="info-label">SHIPPING</span>
									<span class="info-value">Rs. <?php echo $shippcharge; ?></span>
								</div>
								
								<div class="info-item">
									<span class="info-label">TOTAL</span>
									<span class="info-value total">Rs. <?php echo $total; ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="order-footer">
					<div class="payment-method">
						<strong>Payment Method:</strong> <?php echo $row['paym'] ? $row['paym'] : 'Not selected'; ?>
					</div>
					
					<div class="order-actions">
						<a href="pending-orders.php?id=<?php echo $row['oid']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">
							<i class="fa fa-trash"></i> Delete Order
						</a>
					</div>
				</div>
			</div>
			
			<?php 
				$cnt=$cnt+1;
				} 
			?>
			
			<div class="proceed-section">
				<div class="proceed-title">Ready to complete your purchase?</div>
				<button type="submit" name="ordersubmit" class="btn btn-primary">
					<i class="fa fa-credit-card"></i> PROCEED TO PAYMENT
				</button>
			</div>
			
			<?php } else { ?>
			
			<div class="empty-orders">
				<div class="empty-icon">
					<i class="fa fa-shopping-cart"></i>
				</div>
				<h3>No Pending Orders</h3>
				<p>You don't have any orders waiting for payment.</p>
				<a href="index.php" class="btn-continue-shopping">Continue Shopping</a>
			</div>
			
			<?php } ?>
		</form>
	</div>
</div>

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
</body>
</html>
<?php } ?>
