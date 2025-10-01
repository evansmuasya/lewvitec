<?php 
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_GET['action']) && $_GET['action']=="add"){
    $id=intval($_GET['id']);
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]['quantity']++;
        $message = "Product quantity increased in cart";
    }else{
        $sql_p="SELECT * FROM products WHERE id={$id}";
        $query_p=mysqli_query($con,$sql_p);
        if(mysqli_num_rows($query_p)!=0){
            $row_p=mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']]=array("quantity" => 1, "price" => $row_p['productPrice']);
            $message = "Product has been added to the cart";
        }else{
            $error="Product ID is invalid";
        }
    }
    // Store message in session to display after redirect
    if (isset($message)) $_SESSION['message'] = $message;
    if (isset($error)) $_SESSION['error'] = $error;
    echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
    exit();
}
$pid=intval($_GET['pid']);
if(isset($_GET['pid']) && $_GET['action']=="wishlist" ){
    if(strlen($_SESSION['login'])==0)
    {   
        header('location:login.php');
    }
    else
    {
        // Check if product is already in wishlist
        $check = mysqli_query($con, "SELECT * FROM wishlist WHERE userId='".$_SESSION['id']."' AND productId='$pid'");
        if(mysqli_num_rows($check) == 0) {
            mysqli_query($con,"insert into wishlist(userId,productId) values('".$_SESSION['id']."','$pid')");
            $_SESSION['message'] = "Product added to wishlist";
        } else {
            $_SESSION['message'] = "Product is already in your wishlist";
        }
        header('location:my-wishlist.php');
        exit();
    }
}
if(isset($_POST['submit']))
{
    $qty=$_POST['quality'];
    $price=$_POST['price'];
    $value=$_POST['value'];
    $name=$_POST['name'];
    $summary=$_POST['summary'];
    $review=$_POST['review'];
    mysqli_query($con,"insert into productreviews(productId,quality,price,value,name,summary,review) values('$pid','$qty','$price','$value','$name','$summary','$review')");
    $_SESSION['message'] = "Review submitted successfully";
    echo "<script>window.location.href='product-details.php?pid=$pid';</script>";
    exit();
}

