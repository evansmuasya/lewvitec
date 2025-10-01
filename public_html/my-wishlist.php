<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{   
header('location:login.php');
}
else{
// Code forProduct deletion from  wishlist	
$wid=intval($_GET['del']);
if(isset($_GET['del']))
{
$query=mysqli_query($con,"delete from wishlist where id='$wid'");
}

if(isset($_GET['action']) && $_GET['action']=="add"){
	$id=intval($_GET['id']);
	$query=mysqli_query($con,"delete from wishlist where productId='$id'");
	if(isset($_SESSION['cart'][$id])){
		$_SESSION['cart'][$id]['quantity']++;
	}else{
		$sql_p="SELECT * FROM products WHERE id={$id}";
		$query_p=mysqli_query($con,$sql_p);
		if(mysqli_num_rows($query_p)!=0){
			$row_p=mysqli_fetch_array($query_p);
			$_SESSION['cart'][$row_p['id']]=array("quantity" => 1, "price" => $row_p['productPrice']);	
header('location:my-wishlist.php');
}
		else{
			$message="Product ID is invalid";
		}
	}
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

	    <title>My Wishlist</title>
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


		<link rel="shortcut icon" href="assets/images/favicon.ico">
		
		<style>
			:root {
				--primary: #4eacfd;
				--secondary: #2c3e50;
				--accent: #e74c3c;
				--light: #f8f9fa;
				--dark: #343a40;
				--success: #28a745;
				--border: #dee2e6;
				--text: #333;
				--text-light: #6c757d;
				--shadow: 0 4px 12px rgba(0,0,0,0.08);
			}
			
			body {
				font-family: 'Roboto', sans-serif;
				color: var(--text);
				background-color: #f9f9f9;
			}
			
			.wishlist-container {
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px;
			}
			
			.wishlist-header {
				background: linear-gradient(135deg, var(--primary), #2a5c9e);
				color: white;
				padding: 25px;
				border-radius: 10px;
				margin-bottom: 30px;
				box-shadow: var(--shadow);
			}
			
			.wishlist-header h1 {
				font-size: 28px;
				font-weight: 700;
				margin: 0;
			}
			
			.breadcrumb {
				padding: 0;
				background: transparent;
				margin-bottom: 15px;
			}
			
			.wishlist-card {
				background: white;
				border-radius: 10px;
				overflow: hidden;
				box-shadow: var(--shadow);
				transition: transform 0.3s ease, box-shadow 0.3s ease;
				margin-bottom: 20px;
			}
			
			.wishlist-card:hover {
				transform: translateY(-5px);
				box-shadow: 0 8px 20px rgba(0,0,0,0.12);
			}
			
			.wishlist-item {
				display: flex;
				align-items: center;
				padding: 20px;
			}
			
			.product-image {
				flex: 0 0 120px;
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
				font-size: 18px;
				font-weight: 600;
				margin-bottom: 8px;
			}
			
			.product-name a {
				color: var(--secondary);
				text-decoration: none;
				transition: color 0.3s;
			}
			
			.product-name a:hover {
				color: var(--primary);
			}
			
			.rating {
				margin-bottom: 10px;
			}
			
			.rate {
				color: #FFD700;
				font-size: 14px;
			}
			
			.non-rate {
				color: #ddd;
				font-size: 14px;
			}
			
			.review {
				font-size: 13px;
				color: var(--text-light);
				margin-left: 8px;
			}
			
			.price {
				font-size: 18px;
				font-weight: 700;
				color: var(--primary);
				margin-bottom: 15px;
			}
			
			.price span {
				font-size: 14px;
				color: var(--text-light);
				text-decoration: line-through;
				margin-left: 8px;
			}
			
			.action-buttons {
				display: flex;
				gap: 10px;
			}
			
			.btn-add-cart {
				background: var(--primary);
				color: white;
				border: none;
				padding: 10px 20px;
				border-radius: 6px;
				font-weight: 600;
				cursor: pointer;
				transition: background 0.3s;
				text-decoration: none;
				display: inline-block;
			}
			
			.btn-add-cart:hover {
				background: #3a96e0;
				color: white;
			}
			
			.btn-remove {
				background: #fff;
				color: var(--accent);
				border: 1px solid var(--accent);
				padding: 10px 15px;
				border-radius: 6px;
				font-weight: 600;
				cursor: pointer;
				transition: all 0.3s;
				text-decoration: none;
				display: inline-block;
			}
			
			.btn-remove:hover {
				background: var(--accent);
				color: white;
			}
			
			.empty-wishlist {
				text-align: center;
				padding: 60px 20px;
				background: white;
				border-radius: 10px;
				box-shadow: var(--shadow);
			}
			
			.empty-wishlist-icon {
				font-size: 60px;
				color: #ddd;
				margin-bottom: 20px;
			}
			
			.empty-wishlist h3 {
				font-size: 24px;
				color: var(--text-light);
				margin-bottom: 15px;
			}
			
			.empty-wishlist p {
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
			
			@media (max-width: 768px) {
				.wishlist-item {
					flex-direction: column;
					text-align: center;
				}
				
				.product-image {
					margin-right: 0;
					margin-bottom: 15px;
					flex: 0 0 auto;
				}
				
				.action-buttons {
					justify-content: center;
				}
			}
		</style>
	</head>
    <body class="cnt-home">
<header class="header-style-1">

	<!-- ============================================== TOP MENU ============================================== -->
<?php include('includes/top-header.php');?>
<!-- ============================================== TOP MENU : END ============================================== -->
<?php include('includes/main-header.php');?>
	<!-- ============================================== NAVBAR ============================================== -->
<?php include('includes/menu-bar.php');?>
<!-- ============================================== NAVBAR : END ============================================== -->

</header>

<!-- ============================================== HEADER : END ============================================== -->
<div class="wishlist-container">
	<div class="wishlist-header">
		<nav class="breadcrumb">
			<ul class="list-inline list-unstyled">
				<li><a href="home.html">Home</a></li>
				<li class='active'>Wishlist</li>
			</ul>
		</nav>
		<h1>My Wishlist</h1>
	</div>

	<div class="my-wishlist-page">
		<div class="row">
			<div class="col-md-12 my-wishlist">
	<?php
	$ret=mysqli_query($con,"select products.productName as pname,products.productName as proid,products.productImage1 as pimage,products.productPrice as pprice,wishlist.productId as pid,wishlist.id as wid from wishlist join products on products.id=wishlist.productId where wishlist.userId='".$_SESSION['id']."'");
	$num=mysqli_num_rows($ret);
		if($num>0)
		{
	while ($row=mysqli_fetch_array($ret)) {
	?>
				<div class="wishlist-card">
					<div class="wishlist-item">
						<div class="product-image">
							<img src="admin/productimages/<?php echo htmlentities($row['pid']);?>/<?php echo htmlentities($row['pimage']);?>" alt="<?php echo htmlentities($row['pname']);?>">
						</div>
						<div class="product-details">
							<div class="product-name"><a href="product-details.php?pid=<?php echo htmlentities($pd=$row['pid']);?>"><?php echo htmlentities($row['pname']);?></a></div>
	<?php $rt=mysqli_query($con,"select * from productreviews where productId='$pd'");
	$num=mysqli_num_rows($rt);
	{
	?>
							<div class="rating">
								<i class="fa fa-star rate"></i>
								<i class="fa fa-star rate"></i>
								<i class="fa fa-star rate"></i>
								<i class="fa fa-star rate"></i>
								<i class="fa fa-star non-rate"></i>
								<span class="review">( <?php echo htmlentities($num);?> Reviews )</span>
							</div>
							<?php } ?>
							<div class="price">Rs. 
								<?php echo htmlentities($row['pprice']);?>.00
								<span>$900.00</span>
							</div>
							<div class="action-buttons">
								<a href="my-wishlist.php?page=product&action=add&id=<?php echo $row['pid']; ?>" class="btn-add-cart">Add to cart</a>
								<a href="my-wishlist.php?del=<?php echo htmlentities($row['wid']);?>" onClick="return confirm('Are you sure you want to delete?')" class="btn-remove"><i class="fa fa-times"></i> Remove</a>
							</div>
						</div>
					</div>
				</div>
				<?php } } else{ ?>
				<div class="empty-wishlist">
					<div class="empty-wishlist-icon">
						<i class="fa fa-heart-o"></i>
					</div>
					<h3>Your Wishlist is Empty</h3>
					<p>You haven't added any products to your wishlist yet.</p>
					<a href="index.php" class="btn-continue-shopping">Continue Shopping</a>
				</div>
				<?php } ?>
			</div>
		</div>
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