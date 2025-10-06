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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <style>
    /* Search Results Specific Styles */
    .search-results-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px 0;
        margin-bottom: 30px;
        color: white;
    }
    
    .search-query-display {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .results-count {
        font-size: 16px;
        opacity: 0.9;
    }
    
    .search-result-container {
        margin-top: 20px;
    }
    
    .category-product {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        padding: 0 15px;
    }
    
    .product-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        overflow: hidden;
        position: relative;
        border: 1px solid #f0f0f0;
    }
    
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .product-image {
        position: relative;
        overflow: hidden;
        background: #f8f9fa;
        padding: 20px;
    }
    
    .product-image img {
        width: 100%;
        height: 220px;
        object-fit: contain;
        transition: transform 0.4s ease;
    }
    
    .product-card:hover .product-image img {
        transform: scale(1.08);
    }
    
    .product-info {
        padding: 20px;
        background: white;
    }
    
    .product-name {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.4;
        height: 45px;
        overflow: hidden;
    }
    
    .product-name a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .product-name a:hover {
        color: #667eea;
    }
    
    .product-price {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 15px 0;
    }
    
    .current-price {
        font-size: 20px;
        font-weight: 700;
        color: #2c5aa0;
    }
    
    .original-price {
        font-size: 15px;
        color: #999;
        text-decoration: line-through;
    }
    
    .product-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .btn-add-cart {
        flex: 1;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-add-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-wishlist {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #666;
        width: 45px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-wishlist:hover {
        background: #fff5f5;
        color: #e53e3e;
        border-color: #fed7d7;
        transform: translateY(-2px);
    }
    
    .out-of-stock {
        background: #fed7d7;
        color: #c53030;
        padding: 12px;
        text-align: center;
        border-radius: 8px;
        font-weight: 600;
        margin-top: 15px;
        border: 1px solid #feb2b2;
    }
    
    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    }
    
    .no-results-icon {
        font-size: 80px;
        color: #cbd5e0;
        margin-bottom: 25px;
    }
    
    .no-results h3 {
        color: #4a5568;
        margin-bottom: 15px;
        font-size: 24px;
        font-weight: 700;
    }
    
    .no-results p {
        color: #718096;
        margin-bottom: 25px;
        font-size: 16px;
    }
    
    .btn-search-again {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .btn-search-again:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
        .category-product {
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }
    }
    
    @media (max-width: 992px) {
        .category-product {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 18px;
            padding: 0 10px;
        }
        
        .search-results-header {
            padding: 25px 0;
        }
        
        .search-query-display {
            font-size: 24px;
        }
    }
    
    @media (max-width: 768px) {
        .category-product {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
        }
        
        .product-image img {
            height: 200px;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            font-size: 15px;
            height: auto;
        }
        
        .current-price {
            font-size: 18px;
        }
        
        .search-query-display {
            font-size: 22px;
        }
    }
    
    @media (max-width: 576px) {
        .category-product {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 0;
        }
        
        .search-results-header {
            padding: 20px 0;
            margin-bottom: 20px;
        }
        
        .product-actions {
            flex-direction: column;
        }
        
        .btn-wishlist {
            width: 100%;
            height: 45px;
        }
        
        .no-results {
            padding: 60px 15px;
        }
        
        .no-results-icon {
            font-size: 60px;
        }
        
        .no-results h3 {
            font-size: 20px;
        }
    }
    
    /* Loading animation */
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
    
    /* Badge for discounts */
    .discount-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #e53e3e;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 2;
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
                <div class="col-md-12 text-center">
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

        <div class='row'>
            <!-- Main Content -->
            <div class='col-md-12'>
                <div class="search-result-container">
                    <div class="category-product inner-top-vs">
                        <?php
                        if(!empty($searchTerm)) {
                            if($num > 0) {
                                while ($row=mysqli_fetch_array($ret)): 
                                    $discount = 0;
                                    if($row['productPriceBeforeDiscount'] > $row['productPrice']) {
                                        $discount = (($row['productPriceBeforeDiscount'] - $row['productPrice']) / $row['productPriceBeforeDiscount']) * 100;
                                    }
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if($discount > 0): ?>
                                <div class="discount-badge">-<?php echo round($discount); ?>%</div>
                                <?php endif; ?>
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
                                    <span class="current-price">Kes. <?php echo number_format($row['productPrice'], 2);?></span>
                                    <?php if($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                                    <span class="original-price">Kes. <?php echo number_format($row['productPriceBeforeDiscount'], 2);?></span>
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
                            <div class="mt-4">
                                <a href="index.php" class="btn-search-again">Continue Shopping</a>
                                <a href="javascript:history.back()" class="btn-search-again" style="background: #6c757d; margin-left: 10px;">Try Another Search</a>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                        ?>
                        <div class="no-results">
                            <div class="no-results-icon">🔍</div>
                            <h3>Enter a search term</h3>
                            <p>Please enter a product name to search for</p>
                            <a href="index.php" class="btn-search-again">Go to Homepage</a>
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
// Enhanced image loading and interactions
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Add smooth animations for product cards
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.4s ease';
        });
    });
    
    // Add to cart animation
    const addToCartButtons = document.querySelectorAll('.btn-add-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!this.classList.contains('disabled')) {
                this.classList.add('disabled');
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
                
                setTimeout(() => {
                    this.innerHTML = '<i class="fa fa-check"></i> Added!';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fa fa-shopping-cart"></i> Add to Cart';
                        this.classList.remove('disabled');
                    }, 1000);
                }, 500);
            }
        });
    });
});
</script>

</body>
</html>