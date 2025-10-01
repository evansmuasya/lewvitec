<?php
session_start();
error_reporting(0);
include("include/config.php");
if(isset($_POST['submit']))
{
	$username=$_POST['username'];
	$password=md5($_POST['password']);
$ret=mysqli_query($con,"SELECT * FROM admin WHERE username='$username' and password='$password'");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$extra="change-password.php";
$_SESSION['alogin']=$_POST['username'];
$_SESSION['id']=$num['id'];

header("location:change-password.php");
exit();
}
else
{
$_SESSION['errmsg']="Invalid username or password";
header("location:index.php");
exit();
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lewvitec | Admin Portal</title>
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
		}
		
		body {
			background: linear-gradient(135deg, var(--secondary-color) 0%, var(--dark-color) 100%);
			font-family: 'Open Sans', sans-serif;
			height: 100vh;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}
		
		.navbar {
			display: none;
		}
		
		.footer {
			position: fixed;
			bottom: 0;
			width: 100%;
			background: rgba(44, 62, 80, 0.8);
			color: white;
			text-align: center;
			padding: 10px 0;
		}
		
		.wrapper {
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 20px;
		}
		
		.login-container {
			background: white;
			border-radius: 10px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
			width: 100%;
			max-width: 400px;
			overflow: hidden;
		}
		
		.login-header {
			background: var(--primary-color);
			color: white;
			padding: 20px;
			text-align: center;
		}
		
		.login-header h3 {
			margin: 0;
			font-weight: 600;
		}
		
		.login-header p {
			margin: 5px 0 0;
			font-size: 14px;
			opacity: 0.9;
		}
		
		.login-body {
			padding: 20px;
		}
		
		.control-group {
			margin-bottom: 15px;
		}
		
		.controls input {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			transition: all 0.3s;
		}
		
		.controls input:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
			outline: none;
		}
		
		.btn-login {
			background: var(--primary-color);
			color: white;
			border: none;
			padding: 12px 20px;
			width: 100%;
			border-radius: 5px;
			cursor: pointer;
			font-weight: 600;
			transition: all 0.3s;
		}
		
		.btn-login:hover {
			background: #2980b9;
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
		}
		
		.error-message {
			background: #ffecec;
			color: var(--accent-color);
			padding: 10px;
			border-radius: 5px;
			margin-bottom: 15px;
			border-left: 4px solid var(--accent-color);
			text-align: center;
		}
		
		.lewwitec-logo {
			font-size: 24px;
			font-weight: bold;
			color: white;
			letter-spacing: 1px;
			text-align: center;
			margin-bottom: 20px;
		}
		
		.lewwitec-logo span {
			color: var(--primary-color);
		}
	</style>
</head>
<body>

	<div class="lewwitec-logo">LEW<span>VITEC</span> ADMIN</div>

	<div class="wrapper">
		<div class="login-container">
			<div class="login-header">
				<h3>Secure Admin Portal</h3>
				<p>Sign in to access the dashboard</p>
			</div>
			<form class="form-vertical" method="post">
				<div class="login-body">
					<?php if(isset($_SESSION['errmsg']) && $_SESSION['errmsg'] != "") { ?>
					<div class="error-message">
						<?php echo htmlentities($_SESSION['errmsg']); ?>
					</div>
					<?php 
						$_SESSION['errmsg'] = "";
					} ?>
					
					<div class="control-group">
						<div class="controls">
							<input type="text" id="inputEmail" name="username" placeholder="Username" required>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="password" id="inputPassword" name="password" placeholder="Password" required>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<button type="submit" class="btn-login" name="submit">Login</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="footer">
		<b class="copyright">&copy; 2023 Lewvitec Admin Portal. All rights reserved.</b>
	</div>
	<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>