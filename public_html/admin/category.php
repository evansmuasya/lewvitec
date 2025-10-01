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
	$category=$_POST['category'];
	$description=$_POST['description'];
$sql=mysqli_query($con,"insert into category(categoryName,categoryDescription) values('$category','$description')");
$_SESSION['msg']="Category Created !!";

}

if(isset($_GET['del']))
{
    mysqli_query($con,"delete from category where id = '".$_GET['id']."'");
    $_SESSION['delmsg']="Category deleted !!";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lewvitec Admin | Category Management</title>
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
			margin-bottom: 25px;
			overflow: hidden;
		}
		
		.module-head {
			background: var(--primary-color);
			color: white;
			border-radius: 8px 8px 0 0;
			padding: 15px 20px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		
		.module-head h3 {
			margin: 0;
			font-weight: 600;
			font-size: 18px;
		}
		
		.module-body {
			padding: 20px;
		}
		
		.alert {
			border-radius: 5px;
			border: none;
			padding: 15px;
			margin-bottom: 20px;
		}
		
		.alert-success {
			background-color: #d4edda;
			color: #155724;
			border-left: 4px solid var(--success-color);
		}
		
		.alert-error {
			background-color: #f8d7da;
			color: #721c24;
			border-left: 4px solid var(--accent-color);
		}
		
		.control-group {
			margin-bottom: 20px;
		}
		
		.control-label {
			font-weight: 600;
			color: var(--dark-color);
			margin-bottom: 8px;
			display: block;
		}
		
		.controls input, .controls textarea {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			transition: all 0.3s;
			box-sizing: border-box;
			font-family: 'Open Sans', sans-serif;
		}
		
		.controls input:focus, .controls textarea:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
			outline: none;
		}
		
		.controls textarea {
			min-height: 100px;
			resize: vertical;
		}
		
		.btn-primary {
			background: var(--primary-color);
			color: white;
			border: none;
			padding: 12px 25px;
			border-radius: 5px;
			cursor: pointer;
			font-weight: 600;
			transition: all 0.3s;
		}
		
		.btn-primary:hover {
			background: #2980b9;
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
		}
		
		/* DataTables Styling */
		table.dataTable {
			border-collapse: collapse !important;
			border-radius: 5px;
			overflow: hidden;
			box-shadow: 0 0 10px rgba(0,0,0,0.03);
			width: 100% !important;
		}
		
		table.dataTable thead th {
			background-color: var(--secondary-color);
			color: white;
			font-weight: 600;
			padding: 12px 15px;
			border: none;
		}
		
		table.dataTable tbody td {
			padding: 12px 15px;
			border-bottom: 1px solid #eaeaea;
			vertical-align: middle;
		}
		
		table.dataTable tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		
		table.dataTable tbody tr:hover {
			background-color: #f1f7fd;
		}
		
		.dataTables_wrapper .dataTables_paginate {
			margin-top: 15px;
		}
		
		.dataTables_wrapper .dataTables_paginate .paginate_button {
			border: 1px solid #ddd;
			padding: 5px 10px;
			margin: 0 2px;
			border-radius: 3px;
			color: var(--secondary-color);
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
			padding: 5px 10px;
			margin-left: 5px;
		}
		
		.dataTables_info {
			color: #666;
			padding-top: 10px;
		}
		
		.action-buttons {
			display: flex;
			gap: 8px;
		}
		
		.action-btn {
			padding: 6px 10px;
			border-radius: 4px;
			color: white;
			text-decoration: none;
			transition: all 0.3s;
			display: inline-flex;
			align-items: center;
			justify-content: center;
		}
		
		.action-btn.edit {
			background: var(--primary-color);
		}
		
		.action-btn.edit:hover {
			background: #2980b9;
			transform: translateY(-1px);
			color: white;
		}
		
		.action-btn.delete {
			background: var(--accent-color);
		}
		
		.action-btn.delete:hover {
			background: #c0392b;
			transform: translateY(-1px);
			color: white;
		}
		
		.action-btn i {
			margin-right: 4px;
			font-size: 14px;
		}
		
		.category-count {
			background: white;
			color: var(--primary-color);
			padding: 3px 8px;
			border-radius: 12px;
			font-weight: 600;
			font-size: 14px;
		}
		
		.tab-container {
			display: flex;
			margin-bottom: 20px;
			border-bottom: 1px solid #ddd;
		}
		
		.tab {
			padding: 10px 20px;
			cursor: pointer;
			border-bottom: 3px solid transparent;
			font-weight: 600;
			color: #666;
			transition: all 0.3s;
		}
		
		.tab.active {
			color: var(--primary-color);
			border-bottom-color: var(--primary-color);
		}
		
		.tab-content {
			display: none;
		}
		
		.tab-content.active {
			display: block;
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
							<h2>Category Management</h2>
						</div>

						<div class="tab-container">
							<div class="tab active" onclick="switchTab('create')">Create Category</div>
							<div class="tab" onclick="switchTab('manage')">Manage Categories</div>
						</div>

						<!-- Create Category Tab -->
						<div class="tab-content active" id="create-tab">
							<div class="module">
								<div class="module-head">
									<h3>Create New Category</h3>
								</div>
								<div class="module-body">
									<?php if(isset($_POST['submit'])) { ?>
										<div class="alert alert-success">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<strong>Success!</strong> <?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
										</div>
									<?php } ?>

									<form class="form-horizontal row-fluid" name="Category" method="post">
										<div class="control-group">
											<label class="control-label" for="basicinput">Category Name</label>
											<div class="controls">
												<input type="text" placeholder="Enter category name" name="category" class="span8 tip" required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="basicinput">Description</label>
											<div class="controls">
												<textarea class="span8" name="description" rows="5" placeholder="Enter category description"></textarea>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" name="submit" class="btn-primary">
													<i class="icon-plus"></i> Create Category
												</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>

						<!-- Manage Categories Tab -->
						<div class="tab-content" id="manage-tab">
							<div class="module">
								<div class="module-head">
									<h3>Manage Categories</h3>
									<span class="category-count">
										<?php 
											$count_query = mysqli_query($con, "SELECT COUNT(*) as total FROM category");
											$count_data = mysqli_fetch_assoc($count_query);
											echo $count_data['total'] . " Categories";
										?>
									</span>
								</div>
								<div class="module-body table">
									<?php if(isset($_GET['del'])) { ?>
										<div class="alert alert-error">
											<button type="button" class="close" data-dismiss="alert">×</button>
											<strong>Notice:</strong> <?php echo htmlentities($_SESSION['delmsg']);?><?php echo htmlentities($_SESSION['delmsg']="");?>
										</div>
									<?php } ?>

									<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
										<thead>
											<tr>
												<th>#</th>
												<th>Category</th>
												<th>Description</th>
												<th>Creation Date</th>
												<th>Last Updated</th>
												<th>Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											$query = mysqli_query($con, "select * from category");
											$cnt = 1;
											while($row = mysqli_fetch_array($query)) {
											?>									
											<tr>
												<td><?php echo htmlentities($cnt);?></td>
												<td><strong><?php echo htmlentities($row['categoryName']);?></strong></td>
												<td><?php echo htmlentities($row['categoryDescription']);?></td>
												<td><?php echo htmlentities($row['creationDate']);?></td>
												<td><?php echo htmlentities($row['updationDate']);?></td>
												<td>
													<div class="action-buttons">
														<a href="edit-category.php?id=<?php echo $row['id']?>" class="action-btn edit">
															<i class="icon-edit"></i> Edit
														</a>
														<a href="category.php?id=<?php echo $row['id']?>&del=delete" class="action-btn delete" 
														   onClick="return confirm('Are you sure you want to delete this category?')">
															<i class="icon-remove-sign"></i> Delete
														</a>
													</div>
												</td>
											</tr>
											<?php $cnt = $cnt + 1; } ?>
										</tbody>
									</table>
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
	<script>
		$(document).ready(function() {
			$('.datatable-1').dataTable({
				"pageLength": 10,
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"order": [[0, "asc"]],
				"language": {
					"search": "Filter categories:",
					"lengthMenu": "Show _MENU_ categories per page",
					"zeroRecords": "No categories found",
					"info": "Showing _START_ to _END_ of _TOTAL_ categories",
					"infoEmpty": "No categories available",
					"infoFiltered": "(filtered from _MAX_ total categories)",
					"paginate": {
						"previous": "<i class='icon-chevron-left'></i>",
						"next": "<i class='icon-chevron-right'></i>"
					}
				}
			});
		});
		
		function switchTab(tabName) {
			// Hide all tab contents
			document.querySelectorAll('.tab-content').forEach(tab => {
				tab.classList.remove('active');
			});
			
			// Remove active class from all tabs
			document.querySelectorAll('.tab').forEach(tab => {
				tab.classList.remove('active');
			});
			
			// Show selected tab content
			document.getElementById(tabName + '-tab').classList.add('active');
			
			// Activate selected tab
			event.currentTarget.classList.add('active');
		}
	</script>
</body>
<?php } ?>