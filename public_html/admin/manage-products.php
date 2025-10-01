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

if(isset($_GET['del']))
{
    mysqli_query($con,"delete from products where id = '".$_GET['id']."'");
    $_SESSION['delmsg']="Product deleted !!";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lewvitec Admin | Manage Products</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
		
		.alert {
			border-radius: 5px;
			border: none;
			padding: 12px;
			margin-bottom: 15px;
			font-size: 14px;
		}
		
		.alert-error {
			background-color: #f8d7da;
			color: #721c24;
			border-left: 4px solid var(--accent-color);
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
		
		.product-table {
			width: 100%;
			border-collapse: collapse;
			min-width: 800px; /* Minimum width to ensure all columns are visible */
		}
		
		.product-table thead th {
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
		
		.product-table tbody td {
			padding: 12px 15px;
			border-bottom: 1px solid #eaeaea;
			vertical-align: middle;
			font-size: 13px;
		}
		
		.product-table tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		
		.product-table tbody tr:hover {
			background-color: #f1f7fd;
		}
		
		.action-buttons {
			display: flex;
			gap: 5px;
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
			font-size: 12px;
		}
		
		.action-btn.edit {
			background: var(--warning-color);
		}
		
		.action-btn.edit:hover {
			background: #e67e22;
			transform: translateY(-1px);
		}
		
		.action-btn.delete {
			background: var(--accent-color);
		}
		
		.action-btn.delete:hover {
			background: #c0392b;
			transform: translateY(-1px);
		}
		
		.action-btn i {
			margin-right: 4px;
			font-size: 12px;
		}
		
		.product-count {
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
		
		.add-product-btn {
			background: var(--success-color);
			color: white;
			border: none;
			padding: 8px 15px;
			border-radius: 5px;
			cursor: pointer;
			font-weight: 600;
			text-decoration: none;
			transition: all 0.3s;
			display: inline-flex;
			align-items: center;
			font-size: 13px;
		}
		
		.add-product-btn:hover {
			background: #27ae60;
			transform: translateY(-2px);
			color: white;
			text-decoration: none;
		}
		
		.add-product-btn i {
			margin-right: 5px;
		}
		
		.status-badge {
			padding: 4px 8px;
			border-radius: 12px;
			font-size: 11px;
			font-weight: 600;
			display: inline-block;
			text-align: center;
			min-width: 70px;
		}
		
		.status-instock {
			background: #e8f5e9;
			color: var(--success-color);
		}
		
		.status-outofstock {
			background: #ffebee;
			color: var(--accent-color);
		}
		
		.product-image {
			width: 40px;
			height: 40px;
			object-fit: cover;
			border-radius: 4px;
			border: 2px solid #f0f0f0;
		}
		
		.price-tag {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 4px 8px;
			border-radius: 4px;
			font-weight: 600;
			font-size: 12px;
			display: inline-block;
		}
		
		.category-badge {
			background: #e3f2fd;
			color: var(--primary-color);
			padding: 4px 8px;
			border-radius: 4px;
			font-size: 12px;
			font-weight: 600;
			display: inline-block;
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
			
			.add-product-btn {
				width: 100%;
				justify-content: center;
				margin-top: 5px;
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
			.product-table thead th {
				padding: 10px 12px;
				font-size: 12px;
			}
			
			.product-table tbody td {
				padding: 10px 12px;
				font-size: 12px;
			}
			
			.product-image {
				width: 35px;
				height: 35px;
			}
			
			.action-btn {
				padding: 5px 8px;
				font-size: 11px;
			}
		}
		
		@media (max-width: 480px) {
			.product-table thead th {
				padding: 8px 10px;
				font-size: 11px;
			}
			
			.product-table tbody td {
				padding: 8px 10px;
				font-size: 11px;
			}
			
			.action-buttons {
				flex-direction: column;
				gap: 3px;
			}
			
			.action-btn {
				width: 100%;
				justify-content: center;
				padding: 6px;
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
							<h2>Product Management</h2>
							<a href="insert-product.php" class="add-product-btn">
								<i class="icon-plus"></i> Add New Product
							</a>
						</div>

						<div class="module">
							<div class="module-head">
								<h3>All Products</h3>
								<span class="product-count">
									<?php 
										$count_query = mysqli_query($con, "SELECT COUNT(*) as total FROM products");
										$count_data = mysqli_fetch_assoc($count_query);
										echo $count_data['total'] . " Products";
									?>
								</span>
							</div>
							<div class="module-body">
								<?php if(isset($_GET['del'])) { ?>
									<div class="alert alert-error">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Notice:</strong> <?php echo htmlentities($_SESSION['delmsg']);?><?php echo htmlentities($_SESSION['delmsg']="");?>
									</div>
								<?php } ?>

								<div class="table-container">
									<table class="product-table">
										<thead>
											<tr>
												<th>#</th>
												<th>Product</th>
												<th>Category</th>
												<th>Subcategory</th>
												<th>Company</th>
												<th>Price</th>
												<th>Status</th>
												<th>Created</th>
												<th>Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											$query = mysqli_query($con, "select products.*, category.categoryName, subcategory.subcategory from products join category on category.id=products.category join subcategory on subcategory.id=products.subCategory");
											$cnt = 1;
											while($row = mysqli_fetch_array($query)) {
											?>									
											<tr>
												<td><?php echo htmlentities($cnt);?></td>
												<td>
													<div style="display: flex; align-items: center; gap: 8px;">
														<?php 
														$imagePath = "productimages/{$row['id']}/{$row['productImage1']}";
														if (file_exists($imagePath) && !empty($row['productImage1'])) {
															echo '<img src="' . $imagePath . '" class="product-image" alt="' . htmlentities($row['productName']) . '">';
														} else {
															echo '<div style="width:40px; height:40px; background:#f0f0f0; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999; font-size:12px;"><i class="icon-picture"></i></div>';
														}
														?>
														<div>
															<strong style="font-size: 13px;"><?php echo htmlentities($row['productName']);?></strong>
															<br>
															<small style="color: #666; font-size: 11px;">ID: <?php echo htmlentities($row['id']);?></small>
														</div>
													</div>
												</td>
												<td>
													<span class="category-badge"><?php echo htmlentities($row['categoryName']);?></span>
												</td>
												<td>
													<small><?php echo htmlentities($row['subcategory']);?></small>
												</td>
												<td><?php echo htmlentities($row['productCompany']);?></td>
												<td>
													<span class="price-tag">$<?php echo htmlentities($row['productPrice']);?></span>
													<?php if (!empty($row['productPriceBeforeDiscount']) && $row['productPriceBeforeDiscount'] > $row['productPrice']) { ?>
														<br>
														<small style="text-decoration: line-through; color: #999; font-size: 11px;">$<?php echo htmlentities($row['productPriceBeforeDiscount']);?></small>
													<?php } ?>
												</td>
												<td>
													<span class="status-badge <?php echo $row['productAvailability'] == 'In Stock' ? 'status-instock' : 'status-outofstock'; ?>">
														<?php echo htmlentities($row['productAvailability']);?>
													</span>
												</td>
												<td>
													<small><?php echo date('M d, Y', strtotime($row['postingDate']));?></small>
												</td>
												<td>
													<div class="action-buttons">
														<a href="edit-products.php?id=<?php echo $row['id']?>" class="action-btn edit" title="Edit Product">
															<i class="icon-edit"></i> Edit
														</a>
														<a href="manage-products.php?id=<?php echo $row['id']?>&del=delete" class="action-btn delete" 
														   onClick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')" title="Delete Product">
															<i class="icon-remove-sign"></i> Delete
														</a>
													</div>
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
	<script>
		// Add touch event handling for better mobile scrolling
		document.addEventListener('DOMContentLoaded', function() {
			const tableContainer = document.querySelector('.table-container');
			if (tableContainer) {
				let isScrolling = false;
				
				tableContainer.addEventListener('touchstart', function() {
					isScrolling = false;
				});
				
				tableContainer.addEventListener('touchmove', function() {
					isScrolling = true;
				});
				
				tableContainer.addEventListener('touchend', function(event) {
					if (!isScrolling) {
						// Handle tap events on table cells if needed
					}
					isScrolling = false;
				});
			}
		});
	</script>
</body>
<?php } ?>