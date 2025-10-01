<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
}
else{
	$pid=intval($_GET['id']);// product id
if(isset($_POST['submit']))
{
	$category=$_POST['category'];
	$subcat=$_POST['subcategory'];
	$productname=$_POST['productName'];
	$productcompany=$_POST['productCompany'];
	$productprice=$_POST['productprice'];
	$productpricebd=$_POST['productpricebd'];
	$productdescription=$_POST['productDescription'];
	$productscharge=$_POST['productShippingcharge'];
	$stockQuantity = intval($_POST['stockQuantity']); 
	
$sql=mysqli_query($con,"update products set category='$category',subCategory='$subcat',productName='$productname',productCompany='$productcompany',productPrice='$productprice',productDescription='$productdescription',shippingCharge='$productscharge',stockQuantity='$stockQuantity',productPriceBeforeDiscount='$productpricebd' where id='$pid' ");
if($sql) {
	$_SESSION['msg']="Product Updated Successfully !!";
} else {
	$_SESSION['msg']="Error updating product: ".mysqli_error($con);
}

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin| Edit Product</title>
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
			--success-color: #2ecc71;
			--warning-color: #f39c12;
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
			padding: 15px;
			border-radius: 8px;
			background: #f9f9f9;
			border-left: 4px solid var(--primary-color);
		}
		
		.control-label {
			font-weight: 600;
			color: var(--dark-color);
			margin-bottom: 8px;
			display: block;
		}
		
		.controls input, .controls select, .controls textarea {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			transition: all 0.3s;
			box-sizing: border-box;
			font-family: 'Open Sans', sans-serif;
		}
		
		.controls input:focus, .controls select:focus, .controls textarea:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
			outline: none;
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
		
		.quantity-display {
			font-weight: 600;
			padding: 4px 8px;
			border-radius: 4px;
			display: inline-block;
			min-width: 40px;
			text-align: center;
		}
		
		.quantity-low {
			background: #ffeaa7;
			color: #e17055;
		}
		
		.quantity-medium {
			background: #81ecec;
			color: #00cec9;
		}
		
		.quantity-high {
			background: #55efc4;
			color: #00b894;
		}
		
		.quantity-out {
			background: #fab1a0;
			color: #d63031;
		}
		
		.stock-badge {
			font-size: 11px;
			padding: 2px 6px;
			border-radius: 10px;
			margin-left: 5px;
		}
		
		.price-container {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 15px;
		}
		
		.price-box {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 15px;
			border-radius: 8px;
			text-align: center;
		}
		
		.price-box.original {
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
		}
		
		.price-box h4 {
			margin: 0 0 5px;
			font-size: 14px;
			font-weight: 600;
		}
		
		.price-box .price {
			font-size: 18px;
			font-weight: 700;
		}
		
		.discount-badge {
			background: var(--success-color);
			color: white;
			padding: 3px 8px;
			border-radius: 12px;
			font-size: 12px;
			margin-left: 8px;
		}
		
		.form-section {
			background: white;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 20px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.05);
		}
		
		.form-section h4 {
			color: var(--primary-color);
			margin-bottom: 15px;
			padding-bottom: 10px;
			border-bottom: 2px solid #f0f0f0;
		}
		
		.image-preview-container {
			display: flex;
			gap: 15px;
			flex-wrap: wrap;
			margin-top: 10px;
		}
		
		.image-preview-item {
			text-align: center;
		}
		
		.image-preview-item img {
			border: 2px solid #ddd;
			border-radius: 5px;
			padding: 5px;
			background: white;
		}
		
		.image-preview-item a {
			display: block;
			margin-top: 5px;
			color: var(--primary-color);
			text-decoration: none;
			font-weight: 600;
		}
		
		.image-preview-item a:hover {
			color: #2980b9;
			text-decoration: underline;
		}
		
		.required-field::after {
			content: " *";
			color: var(--accent-color);
		}
		
		@media (max-width: 768px) {
			.price-container {
				grid-template-columns: 1fr;
			}
			
			.image-preview-container {
				flex-direction: column;
			}
		}
	</style>
