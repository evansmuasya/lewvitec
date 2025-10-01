<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Get search query from GET or POST
$find = "";
if(isset($_POST['product'])) {
    $find = "%" . trim($_POST['product']) . "%";
} elseif(isset($_GET['q'])) {
    $find = "%" . trim($_GET['q']) . "%";
}

// Add to cart functionality
if(isset($_GET['action']) && $_GET['action']=="add"){
    $id=intval($_GET['id']);
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]['quantity']++;
    }else{
        $sql_p="SELECT * FROM products WHERE id={$id}";
        $query_p=mysqli_query($con,$sql_p);
        if(mysqli_num_rows($query_p)!=0){
            $row_p=mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']]=array("quantity" => 1, "price" => $row_p['productPrice']);
            echo "<script>alert('Product has been added to the cart')</script>";
            echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
        }else{
            $message="Product ID is invalid";
        }
    }
}

// Wishlist functionality
if(isset($_GET['pid']) && $_GET['action']=="wishlist" ){
    if(strlen($_SESSION['login'])==0) {   
        header('location:login.php');
    } else {
        mysqli_query($con,"insert into wishlist(userId,productId) values('".$_SESSION['id']."','".$_GET['pid']."')");
        echo "<script>alert('Product added to wishlist');</script>";
        header('location:my-wishlist.php');
    }
}
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
    <title>Search Results</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <style>
		.container {
  width: 100%;
  padding-right: 0;
  padding-left: 0;
  margin-right: auto;
  margin-left: auto;
}

		
    /* Mobile-First Search Results Styles */
    .search-results-header {
        background: #f8f9fa;
        padding: 15px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .search-query-display {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    
    .results-count {
        color: #666;
        font-size: 14px;
    }
    
    .search-result-container {
        margin-top: 20px;
    }
    
    .category-product {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        padding: 0 10px;
    }
    
    .product-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .product-image {
        position: relative;
        overflow: hidden;
        background: #f8f9fa;
    }
    
    .product-image .image {
        padding: 15px;
        text-align: center;
    }
    
    .product-image img {
        width: 100%;
        height: 200px;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-image img {
        transform: scale(1.05);
    }
    
    .product-info {
        padding: 15px;
    }
    
    .product-name {
        margin: 0 0 10px 0;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.4;
    }
    
    .product-name a {
        color: #333;
        text-decoration: none;
    }
    
    .product-name a:hover {
        color: #667eea;
    }
    
    .product-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
    }
    
    .current-price {
        font-size: 18px;
        font-weight: 700;
        color: #2c5aa0;
    }
    
    .original-price {
        font-size: 14px;
        color: #999;
        text-decoration: line-through;
    }
    
    .product-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }
    
    .btn-add-cart {
        flex: 1;
        background: #667eea;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-add-cart:hover {
        background: #5a67d8;
        transform: translateY(-1px);
    }
    
    .btn-wishlist {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #666;
        width: 40px;
        height: 40px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-wishlist:hover {
        background: #fff5f5;
        color: #e53e3e;
        border-color: #fed7d7;
    }
    
    .out-of-stock {
        background: #fed7d7;
        color: #c53030;
        padding: 10px;
        text-align: center;
        border-radius: 6px;
        font-weight: 600;
        margin-top: 15px;
    }
    
    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
    }
    
    .no-results-icon {
        font-size: 64px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }
    
    .no-results h3 {
        color: #4a5568;
        margin-bottom: 10px;
    }
    
    .no-results p {
        color: #718096;
        margin-bottom: 20px;
    }
    
    .btn-search-again {
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .btn-search-again:hover {
        background: #5a67d8;
        transform: translateY(-1px);
    }
    
    /* Mobile Sidebar Toggle */
    .mobile-filter-toggle {
        display: none;
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        width: 100%;
        font-weight: 600;
        cursor: pointer;
    }
    
    .mobile-filter-toggle i {
        margin-right: 8px;
    }
    
    /* Responsive Design */
    @media (max-width: 991px) {
        .sidebar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 300px;
            height: 100vh;
            background: white;
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.active {
            display: block;
        }
        
        .mobile-filter-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .category-product {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            padding: 0 5px;
        }
        
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        .overlay.active {
            display: block;
        }
        
        .close-sidebar {
            display: block;
            background: #ef4444;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            width: 100%;
            cursor: pointer;
        }
    }
    
    @media (max-width: 768px) {
        .category-product {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }
        
        .product-image img {
            height: 180px;
        }
        
        .product-info {
            padding: 12px;
        }
        
        .product-name {
            font-size: 15px;
        }
        
        .current-price {
            font-size: 16px;
        }
        
        .search-query-display {
            font-size: 16px;
        }
    }
    
    @media (max-width: 576px) {
        .category-product {
            grid-template-columns: 1fr;
            gap: 15px;
            padding: 0;
        }
        
        .search-results-header {
            padding: 12px 0;
            margin-bottom: 15px;
        }
        
        .product-actions {
            flex-direction: column;
        }
        
        .btn-wishlist {
            width: 100%;
            height: 40px;
        }
        
        .sidebar {
            width: 280px;
        }
    }
    
    /* Loading animation for images */
    .product-image {
        background: linear-gradient(110deg, #f5f5f5 8%, #eee 18%, #f5f5f5 33%);
        background-size: 200% 100%;
        animation: 1.5s shine linear infinite;
    }
    
    @keyframes shine {
        to {
            background-position-x: -200%;
        }
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
        <!-- Search Results Header -->
        <div class="search-results-header">
            <div class="row">
                <div class="col-md-12">
                    <div class="search-query-display">
                        Search Results
                    </div>
                    <?php 
                    $searchTerm = trim(str_replace('%', '', $find));
                    if(!empty($searchTerm)): 
                        $ret=mysqli_query($con,"select * from products where productName like '$find'");
                        $num=mysqli_num_rows($ret);
                    ?>
                    <div class="results-count">
                        <?php echo $num; ?> results found for "<strong><?php echo htmlspecialchars($searchTerm); ?></strong>"
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class='row outer-bottom-sm'>
        
            <!-- Main Content -->
            <div class='col-md-9'>
                <div class="search-result-container">
                    <div class="category-product inner-top-vs">
                        <?php
                        if(!empty($searchTerm)) {
                            if($num > 0) {
                                while ($row=mysqli_fetch_array($ret)): 
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <div class="image">
                                    <a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
                                        <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" 
                                             alt="<?php echo htmlentities($row['productName']);?>" 
                                             onerror="this.src='assets/images/placeholder-product.jpg'">
                                    </a>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-name">
                                    <a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
                                        <?php echo htmlentities($row['productName']);?>
                                    </a>
                                </h3>
                                
                                <div class="product-price">
                                    <span class="current-price">Kes. <?php echo htmlentities($row['productPrice']);?></span>
                                    <?php if($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                                    <span class="original-price">Kes. <?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($row['productAvailability']=='In Stock'): ?>
                                <div class="product-actions">
                                    <a href="search-result.php?action=add&id=<?php echo $row['id']; ?>" class="btn-add-cart">
                                        <i class="fa fa-shopping-cart"></i> Add to Cart
                                    </a>
                                    <a href="search-result.php?pid=<?php echo htmlentities($row['id'])?>&action=wishlist" class="btn-wishlist" title="Add to Wishlist">
                                        <i class="fa fa-heart"></i>
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="out-of-stock">Out of Stock</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php 
                                endwhile;
                            } else {
                        ?>
                        <div class="no-results">
                            <div class="no-results-icon">🔍</div>
                            <h3>No products found</h3>
                            <p>We couldn't find any products matching "<?php echo htmlspecialchars($searchTerm); ?>"</p>
                            <a href="javascript:history.back()" class="btn-search-again">Try Another Search</a>
                        </div>
                        <?php
                            }
                        } else {
                        ?>
                        <div class="no-results">
                            <div class="no-results-icon">🔍</div>
                            <h3>Enter a search term</h3>
                            <p>Please enter a product name to search for</p>
                            <a href="javascript:history.back()" class="btn-search-again">Go Back</a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/brands-slider.php');?>
<?php include('includes/footer.php');?>

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/echo.min.js"></script>
<script src="assets/js/jquery.easing-1.3.min.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script>
<script src="assets/js/jquery.rateit.min.js"></script>
<script src="assets/js/lightbox.min.js"></script>
<script src="assets/js/bootstrap-select.min.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/scripts.js"></script>

<script>
// Mobile sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.querySelector('.mobile-filter-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    const closeSidebar = document.querySelector('.close-sidebar');
    
    if (filterToggle && sidebar) {
        filterToggle.addEventListener('click', function() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        closeSidebar.addEventListener('click', closeMobileSidebar);
        overlay.addEventListener('click', closeMobileSidebar);
    }
    
    function closeMobileSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            closeMobileSidebar();
        }
    });
    
    // Image lazy loading enhancement
    const images = document.querySelectorAll('.product-image img');
    images.forEach(img => {
        if (img.complete) {
            img.style.background = 'none';
        } else {
            img.addEventListener('load', function() {
                this.style.background = 'none';
            });
            img.addEventListener('error', function() {
                this.src = 'assets/images/placeholder-product.jpg';
                this.style.background = 'none';
            });
        }
    });
});
</script>

</body>
</html>