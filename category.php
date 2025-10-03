<?php
session_start();
error_reporting(0);
include('includes/config.php');

// ================= SLUG-BASED CATEGORY LOADING =================
$category_slug = $_GET['category_slug'] ?? '';

// Validate slug - redirect if empty or invalid
if(empty($category_slug) || $category_slug == '') {
    header('Location: /categories.php');
    exit();
}

// Get category ID from slug
$cat_query = mysqli_query($con, "SELECT id, categoryName FROM category WHERE slug = '$category_slug'");
if(mysqli_num_rows($cat_query) == 0) {
    // Category not found - show 404
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit();
}

$cat_row = mysqli_fetch_array($cat_query);
$cid = $cat_row['id'];
$category_name = $cat_row['categoryName'];

// ================= ADD TO CART =================
if (isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']);
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
        $message = "Product quantity increased in cart";
    } else {
        $sql_p = "SELECT * FROM products WHERE id={$id}";
        $query_p = mysqli_query($con, $sql_p);
        if (mysqli_num_rows($query_p) != 0) {
            $row_p = mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']] = array("quantity" => 1, "price" => $row_p['productPrice']);
            $message = "Product has been added to the cart";
        } else {
            $error = "Product ID is invalid";
        }
    }
    // Store message in session to display after redirect
    if (isset($message)) $_SESSION['message'] = $message;
    if (isset($error)) $_SESSION['error'] = $error;
    
    // Redirect back to category page with slug
    echo "<script type='text/javascript'> document.location ='products/".$category_slug."/'; </script>";
    exit();
}

