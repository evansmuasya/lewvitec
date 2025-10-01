<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
}
else{
	
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
	$stockquantity=$_POST['stockQuantity']; // New stock quantity field
	$productimage1=$_FILES["productimage1"]["name"];
	$productimage2=$_FILES["productimage2"]["name"];
	$productimage3=$_FILES["productimage3"]["name"];
//for getting product id
$query=mysqli_query($con,"select max(id) as pid from products");
	$result=mysqli_fetch_array($query);
	 $productid=$result['pid']+1;
	$dir="productimages/$productid";
if(!is_dir($dir)){
		mkdir("productimages/".$productid);
	}

	move_uploaded_file($_FILES["productimage1"]["tmp_name"],"productimages/$productid/".$_FILES["productimage1"]["name"]);
	move_uploaded_file($_FILES["productimage2"]["tmp_name"],"productimages/$productid/".$_FILES["productimage2"]["name"]);
	move_uploaded_file($_FILES["productimage3"]["tmp_name"],"productimages/$productid/".$_FILES["productimage3"]["name"]);
$sql=mysqli_query($con,"insert into products(category,subCategory,productName,productCompany,productPrice,productDescription,shippingCharge,stockQuantity,productImage1,productImage2,productImage3,productPriceBeforeDiscount) values('$category','$subcat','$productname','$productcompany','$productprice','$productdescription','$productscharge','$stockquantity','$productimage1','$productimage2','$productimage3','$productpricebd')");
$_SESSION['msg']="Product Inserted Successfully !!";

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lewvitec Admin | Insert Product</title>
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
		
		.controls select {
			background: white;
			cursor: pointer;
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
			font-size: 12px;
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
			font-size: 12px;
		}
		
		.subcategory-count {
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
		
		.category-badge {
			background: #e8f4fd;
			color: var(--primary-color);
			padding: 3px 8px;
			border-radius: 4px;
			font-size: 12px;
			font-weight: 600;
			display: inline-block;
			margin-top: 4px;
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
function selectCountry(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
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
						<div class="page-header">
							<h2>Product Management</h2>
						</div>

						<div class="module">
							<div class="module-head">
								<h3>Insert Product</h3>
							</div>
							<div class="module-body">

									<?php if(isset($_POST['submit']))
{?>
									<div class="alert alert-success">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Success!</strong>	<?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
									</div>
<?php } ?>


									<?php if(isset($_GET['del']))
{?>
									<div class="alert alert-error">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Notice:</strong> 	<?php echo htmlentities($_SESSION['delmsg']);?><?php echo htmlentities($_SESSION['delmsg']="");?>
									</div>
<?php } ?>

									<br />

			<form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">

<div class="control-group">
<label class="control-label" for="basicinput">Category</label>
<div class="controls">
<select name="category" class="span8 tip" onChange="getSubcat(this.value);"  required>
<option value="">Select Category</option> 
<?php $query=mysqli_query($con,"select * from category");
while($row=mysqli_fetch_array($query))
{?>

<option value="<?php echo $row['id'];?>"><?php echo $row['categoryName'];?></option>
<?php } ?>
</select>
</div>
</div>

									
<div class="control-group">
<label class="control-label" for="basicinput">Sub Category</label>
<div class="controls">
<select   name="subcategory"  id="subcategory" class="span8 tip" required>
</select>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput">Product Name</label>
<div class="controls">
<input type="text"    name="productName"  placeholder="Enter Product Name" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Product Company</label>
<div class="controls">
<input type="text"    name="productCompany"  placeholder="Enter Product Company Name" class="span8 tip" required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="basicinput">Product Price Before Discount</label>
<div class="controls">
<input type="text"    name="productpricebd"  placeholder="Enter Product Price" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Product Price After Discount (Selling Price)</label>
<div class="controls">
<input type="text"    name="productprice"  placeholder="Enter Product Price" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Product Description</label>
<div class="controls">
<textarea  name="productDescription"  placeholder="Enter Product Description" rows="6" class="span8 tip">
</textarea>  
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Product Shipping Charge</label>
<div class="controls">
<input type="text"    name="productShippingcharge"  placeholder="Enter Product Shipping Charge" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Stock Quantity</label>
<div class="controls">
<input type="number" min="0" name="stockQuantity" placeholder="Enter stock quantity" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Product Image1</label>
<div class="controls">
<input type="file" name="productimage1" id="productimage1" value="" class="span8 tip" required>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput">Product Image2</label>
<div class="controls">
<input type="file" name="productimage2"  class="span8 tip" required>
</div>
</div>



<div class="control-group">
<label class="control-label" for="basicinput">Product Image3</label>
<div class="controls">
<input type="file" name="productimage3"  class="span8 tip">
</div>
</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" name="submit" class="btn-primary">
				<i class="icon-plus"></i> Insert Product
			</button>
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
	<script src="scripts/datatables/jquery.dataTables.js"></script>
	<script>
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
		} );
	</script>
</body>
<?php } ?>