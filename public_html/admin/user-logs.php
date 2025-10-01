<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
}
else{

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lewvitec Admin | User Logs</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap.css">
	<style>
		:root {
			--primary-color: #3498db;
			--secondary-color: #2c3e50;
			--accent-color: #e74c3c;
			--light-color: #ecf0f1;
			--dark-color: #2c3e50;
			--success-color: #2ecc71;
			--warning-color: #f39c12;
		}
		
		body {
			background-color: #f5f5f5;
			font-family: 'Open Sans', sans-serif;
			font-size: 14px;
		}
		
		.navbar {
			background: var(--secondary-color);
			border-bottom: 3px solid var(--primary-color);
		}
		
		.navbar .brand {
			color: white !important;
			font-weight: 600;
			font-size: 16px;
		}
		
		.navbar .brand span {
			color: var(--primary-color);
		}
		
		.wrapper {
			background: #f5f5f5;
			padding: 10px 0;
		}
		
		.container {
			padding: 0 10px;
			max-width: 100%;
		}
		
		.module {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			border: none;
			margin-bottom: 15px;
			overflow: hidden;
		}
		
		.module-head {
			background: var(--primary-color);
			color: white;
			border-radius: 8px 8px 0 0;
			padding: 12px 15px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: wrap;
			gap: 10px;
		}
		
		.module-head h3 {
			margin: 0;
			font-weight: 600;
			font-size: 16px;
		}
		
		.module-body {
			padding: 15px;
		}
		
		/* Table container for horizontal scrolling */
		.table-container {
			width: 100%;
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			border: 1px solid #e0e0e0;
			border-radius: 5px;
			background: white;
		}
		
		.userlog-table {
			width: 100%;
			border-collapse: collapse;
			min-width: 800px; /* Minimum width to ensure all columns are visible */
		}
		
		.userlog-table thead th {
			background-color: var(--secondary-color);
			color: white;
			font-weight: 600;
			padding: 12px 15px;
			border: none;
			font-size: 13px;
			text-align: left;
			position: sticky;
			top: 0;
			white-space: nowrap;
		}
		
		.userlog-table tbody td {
			padding: 12px 15px;
			border-bottom: 1px solid #eaeaea;
			vertical-align: middle;
			font-size: 13px;
		}
		
		.userlog-table tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		
		.userlog-table tbody tr:hover {
			background-color: #f1f7fd;
		}
		
		.log-count {
			background: white;
			color: var(--primary-color);
			padding: 3px 8px;
			border-radius: 12px;
			font-weight: 600;
			font-size: 12px;
		}
		
		.page-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 15px;
			flex-wrap: wrap;
			gap: 10px;
		}
		
		.page-header h2 {
			font-size: 18px;
			margin: 0;
			color: var(--dark-color);
		}
		
		.status-badge {
			padding: 4px 8px;
			border-radius: 12px;
			font-size: 11px;
			font-weight: 600;
			display: inline-block;
			text-align: center;
		}
		
		.status-success {
			background: #e8f5e9;
			color: var(--success-color);
		}
		
		.status-failed {
			background: #ffebee;
			color: var(--accent-color);
		}
		
		.ip-address {
			background: #f0f4f8;
			color: var(--secondary-color);
			padding: 4px 8px;
			border-radius: 4px;
			font-family: monospace;
			font-size: 12px;
		}
		
		.email-cell {
			max-width: 200px;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		
		/* Scroll indicator for mobile */
		.scroll-indicator {
			display: none;
			text-align: center;
			padding: 8px;
			background: #f8f9fa;
			color: #6c757d;
			font-size: 12px;
			border-top: 1px solid #dee2e6;
		}
		
		.scroll-indicator i {
			margin: 0 5px;
			animation: bounce 2s infinite;
		}
		
		@keyframes bounce {
			0%, 20%, 50%, 80%, 100% {transform: translateX(0);}
			40% {transform: translateX(-5px);}
			60% {transform: translateX(5px);}
		}
		
		/* DataTables styling */
		.dataTables_wrapper .dataTables_paginate {
			margin-top: 15px;
			display: flex;
			flex-wrap: wrap;
			gap: 5px;
			justify-content: center;
		}
		
		.dataTables_wrapper .dataTables_paginate .paginate_button {
			border: 1px solid #ddd;
			padding: 5px 8px;
			margin: 0 2px;
			border-radius: 3px;
			color: var(--secondary-color);
			font-size: 12px;
			min-width: 30px;
			text-align: center;
		}
		
		.dataTables_wrapper .dataTables_paginate .paginate_button.current,
		.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
			background: var(--primary-color);
			color: white;
			border-color: var(--primary-color);
		}
		
		.dataTables_wrapper .dataTables_filter input {
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 6px 10px;
			margin-left: 5px;
			width: 150px;
			font-size: 13px;
		}
		
		.dataTables_info {
			color: #666;
			padding-top: 10px;
			font-size: 12px;
			text-align: center;
		}
		
		/* Mobile-specific styles */
		@media (max-width: 767px) {
			body {
				font-size: 13px;
			}
			
			.container {
				padding: 0 5px;
			}
			
			.module-head {
				padding: 10px;
				flex-direction: column;
				align-items: flex-start;
			}
			
			.module-body {
				padding: 10px;
			}
			
			.page-header {
				flex-direction: column;
				align-items: flex-start;
			}
			
			.span9 {
				width: 100%;
				float: none;
			}
			
			.row {
				margin: 0;
			}
			
			/* Show scroll indicator on mobile */
			.scroll-indicator {
				display: block;
			}
			
			/* Adjust table for mobile */
			.userlog-table thead th {
				padding: 10px 12px;
				font-size: 12px;
			}
			
			.userlog-table tbody td {
				padding: 10px 12px;
				font-size: 12px;
			}
			
			.dataTables_wrapper .dataTables_filter {
				width: 100%;
				margin-bottom: 10px;
			}
			
			.dataTables_wrapper .dataTables_filter input {
				width: 100%;
				margin-left: 0;
				margin-top: 5px;
			}
		}
		
		@media (max-width: 480px) {
			.userlog-table thead th {
				padding: 8px 10px;
				font-size: 11px;
			}
			
			.userlog-table tbody td {
				padding: 8px 10px;
				font-size: 11px;
			}
			
			.email-cell {
				max-width: 120px;
			}
		}
		
		/* Sidebar adjustment for mobile */
		@media (max-width: 979px) {
			.span9 {
				width: 100%;
			}
		}
		
	</style>