<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

   <script>
function getSubcat(val) {
	$.ajax({
	type: "POST",
	url: "get_subcat.php",
	data:'cat_id='+val,
	success: function(data){
		$("#subcategory").html(data);
	}
	});
}

// Function to update stock status display
function updateStockStatus(quantity) {
    const stockStatus = document.getElementById('stockStatus');
    if (quantity == 0) {
        stockStatus.innerHTML = '<span class="quantity-out">Out of Stock</span>';
    } else if (quantity <= 10) {
        stockStatus.innerHTML = '<span class="quantity-low">Low Stock</span>';
    } else if (quantity <= 50) {
        stockStatus.innerHTML = '<span class="quantity-medium">Medium Stock</span>';
    } else {
        stockStatus.innerHTML = '<span class="quantity-high">High Stock</span>';
    }
}

// Update stock status when quantity changes
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.querySelector('input[name="stockQuantity"]');
    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            updateStockStatus(this.value);
        });
        
        // Initialize stock status
        updateStockStatus(quantityInput.value);
    }
});
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
								<h3>Edit Product</h3>
							</div>
							<div class="module-body">
								<?php if(isset($_POST['submit'])) { ?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Success!</strong> <?php echo htmlentities($_SESSION['msg']);?><?php unset($_SESSION['msg']); ?>
									</div>
								<?php } ?>

								<br />

								<form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">
									<?php 
									$query=mysqli_query($con,"select products.*,category.categoryName as catname,category.id as cid,subcategory.subcategory as subcatname,subcategory.id as subcatid from products join category on category.id=products.category join subcategory on subcategory.id=products.subCategory where products.id='$pid'");
									$cnt=1;
									while($row=mysqli_fetch_array($query))
									{
										// Determine quantity display class
										$quantity = $row['stockQuantity'];
										if($quantity == 0) {
											$quantity_class = "quantity-out";
											$stock_status = "Out of Stock";
										} elseif($quantity <= 10) {
											$quantity_class = "quantity-low";
											$stock_status = "Low Stock";
										} elseif($quantity <= 50) {
											$quantity_class = "quantity-medium";
											$stock_status = "Medium Stock";
										} else {
											$quantity_class = "quantity-high";
											$stock_status = "High Stock";
										}
									?>
									
									<!-- Basic Information Section -->
									<div class="form-section">
										<h4>Basic Information</h4>
										
										<div class="control-group">
											<label class="control-label required-field" for="basicinput">Category</label>
											<div class="controls">
												<select name="category" class="span8 tip" onChange="getSubcat(this.value);" required>
													<option value="<?php echo htmlentities($row['cid']);?>"><?php echo htmlentities($row['catname']);?></option> 
													<?php 
													$cat_query=mysqli_query($con,"select * from category");
													while($rw=mysqli_fetch_array($cat_query))
													{
														if($row['catname']==$rw['categoryName'])
														{
															continue;
														}
														else{
														?>
														<option value="<?php echo $rw['id'];?>"><?php echo $rw['categoryName'];?></option>
													<?php }} ?>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label required-field" for="basicinput">Sub Category</label>
											<div class="controls">
												<select name="subcategory" id="subcategory" class="span8 tip" required>
													<option value="<?php echo htmlentities($row['subcatid']);?>"><?php echo htmlentities($row['subcatname']);?></option>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label required-field" for="basicinput">Product Name</label>
											<div class="controls">
												<input type="text" name="productName" placeholder="Enter Product Name" value="<?php echo htmlentities($row['productName']);?>" class="span8 tip" required>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label required-field" for="basicinput">Product Company</label>
											<div class="controls">
												<input type="text" name="productCompany" placeholder="Enter Product Company Name" value="<?php echo htmlentities($row['productCompany']);?>" class="span8 tip" required>
											</div>
										</div>
									</div>

									<!-- Pricing Section -->
									<div class="form-section">
										<h4>Pricing Information</h4>
										<div class="price-container">
											<div class="price-box original">
												<h4>Original Price</h4>
												<div class="price">
													<input type="number" name="productpricebd" placeholder="0.00" value="<?php echo htmlentities($row['productPriceBeforeDiscount']);?>" class="span8 tip" step="0.01" min="0" required style="background: transparent; border: none; color: white; text-align: center; font-size: 18px; font-weight: bold; width: 100%;">
												</div>
											</div>
											<div class="price-box">
												<h4>Selling Price <span class="discount-badge">Discount</span></h4>
												<div class="price">
													<input type="number" name="productprice" placeholder="0.00" value="<?php echo htmlentities($row['productPrice']);?>" class="span8 tip" step="0.01" min="0" required style="background: transparent; border: none; color: white; text-align: center; font-size: 18px; font-weight: bold; width: 100%;">
												</div>
											</div>
										</div>
									</div>

									<!-- Stock Quantity Section -->
									<div class="form-section">
										<h4>Inventory Management</h4>
										<div class="control-group">
											<label class="control-label required-field" for="basicinput">Stock Quantity</label>
											<div class="controls">
												<input type="number" name="stockQuantity" placeholder="Enter stock quantity" value="<?php echo htmlentities($quantity);?>" class="span8 tip" min="0" required>
												<div style="margin-top: 8px;">
													<strong>Current Status:</strong> <span id="stockStatus" class="<?php echo $quantity_class; ?>"><?php echo $stock_status; ?></span>
												</div>
											</div>
										</div>
									</div>

									<!-- Description Section -->
									<div class="form-section">
										<h4>Product Details</h4>
										<div class="control-group">
											<label class="control-label required-field" for="basicinput">Product Description</label>
											<div class="controls">
												<textarea name="productDescription" placeholder="Enter Product Description" rows="6" class="span8 tip" required><?php echo htmlentities($row['productDescription']);?></textarea>  
											</div>
										</div>

										<div class="control-group">
											<label class="control-label required-field" for="basicinput">Shipping Charge</label>
											<div class="controls">
												<input type="number" name="productShippingcharge" placeholder="Enter Product Shipping Charge" value="<?php echo htmlentities($row['shippingCharge']);?>" class="span8 tip" step="0.01" min="0" required>
											</div>
										</div>
									</div>

									<!-- Images Section -->
									<div class="form-section">
										<h4>Product Images</h4>
										<div class="image-preview-container">
											<div class="image-preview-item">
												<strong>Main Image</strong><br>
												<img src="productimages/<?php echo htmlentities($pid);?>/<?php echo htmlentities($row['productImage1']);?>" width="200" height="100"><br>
												<a href="update-image1.php?id=<?php echo $row['id'];?>">Change Image 1</a>
											</div>
											
											<div class="image-preview-item">
												<strong>Secondary Image</strong><br>
												<img src="productimages/<?php echo htmlentities($pid);?>/<?php echo htmlentities($row['productImage2']);?>" width="200" height="100"><br>
												<a href="update-image2.php?id=<?php echo $row['id'];?>">Change Image 2</a>
											</div>
											
											<div class="image-preview-item">
												<strong>Additional Image</strong><br>
												<img src="productimages/<?php echo htmlentities($pid);?>/<?php echo htmlentities($row['productImage3']);?>" width="200" height="100"><br>
												<a href="update-image3.php?id=<?php echo $row['id'];?>">Change Image 3</a>
											</div>
										</div>
									</div>
									<?php } ?>

									<div class="control-group" style="border: none; background: none; text-align: center;">
										<div class="controls">
											<button type="submit" name="submit" class="btn-primary">
												<i class="icon-save"></i> Update Product
											</button>
											<a href="manage-products.php" class="btn" style="margin-left: 10px;">
												<i class="icon-arrow-left"></i> Back to Products
											</a>
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