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
	$stockquantity=$_POST['stockQuantity'];
	
	// File upload configuration
	$allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
	$max_file_size = 5 * 1024 * 1024; // 5MB
	
	// Function to validate and upload images
	function uploadProductImage($file, $productid, $imageNumber) {
		global $allowed_types, $max_file_size;
		
		if($file['error'] !== UPLOAD_ERR_OK) {
			return array('success' => false, 'message' => 'Upload error for image ' . $imageNumber);
		}
		
		// Check file size
		if($file['size'] > $max_file_size) {
			return array('success' => false, 'message' => 'Image ' . $imageNumber . ' is too large. Maximum size is 5MB.');
		}
		
		// Get file extension
		$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		
		// Check if file type is allowed
		if(!in_array($file_extension, $allowed_types)) {
			return array('success' => false, 'message' => 'Image ' . $imageNumber . ' must be JPG, JPEG, PNG, GIF, or WEBP.');
		}
		
		// Additional MIME type validation
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime_type = finfo_file($finfo, $file['tmp_name']);
		finfo_close($finfo);
		
		$allowed_mime_types = array(
			'image/jpeg',
			'image/jpg', 
			'image/png',
			'image/gif',
			'image/webp'
		);
		
		if(!in_array($mime_type, $allowed_mime_types)) {
			return array('success' => false, 'message' => 'Invalid file type for image ' . $imageNumber);
		}
		
		// Generate unique filename
		$new_filename = uniqid() . '.' . $file_extension;
		
		// Create directory if it doesn't exist
		$dir = "productimages/$productid";
		if(!is_dir($dir)){
			mkdir($dir, 0755, true);
		}
		
		// Move uploaded file
		if(move_uploaded_file($file['tmp_name'], $dir . '/' . $new_filename)) {
			return array('success' => true, 'filename' => $new_filename);
		} else {
			return array('success' => false, 'message' => 'Failed to upload image ' . $imageNumber);
		}
	}
	
	// Get product ID
	$query=mysqli_query($con,"select max(id) as pid from products");
	$result=mysqli_fetch_array($query);
	$productid=$result['pid']+1;
	
	$upload_errors = array();
	$uploaded_files = array();
	
	// Upload image 1 (required)
	$upload1 = uploadProductImage($_FILES["productimage1"], $productid, 1);
	if(!$upload1['success']) {
		$upload_errors[] = $upload1['message'];
	} else {
		$productimage1 = $upload1['filename'];
		$uploaded_files[] = $productimage1;
	}
	
	// Upload image 2 (required)
	$upload2 = uploadProductImage($_FILES["productimage2"], $productid, 2);
	if(!$upload2['success']) {
		$upload_errors[] = $upload2['message'];
	} else {
		$productimage2 = $upload2['filename'];
		$uploaded_files[] = $productimage2;
	}
	
	// Upload image 3 (optional)
	if(!empty($_FILES["productimage3"]["name"])) {
		$upload3 = uploadProductImage($_FILES["productimage3"], $productid, 3);
		if(!$upload3['success']) {
			$upload_errors[] = $upload3['message'];
		} else {
			$productimage3 = $upload3['filename'];
			$uploaded_files[] = $productimage3;
		}
	} else {
		$productimage3 = '';
	}
	
	// If there are upload errors, show them and stop
	if(!empty($upload_errors)) {
		$_SESSION['msg'] = "Upload errors: " . implode(', ', $upload_errors);
	} else {
		// All uploads successful, insert into database
		$sql=mysqli_query($con,"insert into products(category,subCategory,productName,productCompany,productPrice,productDescription,shippingCharge,stockQuantity,productImage1,productImage2,productImage3,productPriceBeforeDiscount) values('$category','$subcat','$productname','$productcompany','$productprice','$productdescription','$productscharge','$stockquantity','$productimage1','$productimage2','$productimage3','$productpricebd')");
		
		if($sql) {
			$_SESSION['msg']="Product Inserted Successfully !!";
		} else {
			$_SESSION['msg']="Error inserting product into database: " . mysqli_error($con);
			// Clean up uploaded files if database insert failed
			foreach($uploaded_files as $file) {
				@unlink("productimages/$productid/$file");
			}
		}
	}
}
?>