</head>
<body>
<?php include('include/header.php');?>

	<div class="wrapper">
		<div class="container">
			<div class="row">
<?php include('include/sidebar.php');?>				
			<div class="span9">
					<div class="content">
						<div class="page-header">
							<h2>User Login History</h2>
						</div>

						<div class="module">
							<div class="module-head">
								<h3>User Activity Logs</h3>
								<span class="log-count">
									<?php 
										$count_query = mysqli_query($con, "SELECT COUNT(*) as total FROM userlog");
										$count_data = mysqli_fetch_assoc($count_query);
										echo $count_data['total'] . " Log Entries";
									?>
								</span>
							</div>
							<div class="module-body">
								<div class="table-container">
									<table cellpadding="0" cellspacing="0" border="0" class="userlog-table display" width="100%">
										<thead>
											<tr>
												<th>#</th>
												<th>User Email</th>
												<th>User IP</th>
												<th>Login Time</th>
												<th>Logout Time</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											$query = mysqli_query($con, "select * from userlog");
											$cnt = 1;
											while($row = mysqli_fetch_array($query)) {
											?>									
											<tr>
												<td><?php echo htmlentities($cnt);?></td>
												<td class="email-cell"><?php echo htmlentities($row['userEmail']);?></td>
												<td><span class="ip-address"><?php echo htmlentities($row['userip']);?></span></td>
												<td><?php echo htmlentities($row['loginTime']);?></td>
												<td><?php echo htmlentities($row['logout']); ?></td>
												<td>
													<?php 
													$st = $row['status'];
													if($st == 1) {
														echo '<span class="status-badge status-success">Successful</span>';
													} else {
														echo '<span class="status-badge status-failed">Failed</span>';
													}
													?>
												</td>
											</tr>
											<?php $cnt = $cnt + 1; } ?>
										</tbody>
									</table>
									<div class="scroll-indicator">
										<i class="icon-arrow-left"></i> Scroll horizontally to view all columns <i class="icon-arrow-right"></i>
									</div>
								</div>
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
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap.js"></script>
	

</body>
<?php } ?>