// ================= ADD TO WISHLIST =================
if (isset($_GET['pid']) && $_GET['action'] == "wishlist") {
    if (strlen($_SESSION['login']) == 0) {
        header('location:login.php');
        exit();
    } else {
        // Check if product is already in wishlist
        $check = mysqli_query($con, "SELECT * FROM wishlist WHERE userId='".$_SESSION['id']."' AND productId='".$_GET['pid']."'");
        if(mysqli_num_rows($check) == 0) {
            mysqli_query($con, "INSERT INTO wishlist(userId,productId) values('".$_SESSION['id']."','".$_GET['pid']."')");
            $_SESSION['message'] = "Product added to wishlist";
        } else {
            $_SESSION['message'] = "Product is already in your wishlist";
        }
        header('location:my-wishlist.php');
        exit();
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
    <title><?php echo $category_name ? htmlspecialchars($category_name) . ' - ' : ''; ?>Product Category</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

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
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }
        
        .category-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 0 20px;
            margin-bottom: 30px;
        }
        
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr) !important; /* Two columns on mobile */
                gap: 12px;
                padding: 0 10px;
            }
            
            .product-card {
                border-radius: 8px;
            }
            
            .product-image {
                height: 140px; /* Reduced height for mobile */
            }
            
            .product-info {
                padding: 10px;
            }
            
            .product-name {
                font-size: 0.85rem;
                height: 2.6em; /* Adjusted for smaller screen */
            }
            
            .current-price {
                font-size: 0.95rem;
            }
            
            .original-price {
                font-size: 0.8rem;
            }
            
            .product-actions {
                flex-direction: column;
                gap: 8px;
                margin-top: 10px;
            }
            
            .btn-add-cart, .btn-wishlist {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            
            .btn-add-cart {
                padding: 5px 8px;
                font-size: 0.8rem;
            }
            
            .btn-wishlist {
                font-size: 1rem;
            }
        }
        
        /* Tablet and larger screens */
        @media (min-width: 769px) and (max-width: 1024px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
        }
        
        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product-image {
            height: 180px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
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
        
        .product-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-name {
            font-size: 0.95rem;
            margin-bottom: 10px;
            line-height: 1.4;
            height: 2.8em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .product-name a {
            color: var(--dark-text);
            text-decoration: none;
        }
        
        .product-name a:hover {
            color: var(--primary-color);
        }
        
        .product-price {
            margin-top: auto;
        }
        
        .current-price {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary-color);
        }
        
        .original-price {
            text-decoration: line-through;
            color: #b7b7b7;
            font-size: 0.9rem;
            margin-left: 5px;
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .btn-add-cart {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-add-cart:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-wishlist {
            color: #ddd;
            background: transparent;
            border: none;
            font-size: 1.1rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-wishlist:hover {
            color: #e83e8c;
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .page-link {
            color: var(--primary-color);
        }
        
        .no-products {
            text-align: center;
            padding: 40px 0;
            color: var(--dark-text);
        }
        
        .no-products i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ddd;
        }
        
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
    </style>
</head>
<body class="cnt-home">

<header class="header-style-1">
    <?php include('includes/top-header.php');?>
    <?php include('includes/main-header.php');?>
    <?php include('includes/menu-bar.php');?>
</header>

<div class="body-content outer-top-xs">
    <div class='container'>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="/categories.php">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo $category_name ? htmlspecialchars($category_name) : 'Products'; ?>
                </li>
            </ol>
        </nav>
        
        <div class="category-header">
            <h2><?php echo $category_name ? htmlspecialchars($category_name) : 'All Products'; ?></h2>
           
        </div>
        
        <div class='row outer-bottom-sm'>
            <div class='col-md-12'>
                <div class="search-result-container" style="padding: 20px;">
                    <div class="category-product">
                        <?php
                        // ================= PAGINATION =================
                        $limit = 12;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        if ($page < 1) $page = 1;
                        $start = ($page - 1) * $limit;

                        $countQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM products WHERE category='$cid'");
                        $countRow = mysqli_fetch_assoc($countQuery);
                        $total = $countRow['total'];
                        $pages = ceil($total / $limit);

                        $ret = mysqli_query($con, "SELECT * FROM products WHERE category='$cid' ORDER BY id DESC LIMIT $start, $limit");
                        $num = mysqli_num_rows($ret);

                        if ($num > 0) {
                            echo '<div class="product-grid">';
                            while ($row = mysqli_fetch_array($ret)) {
                                // Get product slug for clean URLs
                                $product_slug = $row['p_slug'] ?? '';
                                $product_url = !empty($product_slug) ? 
                                    "products/{$category_slug}/{$product_slug}/" : 
                                    "product-details.php?pid=" . htmlentities($row['id']);
                                ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <a href="<?php echo $product_url; ?>">
                                            <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" 
                                                 alt="<?php echo htmlentities($row['productName']);?>">
                                        </a>
                                        <div class="product-badge <?php echo $row['productAvailability'] != 'In Stock' ? 'out-of-stock' : ''; ?>">
                                            <?php echo htmlentities($row['productAvailability']); ?>
                                        </div>
                                    </div>

                                    <div class="product-info">
                                        <h3 class="product-name">
                                            <a href="<?php echo $product_url; ?>">
                                                <?php echo htmlentities($row['productName']);?>
                                            </a>
                                        </h3>
                                        
                                        <div class="product-price">
                                            <span class="current-price">Kes. <?php echo number_format(htmlentities($row['productPrice']));?></span>
                                            <?php if ($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                                                <span class="original-price">Kes. <?php echo number_format(htmlentities($row['productPriceBeforeDiscount']));?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="product-actions">
                                            <?php if ($row['stockQuantity'] > 0) { ?>
                                                <a href="products/<?php echo $category_slug; ?>/?action=add&id=<?php echo $row['id']; ?>" class="btn-add-cart add-to-cart">
                                                    <i class="fas fa-shopping-cart"></i> Add to cart
                                                </a>
                                            <?php } ?>
                                            
                                            <a class="btn-wishlist add-to-wishlist" href="products/<?php echo $category_slug; ?>/?pid=<?php echo htmlentities($row['id'])?>&action=wishlist" title="Add to Wishlist">
                                                <i class="fa fa-heart"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            echo '</div>';
                        } else { ?>
                            <div class="no-products">
                                <i class="fas fa-box-open"></i>
                                <h3>No Products Found</h3>
                                <p>We couldn't find any products in this category.</p>
                                <a href="/index.php" class="btn btn-primary mt-3">Browse Other Categories</a>
                            </div>
                        <?php } ?>
                    </div><!-- /.category-product -->

                    <!-- Pagination -->
                    <?php if ($pages > 1): ?>
                    <nav aria-label="Product pagination" class="pagination-container">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="products/<?php echo $category_slug; ?>/?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php 
                            // Show limited pagination links
                            $startPage = max(1, $page - 2);
                            $endPage = min($pages, $startPage + 4);
                            
                            if ($endPage - $startPage < 4) {
                                $startPage = max(1, $endPage - 4);
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="products/<?php echo $category_slug; ?>/?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="products/<?php echo $category_slug; ?>/?page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                </div><!-- /.search-result-container -->
            </div>
        </div>
        <?php include('includes/brands-slider.php');?>
    </div>
</div>

<?php include('includes/footer.php');?>

<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner"></div>
</div>

<!-- JS -->
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Echo for image lazy loading
    Echo.init();
    
    // Show loading overlay when adding to cart or wishlist
    $('.add-to-cart, .add-to-wishlist').on('click', function(e) {
        // For wishlist links, check if user is logged in
        if ($(this).hasClass('add-to-wishlist') && !<?php echo isset($_SESSION['login']) && strlen($_SESSION['login']) > 0 ? 'true' : 'false'; ?>) {
            e.preventDefault();
            toastr.warning('Please login to add items to your wishlist');
            return;
        }
        
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