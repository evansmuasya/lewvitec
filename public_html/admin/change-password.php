<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');// change according timezone
$currentTime = date( 'd-m-Y h:i:s A', time () );


if(isset($_POST['submit']))
{
$sql=mysqli_query($con,"SELECT password FROM  admin where password='".md5($_POST['password'])."' && username='".$_SESSION['alogin']."'");
$num=mysqli_fetch_array($sql);
if($num>0)
{
 $con=mysqli_query($con,"update admin set password='".md5($_POST['newpassword'])."', updationDate='$currentTime' where username='".$_SESSION['alogin']."'");
$_SESSION['msg']="Password Changed Successfully !!";
}
else
{
$_SESSION['msg']="Old Password not match !!";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lewvitec Admin | Change Password</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<style>
		:root {
			--primary-color: #3498db;
			--secondary-color: #2c3e50;
			--accent-color: #e74c3c;
			--light-color: #ecf0f1;
			--dark-color: #2c3e50;
			--success-color: #2ecc71;
		}
		
		body {
			background-color: #f5f5f5;
			font-family: 'Open Sans', sans-serif;
		}
		
		.navbar {
			background: var(--secondary-color);
			border-bottom: 3px solid var(--primary-color);
		}
		
		.navbar .brand {
			color: white !important;
			font-weight: 600;
		}
		
		.navbar .brand span {
			color: var(--primary-color);
		}
		
		.wrapper {
			background: #f5f5f5;
		}
		
		.module {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
			border: none;
			margin-bottom: 20px;
		}
		
		.module-head {
			background: var(--primary-color);
			color: white;
			border-radius: 8px 8px 0 0;
			padding: 15px 20px;
		}
		
		.module-head h3 {
			margin: 0;
			font-weight: 600;
		}
		
		.module-body {
			padding: 20px;
		}
		
		.control-group {
			margin-bottom: 20px;
		}
		
		.control-label {
			font-weight: 600;
			color: var(--dark-color);
			margin-bottom: 8px;
		}
		
		.controls input {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			transition: all 0.3s;
			box-sizing: border-box;
		}
		
		.controls input:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
			outline: none;
		}
		
		.btn-submit {
			background: var(--primary-color);
			color: white;
			border: none;
			padding: 12px 25px;
			border-radius: 5px;
			cursor: pointer;
			font-weight: 600;
			transition: all 0.3s;
		}
		
		.btn-submit:hover {
			background: #2980b9;
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
		}
		
		.alert {
			border-radius: 5px;
			border: none;
			padding: 15px;
		}
		
		.alert-success {
			background-color: #d4edda;
			color: #155724;
			border-left: 4px solid var(--success-color);
		}
		
		.sidebar {
			background: var(--secondary-color);
			border-radius: 8px;
			box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
		}
		
		.nav-list li a {
			color: white;
			border-bottom: 1px solid rgba(255, 255, 255, 0.1);
			padding: 12px 20px;
		}
		
		.nav-list li a:hover {
			background: rgba(52, 152, 219, 0.2);
			color: white;
		}
		
		.nav-list li.active a {
			background: var(--primary-color);
			color: white;
		}
		
		.footer {
			background: var(--secondary-color);
			color: white;
			text-align: center;
			padding: 15px;
			margin-top: 20px;
		}
		
		.content {
			padding: 20px;
		}
		
		.password-strength {
			height: 5px;
			width: 100%;
			background: #eee;
			border-radius: 3px;
			margin-top: 5px;
			display: none;
		}
		
		.password-strength-meter {
			height: 100%;
			border-radius: 3px;
			width: 0%;
			transition: width 0.3s;
		}
	</style>
	<script type="text/javascript">
	function valid()
	{
	if(document.chngpwd.password.value=="")
	{
	alert("Current Password Filed is Empty !!");
	document.chngpwd.password.focus();
	return false;
	}
	else if(document.chngpwd.newpassword.value=="")
	{
	alert("New Password Filed is Empty !!");
	document.chngpwd.newpassword.focus();
	return false;
	}
	else if(document.chngpwd.confirmpassword.value=="")
	{
	alert("Confirm Password Filed is Empty !!");
	document.chngpwd.confirmpassword.focus();
	return false;
	}
	else if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
	{
	alert("Password and Confirm Password Field do not match  !!");
	document.chngpwd.confirmpassword.focus();
	return false;
	}
	return true;
	}
	
	// Additional function to check password strength
	function checkPasswordStrength(password) {
		var strength = 0;
		if (password.length >= 8) strength++;
		if (password.match(/[a-z]+/)) strength++;
		if (password.match(/[A-Z]+/)) strength++;
		if (password.match(/[0-9]+/)) strength++;
		if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength++;
		
		return strength;
	}
	
	// Function to update password strength meter
	function updatePasswordStrength() {
		var password = document.chngpwd.newpassword.value;
		var strengthMeter = document.getElementById('password-strength-meter');
		var strengthContainer = document.getElementById('password-strength');
		
		if (password.length > 0) {
			strengthContainer.style.display = 'block';
			var strength = checkPasswordStrength(password);
			
			if (strength <= 2) {
				strengthMeter.style.width = '33%';
				strengthMeter.style.background = '#e74c3c';
			} else if (strength <= 4) {
				strengthMeter.style.width = '66%';
				strengthMeter.style.background = '#f39c12';
			} else {
				strengthMeter.style.width = '100%';
				strengthMeter.style.background = '#2ecc71';
			}
		} else {
			strengthContainer.style.display = 'none';
		}
	}
	</script>
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
<?php include('include/sidebar.php');?>				
			<div class="span9">
					<div class="content">

						<div class="module">
							<div class="module-head">
								<h3>Change Password</h3>
							</div>
							<div class="module-body">

									<?php if(isset($_POST['submit']))
{?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
									</div>
<?php } ?>
									<br />

			<form class="form-horizontal row-fluid" name="chngpwd" method="post" onSubmit="return valid();">
									
<div class="control-group">
<label class="control-label" for="basicinput">Current Password</label>
<div class="controls">
<input type="password" placeholder="Enter your current password"  name="password" class="span8 tip" required>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput">New Password</label>
<div class="controls">
<input type="password" placeholder="Enter your new password"  name="newpassword" class="span8 tip" required onkeyup="updatePasswordStrength()">
<div id="password-strength" class="password-strength">
	<div id="password-strength-meter" class="password-strength-meter"></div>
</div>
<small class="help-block">Password must be at least 8 characters with uppercase, lowercase, number and special character.</small>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Confirm New Password</label>
<div class="controls">
<input type="password" placeholder="Confirm your new password"  name="confirmpassword" class="span8 tip" required>
</div>
</div>



										

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn-submit">Update Password</button>
											</div>
										</div>
									</form>
							</div>
						</div>

						
						
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->

<?php include('include/footer.php');?>

	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
</body>
<?php } ?>