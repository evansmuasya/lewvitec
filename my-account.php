<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{   
header('location:login.php');
}
else{
	if(isset($_POST['update']))
	{
		$name=$_POST['name'];
		$contactno=$_POST['contactno'];
		$query=mysqli_query($con,"update users set name='$name',contactno='$contactno' where id='".$_SESSION['id']."'");
		if($query)
		{
echo "<script>alert('Your info has been updated');</script>";
		}
	}

date_default_timezone_set('Asia/Kolkata');// change according timezone
$currentTime = date( 'd-m-Y h:i:s A', time () );

if(isset($_POST['submit']))
{
$sql=mysqli_query($con,"SELECT password FROM  users where password='".md5($_POST['cpass'])."' && id='".$_SESSION['id']."'");
$num=mysqli_fetch_array($sql);
if($num>0)
{
 $con=mysqli_query($con,"update students set password='".md5($_POST['newpass'])."', updationDate='$currentTime' where id='".$_SESSION['id']."'");
echo "<script>alert('Password Changed Successfully !!');</script>";
}
else
{
	echo "<script>alert('Current Password not match !!');</script>";
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

	    <title>My Account</title>

	    <!-- Bootstrap Core CSS -->
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
				--radius: 10px;
			}
			
			body {
				font-family: 'Roboto', sans-serif;
				color: var(--text);
				background-color: #f9f9f9;
				line-height: 1.6;
			}
			
			.account-container {
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px;
			}
			
			.account-header {
				background: linear-gradient(135deg, var(--primary), #2a5c9e);
				color: white;
				padding: 25px;
				border-radius: var(--radius);
				margin-bottom: 30px;
				box-shadow: var(--shadow);
			}
			
			.account-header h1 {
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
			
			.account-content {
				display: flex;
				gap: 30px;
				flex-wrap: wrap;
			}
			
			.account-main {
				flex: 1;
				min-width: 300px;
			}
			
			.account-card {
				background: white;
				border-radius: var(--radius);
				overflow: hidden;
				box-shadow: var(--shadow);
				margin-bottom: 25px;
				transition: transform 0.3s ease;
			}
			
			.account-card:hover {
				transform: translateY(-3px);
			}
			
			.card-header {
				background: var(--light);
				padding: 20px;
				border-bottom: 1px solid var(--border);
				display: flex;
				align-items: center;
				cursor: pointer;
			}
			
			.card-header h3 {
				margin: 0;
				font-size: 18px;
				font-weight: 600;
				color: var(--secondary);
				display: flex;
				align-items: center;
			}
			
			.card-header .step-number {
				background: var(--primary);
				color: white;
				width: 30px;
				height: 30px;
				border-radius: 50%;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				margin-right: 15px;
				font-weight: bold;
			}
			
			.card-body {
				padding: 25px;
			}
			
			.form-group {
				margin-bottom: 20px;
			}
			
			.form-group label {
				display: block;
				margin-bottom: 8px;
				font-weight: 500;
				color: var(--secondary);
			}
			
			.form-control {
				width: 100%;
				padding: 12px 15px;
				border: 1px solid var(--border);
				border-radius: 6px;
				font-size: 15px;
				transition: all 0.3s;
			}
			
			.form-control:focus {
				outline: none;
				border-color: var(--primary);
				box-shadow: 0 0 0 3px rgba(78, 172, 253, 0.2);
			}
			
			.form-control:read-only {
				background-color: #f8f9fa;
				color: var(--text-light);
			}
			
			.btn-primary {
				background: var(--primary);
				color: white;
				border: none;
				padding: 12px 25px;
				border-radius: 6px;
				font-weight: 600;
				cursor: pointer;
				transition: background 0.3s;
				display: inline-block;
				text-decoration: none;
			}
			
			.btn-primary:hover {
				background: #3a96e0;
			}
			
			.alert {
				padding: 12px 15px;
				border-radius: 6px;
				margin-bottom: 20px;
				font-weight: 500;
			}
			
			.alert-success {
				background: #d4edda;
				color: #155724;
				border: 1px solid #c3e6cb;
			}
			
			.alert-error {
				background: #f8d7da;
				color: #721c24;
				border: 1px solid #f5c6cb;
			}
			
			.account-sidebar {
				width: 300px;
				background: white;
				border-radius: var(--radius);
				box-shadow: var(--shadow);
				padding: 20px;
				align-self: flex-start;
			}
			
			.sidebar-header {
				display: flex;
				align-items: center;
				margin-bottom: 20px;
				padding-bottom: 15px;
				border-bottom: 1px solid var(--border);
			}
			
			.user-avatar {
				width: 60px;
				height: 60px;
				border-radius: 50%;
				background: var(--primary);
				display: flex;
				align-items: center;
				justify-content: center;
				color: white;
				font-size: 24px;
				font-weight: bold;
				margin-right: 15px;
			}
			
			.user-info h4 {
				margin: 0;
				font-size: 16px;
				color: var(--secondary);
			}
			
			.user-info p {
				margin: 3px 0 0;
				color: var(--text-light);
				font-size: 13px;
			}
			
			.sidebar-menu {
				list-style: none;
				padding: 0;
				margin: 0;
			}
			
			.sidebar-menu li {
				margin-bottom: 10px;
			}
			
			.sidebar-menu a {
				display: flex;
				align-items: center;
				padding: 12px 15px;
				color: var(--text);
				text-decoration: none;
				border-radius: 6px;
				transition: all 0.3s;
			}
			
			.sidebar-menu a:hover, .sidebar-menu a.active {
				background: var(--primary);
				color: white;
			}
			
			.sidebar-menu i {
				margin-right: 10px;
				width: 20px;
				text-align: center;
			}
			
			@media (max-width: 768px) {
				.account-content {
					flex-direction: column;
				}
				
				.account-sidebar {
					width: 100%;
				}
			}
		</style>
		
<script type="text/javascript">
function valid()
{
if(document.chngpwd.cpass.value=="")
{
alert("Current Password Filed is Empty !!");
document.chngpwd.cpass.focus();
return false;
}
else if(document.chngpwd.newpass.value=="")
{
alert("New Password Filed is Empty !!");
document.chngpwd.newpass.focus();
return false;
}
else if(document.chngpwd.cnfpass.value=="")
{
alert("Confirm Password Filed is Empty !!");
document.chngpwd.cnfpass.focus();
return false;
}
else if(document.chngpwd.newpass.value!= document.chngpwd.cnfpass.value)
{
alert("Password and Confirm Password Field do not match  !!");
document.chngpwd.cnfpass.focus();
return false;
}
return true;
}
</script>

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

<div class="account-container">
	<div class="account-header">
		<nav class="breadcrumb">
			<ul class="list-inline list-unstyled">
				<li><a href="#">Home</a></li>
				<li class='active'>My Account</li>
			</ul>
		</nav>
		<h1>My Account</h1>
		<p>Manage your personal information and security settings</p>
	</div>

	<div class="account-content">
		<div class="account-main">
			<!-- Personal Info Card -->
			<div class="account-card">
				<div class="card-header" data-toggle="collapse" data-target="#personalInfo">
					<h3><span class="step-number">1</span>Personal Information</h3>
				</div>
				<div id="personalInfo" class="card-body collapse show">
					<?php
					$query=mysqli_query($con,"select * from users where id='".$_SESSION['id']."'");
					while($row=mysqli_fetch_array($query))
					{
					?>
					<form class="register-form" role="form" method="post">
						<div class="form-group">
							<label for="name">Full Name<span>*</span></label>
							<input type="text" class="form-control" value="<?php echo $row['name'];?>" id="name" name="name" required="required">
						</div>

						<div class="form-group">
							<label for="exampleInputEmail1">Email Address <span>*</span></label>
							<input type="email" class="form-control" id="exampleInputEmail1" value="<?php echo $row['email'];?>" readonly>
						</div>
						
						<div class="form-group">
							<label for="contactno">Contact Number <span>*</span></label>
							<input type="text" class="form-control" id="contactno" name="contactno" required="required" value="<?php echo $row['contactno'];?>" maxlength="10">
						</div>
						
						<button type="submit" name="update" class="btn-primary">Update Profile</button>
					</form>
					<?php } ?>
				</div>
			</div>

			<!-- Change Password Card -->
			<div class="account-card">
				<div class="card-header" data-toggle="collapse" data-target="#changePassword">
					<h3><span class="step-number">2</span>Change Password</h3>
				</div>
				<div id="changePassword" class="card-body collapse">
					<form class="register-form" role="form" method="post" name="chngpwd" onSubmit="return valid();">
						<div class="form-group">
							<label for="cpass">Current Password<span>*</span></label>
							<input type="password" class="form-control" id="cpass" name="cpass" required="required">
						</div>

						<div class="form-group">
							<label for="newpass">New Password <span>*</span></label>
							<input type="password" class="form-control" id="newpass" name="newpass">
						</div>
						
						<div class="form-group">
							<label for="cnfpass">Confirm Password <span>*</span></label>
							<input type="password" class="form-control" id="cnfpass" name="cnfpass" required="required">
						</div>
						
						<button type="submit" name="submit" class="btn-primary">Change Password</button>
					</form>
				</div>
			</div>
		</div>

		<div class="account-sidebar">
			<div class="sidebar-header">
				<div class="user-avatar">
					<?php 
					$query=mysqli_query($con,"select * from users where id='".$_SESSION['id']."'");
					$row=mysqli_fetch_array($query);
					echo strtoupper(substr($row['name'], 0, 1)); 
					?>
				</div>
				<div class="user-info">
					<h4><?php echo $row['name']; ?></h4>
					<p>Member since <?php echo date('M Y', strtotime($row['regDate'])); ?></p>
				</div>
			</div>

			<ul class="sidebar-menu">
				<li><a href="my-account.php" class="active"><i class="fa fa-user"></i> Account Overview</a></li>
				<li><a href="my-orders.php"><i class="fa fa-shopping-bag"></i> My Orders</a></li>
				<li><a href="my-wishlist.php"><i class="fa fa-heart"></i> My Wishlist</a></li>
				<li><a href="my-addresses.php"><i class="fa fa-map-marker"></i> My Addresses</a></li>
				<li><a href="change-password.php"><i class="fa fa-lock"></i> Change Password</a></li>
				<li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
			</ul>
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
			
			// Toggle card sections
			$('.card-header').click(function() {
				var target = $(this).data('target');
				$(target).collapse('toggle');
			});
		});

		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
</body>
</html>
<?php } ?>