// Store information for order buttons
$store_phone = "+254702379337"; // Replace with actual store phone number
$store_email = "muasyaevans55@gmail.com"; // Replace with actual store email
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keywords" content="MediaCenter, Template, eCommerce">
        <meta name="robots" content="all">
        <title>Product Details</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

        <style>	    
             .container {
            width: 100%;
            padding-right: 0;
            padding-left: 0;
            margin-right: auto;
            margin-left: auto;
        }
            :root {
                --primary-color: #4e73df;
                --secondary-color: #6f42c1;
                --success-color: #1cc88a;
                --danger-color: #e74a3b;
                --warning-color: #f6c23e;
                --light-bg: #f8f9fc;
                --dark-text: #5a5c69;
                --border-color: #e3e6f0;
                --whatsapp-color: #25D366;
                --email-color: #D44638;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f8f9fc;
                color: var(--dark-text);
            }

            .breadcrumb {
                background-color: white;
                padding: 15px 0;
                margin-bottom: 20px;
                border-radius: 0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .breadcrumb-inner ul {
                margin: 0;
                padding: 0;
            }

            .breadcrumb-inner li {
                display: inline-block;
                font-size: 14px;
            }

            .breadcrumb-inner li:not(:last-child):after {
                content: ">";
                margin: 0 10px;
                color: #6c757d;
            }

            .breadcrumb-inner li a {
                color: var(--primary-color);
                text-decoration: none;
            }

            .breadcrumb-inner li.active {
                color: #6c757d;
            }

            /* Single Product */
            .single-product {
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 15px rgba(0,0,0,0.05);
                padding: 25px;
                margin-bottom: 30px;
            }

            .product-info-block .product-info .name {
                font-size: 24px;
                font-weight: 600;
                margin-bottom: 10px;
                color: var(--dark-text);
            }

            .stock-container, .price-container {
                background-color: var(--light-bg);
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .stock-box, .price-box {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .label {
                font-weight: 500;
                color: #6c757d;
            }

            .value {
                font-weight: 500;
            }

            .price {
                font-size: 24px;
                font-weight: 700;
                color: var(--primary-color);
            }

            .price-strike {
                font-size: 18px;
                color: #b7b7b7;
                text-decoration: line-through;
                margin-left: 10px;
            }

            .favorite-button a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: white;
                border: 1px solid var(--border-color);
                color: #ddd;
                transition: all 0.3s;
                text-decoration: none;
            }

            .favorite-button a:hover {
                background-color: #e83e8c;
                color: white;
                border-color: #e83e8c;
            }

            .quantity-container {
                background-color: var(--light-bg);
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }

            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
                padding: 12px 25px;
                font-weight: 500;
                border-radius: 5px;
                transition: all 0.3s;
            }

            .btn-primary:hover {
                background-color: #2c52a7;
                border-color: #2c52a7;
                transform: translateY(-2px);
            }

            /* Order buttons styles */
            .order-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 20px;
            }

            .btn-whatsapp {
                background-color: var(--whatsapp-color);
                border-color: var(--whatsapp-color);
                color: white;
                padding: 12px 20px;
                font-weight: 500;
                border-radius: 5px;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
            }

            .btn-whatsapp:hover {
                background-color: #128C7E;
                border-color: #128C7E;
                color: white;
                transform: translateY(-2px);
            }

            .btn-call {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
                color: white;
                padding: 12px 20px;
                font-weight: 500;
                border-radius: 5px;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
            }

            .btn-call:hover {
                background-color: #2c52a7;
                border-color: #2c52a7;
                color: white;
                transform: translateY(-2px);
            }

            .btn-email {
                background-color: var(--email-color);
                border-color: var(--email-color);
                color: white;
                padding: 12px 20px;
                font-weight: 500;
                border-radius: 5px;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
            }

            .btn-email:hover {
                background-color: #BB001B;
                border-color: #BB001B;
                color: white;
                transform: translateY(-2px);
            }

            /* Related Products */
            .featured-product .product-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }

            .featured-product .product-card {
                background: white;
                border-radius: 8px;
                overflow: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                height: 100%;
                display: flex;
                flex-direction: column;
                border: 1px solid var(--border-color);
            }

            .featured-product .product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            }

            .featured-product .product-image {
                height: 25px;
                overflow: hidden;
                position: relative;
                text-align: center;
                padding: 15px;
            }

            .featured-product .product-image img {
                max-height: 100%;
                width: auto;
                object-fit: contain;
            }

            .featured-product .product-info {
                padding: 15px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .featured-product .product-name {
                font-size: 16px;
                font-weight: 500;
                margin-bottom: 10px;
                height: 40px;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .featured-product .product-name a {
                color: var(--dark-text);
                text-decoration: none;
            }

            .featured-product .product-name a:hover {
                color: var(--primary-color);
            }

            .featured-product .product-price {
                margin-top: auto;
            }

            .featured-product .current-price {
                font-weight: 600;
                color: var(--primary-color);
                font-size: 16px;
            }

            .featured-product .original-price {
                font-size: 14px;
                color: #b7b7b7;
                text-decoration: line-through;
                margin-left: 5px;
            }

            .featured-product .product-actions {
                display: flex;
                justify-content: space-between;
                margin-top: 15px;
            }

            .featured-product .btn-add-cart {
                background: var(--primary-color);
                color: white;
                border: none;
                padding: 8px 15px;
                border-radius: 4px;
                font-size: 0.85rem;
                transition: all 0.3s;
                text-decoration: nil;
                display: inline-block;
            }

            .featured-product .btn-add-cart:hover {
                background: var(--secondary-color);
                transform: translateY(-2px);
                color: white;
            }

            .featured-product .btn-add-cart:disabled {
                background: #ccc;
                cursor: not-allowed;
            }

            .featured-product .btn-add-cart:disabled:hover {
                transform: none;
            }

            /* Loading overlay */
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255,255,255,0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                display: none;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid var(--primary-color);
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Badges */
            .product-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                background: var(--success-color);
                color: white;
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .out-of-stock {
                background: var(--danger-color);
            }

            /* Product Image Slider */
            .product-slider {
                margin-bottom: 30px;
            }

            .main-slider img {
                width: 100%;
                height: auto;
                border-radius: 8px;
            }

            .thumb-slider {
                margin-top: 10px;
            }

            .thumb-slider .slick-slide {
                margin: 0 5px;
                cursor: pointer;
                opacity: 0.6;
                transition: opacity 0.3s;
                border: 2px solid transparent;
            }

            .thumb-slider .slick-slide.slick-current, 
            .thumb-slider .slick-slide:hover {
                opacity: 1;
                border-color: var(--primary-color);
            }

            .thumb-slider img {
                width: 100%;
                height: 80px;
                object-fit: cover;
                border-radius: 4px;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .featured-product .product-grid {
                    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                    gap: 15px;
                }
                
                .order-buttons {
                    flex-direction: column;
                }
                
                .order-buttons a {
                    width: 100%;
                    justify-content: center;
                }
                
                .product-info-block {
                    margin-top: 20px;
                }
                
                .thumb-slider img {
                    height: 60px;
                }
            }
        </style>

        <link rel="shortcut icon" href="assets/images/favicon.ico">
    </head>
    <body class="cnt-home">
    
<header class="header-style-1">
    <?php include('includes/top-header.php');?>
    <?php include('includes/main-header.php');?>
    <?php include('includes/menu-bar.php');?>
</header>

<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
<?php
$ret=mysqli_query($con,"select category.categoryName as catname,subcategory.subcategory as subcatname,products.productName as pname from products join category on category.id=products.category join subcategory on subcategory.id=products.subcategory where products.id='$pid'");
while ($rw=mysqli_fetch_array($ret)) {
?>
            <ul class="list-inline list-unstyled">
                <li><a href="index.php">Home</a></li>
                <li><?php echo htmlentities($rw['catname']);?></a></li>
                <li><?php echo htmlentities($rw['subcatname']);?></li>
                <li class='active'><?php echo htmlentities($rw['pname']);?></li>
            </ul>
            <?php }?>
        </div>
    </div>
</div>

<div class="body-content outer-top-xs">
    <div class='container'>
        <div class='row single-product outer-bottom-sm '>
            <?php 
            $ret=mysqli_query($con,"select * from products where id='$pid'");
            while($row=mysqli_fetch_array($ret)) {
            ?>
            <div class='col-md-12'>
                <div class="row wow fadeInUp">
                    <!-- Product Image Slider -->
                    <div class="col-md-6">
                        <div class="product-slider">
                            <div class="main-slider">
                                <div>
                                    <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" alt="<?php echo htmlentities($row['productName']);?>">
                                </div>
                                <?php if(!empty($row['productImage2'])): ?>
                                <div>
                                    <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>" alt="<?php echo htmlentities($row['productName']);?>">
                                </div>
                                <?php endif; ?>
                                <?php if(!empty($row['productImage3'])): ?>
                                <div>
                                    <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>" alt="<?php echo htmlentities($row['productName']);?>">
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="thumb-slider">
                                <div>
                                    <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" alt="Thumbnail">
                                </div>
                                <?php if(!empty($row['productImage2'])): ?>
                                <div>
                                    <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>" alt="Thumbnail">
                                </div>
                                <?php endif; ?>
                                <?php if(!empty($row['productImage3'])): ?>
                                <div>
                                    <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>" alt="Thumbnail">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class='col-md-6 product-info-block'>
                        <div class="product-info">
                            <h1 class="name"><?php echo htmlentities($row['productName']);?></h1>
                            <?php $rt=mysqli_query($con,"select * from productreviews where productId='$pid'");
                            $num=mysqli_num_rows($rt);
                            { ?>
                            <div class="rating-reviews m-t-20">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="rating rateit-small"></div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="reviews">
                                            <a href="#review" class="lnk">(<?php echo htmlentities($num);?> Reviews)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            

                            <div class="stock-container info-container m-t-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="stock-box">
                                            <span class="label">Product Brand :</span>
                                        </div>    
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="stock-box">
                                            <span class="value"><?php echo htmlentities($row['productCompany']);?></span>
                                        </div>    
                                    </div>
                                </div>
                            </div>

                            <div class="stock-container info-container m-t-10">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="stock-box">
                                            <span class="label">Shipping Charge :</span>
                                        </div>    
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="stock-box">
                                            <span class="value"><?php if($row['shippingCharge']==0) {
                                                echo "Free";
                                            } else {
                                                echo "Kes. " . htmlentities($row['shippingCharge']);
                                            } ?></span>
                                        </div>    
                                    </div>
                                </div>
                            </div>

                            <div class="price-container info-container m-t-20">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="price-box">
                                            <span class="price">Kes. <?php echo number_format(htmlentities($row['productPrice']));?></span>
                                            <?php if($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                                            <span class="price-strike">Kes. <?php echo number_format(htmlentities($row['productPriceBeforeDiscount']));?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="favorite-button m-t-10">
                                            <a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="Wishlist" href="product-details.php?pid=<?php echo htmlentities($row['id'])?>&&action=wishlist">
                                                <i class="fa fa-heart"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="quantity-container info-container">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <span class="label">Qty :</span>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="cart-quantity">
                                            <div class="quant-input">
                                                <div class="arrows">
                                                    <div class="arrow plus gradient"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
                                                    <div class="arrow minus gradient"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
                                                </div>
                                                <input type="text" value="1" id="quantity-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <?php if($row['stockQuantity'] >'0'){ ?>
                                        <a href="product-details.php?action=add&id=<?php echo $row['id']; ?>" class="btn btn-primary add-to-cart-btn">
                                            <i class="fa fa-shopping-cart inner-right-vs"></i> ADD TO CART
                                        </a>
                                        <?php } else { ?>
                                        <div class="action" style="color:red">Out of Stock</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Buttons -->
                            <div class="order-buttons">
                                <?php 
                                $product_name = urlencode($row['productName']);
                                $product_price = $row['productPrice'];
                                $product_url = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                                $whatsapp_message = urlencode("Hello! I would like to order:\nProduct: {$row['productName']}\nPrice: Kes. {$row['productPrice']}\n\nPlease contact me to complete the order.");
                                $email_subject = urlencode("Order Inquiry: {$row['productName']}");
                                $email_body = rawurlencode("Hello,

I am interested in ordering the following product:

Product Name: {$row['productName']}
Product Price: Kes. {$row['productPrice']}
Product URL: {$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}

Please contact me with information on how to complete my order.

Thank you.");
?>
                                
                                <a href="https://wa.me/<?php echo $store_phone; ?>?text=<?php echo $whatsapp_message; ?>" 
                                   class="btn btn-whatsapp" target="_blank">
                                    <i class="fa fa-whatsapp"></i> Order via WhatsApp
                                </a>
                                
                                <a href="tel:<?php echo $store_phone; ?>" class="btn btn-call">
                                    <i class="fa fa-phone"></i> Call to Order
                                </a>
                                
                                <a href="mailto:<?php echo $store_email; ?>?subject=<?php echo $email_subject; ?>&body=<?php echo $email_body; ?>" 
                                   class="btn btn-email">
                                    <i class="fa fa-envelope"></i> Email Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Tabs -->
                <div class="product-tabs outer-top-smal wow fadeInUp">
                    <div class="row">
                        <div class="col-sm-3">
                            <ul id="product-tabs" class="nav nav-tabs nav-tab-cell">
                                <li class="active"><a data-toggle="tab" href="#description">Description</a></li>
                                <li><a data-toggle="tab" href="#review">Review</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-9">
                            <div class="tab-content">
                                <div id="description" class="tab-pane in active">
                                    <div class="product-tab">
                                        <p class="text"><?php echo $row['productDescription'];?></p>
                                    </div>    
                                </div>

                                <div id="review" class="tab-pane">
                                    <div class="product-tab">
                                        <div class="product-reviews">
                                            <h4 class="title">Customer Reviews</h4>
                                            <?php 
                                            $qry=mysqli_query($con,"select * from productreviews where productId='$pid'");
                                            while ($rvw=mysqli_fetch_array($qry)) {
                                            ?>                                            
                                            <div class="reviews">
                                                <div class="review">
                                                    <div class="review-title"><span class="summary"><?php echo htmlentities($rvw['summary']);?></span><span class="date"><i class="fa fa-calendar"></i><span><?php echo htmlentities($rvw['reviewDate']);?></span></span></div>
                                                    <div class="text">"<?php echo htmlentities($rvw['review']);?>"</div>
                                                    <div class="author m-t-15"><i class="fa fa-pencil-square-o"></i> <span class="name"><?php echo htmlentities($rvw['name']);?></span></div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <div class="product-add-review">
                                            <h4 class="title">Write your own review</h4>
                                            <div class="review-table">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered"> 
                                                        <thead>
                                                            <tr>
                                                                <th class="cell-label">&nbsp;</th>
                                                                <th>1 star</th>
                                                                <th>2 stars</th>
                                                                <th>3 stars</th>
                                                                <th>4 stars</th>
                                                                <th>5 stars</th>
                                                            </tr>
                                                        </thead>  
                                                        <tbody>
                                                            <tr>
                                                                <td class="cell-label">Quality</td>
                                                                <td><input type="radio" name="quality" class="radio" value="1"></td>
                                                                <td><input type="radio" name="quality" class="radio" value="2"></td>
                                                                <td><input type="radio" name="quality" class="radio" value="3"></td>
                                                                <td><input type="radio" name="quality" class="radio" value="4"></td>
                                                                <td><input type="radio" name="quality" class="radio" value="5"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="cell-label">Price</td>
                                                                <td><input type="radio" name="price" class="radio" value="1"></td>
                                                                <td><input type="radio" name="price" class="radio" value="2"></td>
                                                                <td><input type="radio" name="price" class="radio" value="3"></td>
                                                                <td><input type="radio" name="price" class="radio" value="4"></td>
                                                                <td><input type="radio" name="price" class="radio" value="5"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="cell-label">Value</td>
                                                                <td><input type="radio" name="value" class="radio" value="1"></td>
                                                                <td><input type="radio" name="value" class="radio" value="2"></td>
                                                                <td><input type="radio" name="value" class="radio" value="3"></td>
                                                                <td><input type="radio" name="value" class="radio" value="4"></td>
                                                                <td><input type="radio" name="value" class="radio" value="5"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <div class="review-form">
                                                <div class="form-container">
                                                    <form role="form" class="cnt-form" method="post">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputName">Your Name <span class="astk">*</span></label>
                                                                    <input type="text" class="form-control txt" id="exampleInputName" name="name" required="required">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputSummary">Summary <span class="astk">*</span></label>
                                                                    <input type="text" class="form-control txt" id="exampleInputSummary" name="summary" required="required">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputReview">Review <span class="astk">*</span></label>
                                                                    <textarea class="form-control txt txt-review" id="exampleInputReview" rows="4" name="review" required="required"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="action text-right">
                                                            <button type="submit" name="submit" class="btn btn-primary btn-upper">SUBMIT REVIEW</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $cid=$row['category'];
                $subcid=$row['subCategory']; } ?>

                <!-- RELATED PRODUCTS -->
                <section class="section featured-product wow fadeInUp">
                    <h3 class="section-title">Related Products</h3>
                    <div class="product-grid">
                        <?php 
                        $qry=mysqli_query($con,"select * from products where subCategory='$subcid' and category='$cid' and id != '$pid' limit 4");
                        while($rw=mysqli_fetch_array($qry)) {
                        ?>    
                        <div class="product-card">
                            <div class="product-image">
                                <a href="product-details.php?pid=<?php echo htmlentities($rw['id']);?>">
                                    <img src="admin/productimages/<?php echo htmlentities($rw['id']);?>/<?php echo htmlentities($rw['productImage1']);?>" alt="<?php echo htmlentities($rw['productName']);?>">
                                </a>
                                <div class="product-actions">
    <?php if ($rw['stockQuantity'] > 0) { ?>
        <form method="post" action="product-details.php?action=add&id=<?php echo $rw['id']; ?>">
            <div class="quantity-wrapper">
                <label for="quantity">Qty:</label>
                <input type="number" 
                       id="quantity" 
                       name="quantity" 
                       value="1" 
                       min="1" 
                       max="<?php echo $rw['stockQuantity']; ?>" 
                       required>
            </div>
            <button type="submit" class="btn-add-cart add-to-cart">
                <i class="fas fa-shopping-cart"></i> Add to Cart (<?php echo $rw['stockQuantity']; ?> left)
            </button>
        </form>
    <?php } else { ?>
        <button class="btn-add-cart" disabled>
            <i class="fas fa-times-circle"></i> Out of stock
        </button>
    <?php } ?>
</div>

                            </div>
                            <div class="product-info">
                                <h3 class="product-name">
                                    <a href="product-details.php?pid=<?php echo htmlentities($rw['id']);?>"><?php echo htmlentities($rw['productName']);?></a>
                                </h3>
                                <div class="product-price">
                                    <span class="current-price">Kes. <?php echo number_format(htmlentities($rw['productPrice']));?></span>
                                    <?php if($rw['productPriceBeforeDiscount'] > $rw['productPrice']): ?>
                                    <span class="original-price">Kes. <?php echo number_format(htmlentities($rw['productPriceBeforeDiscount']));?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions">
    <?php if ($rw['stockQuantity'] > 0) { ?>
        <a href="product-details.php?action=add&id=<?php echo $rw['id']; ?>" class="btn-add-cart add-to-cart">
            <i class="fas fa-shopping-cart"></i> Add to Cart (<?php echo $rw['stockQuantity']; ?> left)
        </a>
    <?php } else { ?>
        <button class="btn-add-cart" disabled>
            <i class="fas fa-times-circle"></i> Out of stock
        </button>
    <?php } ?>
</div>

                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>

<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner"></div>
</div>

<!-- JavaScripts -->
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize product image slider
    $('.main-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        asNavFor: '.thumb-slider'
    });
    
    $('.thumb-slider').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '.main-slider',
        dots: false,
        arrows: false,
        centerMode: false,
        focusOnSelect: true,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 2
                }
            }
        ]
    });
    
    // Quantity input functionality
    $('.arrow.plus').click(function() {
        var input = $('#quantity-input');
        var value = parseInt(input.val());
        input.val(value + 1);
    });
    
    $('.arrow.minus').click(function() {
        var input = $('#quantity-input');
        var value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1);
        }
    });
    
    // Show loading overlay when adding to cart or wishlist
    $('.add-to-cart, .add-to-cart-btn').on('click', function(e) {
        $('.loading-overlay').fadeIn();
    });
    
    // Display any messages from PHP
    <?php if (isset($_SESSION['message'])): ?>
        toastr.success('<?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?>');
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        toastr.error('<?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>');
    <?php endif; ?>
});
</script>
</body>
</html>