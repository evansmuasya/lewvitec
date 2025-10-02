<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// ==============================
// Add to Cart (via product slug)
// ==============================
if (isset($_GET['action']) && $_GET['action'] == "add") {
    // Prefer p_slug if present, otherwise fall back to numeric id (for backward compatibility)
    $p_slug = $_GET['p_slug'] ?? '';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (!empty($p_slug)) {
        // Find product by slug
        $sql_p = "SELECT * FROM products WHERE p_slug = ?";
        $stmt = $con->prepare($sql_p);
        $stmt->bind_param("s", $p_slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $row_p = $result->fetch_assoc();
    } elseif ($id > 0) {
        // Fallback: Find product by ID (legacy support)
        $sql_p = "SELECT * FROM products WHERE id = ?";
        $stmt = $con->prepare($sql_p);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row_p = $result->fetch_assoc();
    }

    if (!empty($row_p)) {
        $pid = $row_p['id'];
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['quantity']++;
        } else {
            $_SESSION['cart'][$pid] = array("quantity" => 1, "price" => $row_p['productPrice']);
        }
        echo "<script>alert('Product has been added to the cart');</script>";
    } else {
        echo "<script>alert('Invalid product');</script>";
    }

    echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">

	    <title>Lewvitec Sounds</title>

	    <!-- Bootstrap Core CSS -->
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
	    
	    <!-- Custom CSS for carousel and category sections -->
	    <style>
        /* Carousel Styles */

/* Add the modified CSS here */
.container {
  width: 100%;
  padding-right: 0;
  padding-left: 0;
  margin-right: auto;
  margin-left: auto;
}

        
.main-slider {
    margin: 30px 0;
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.slide {
    position: relative;
    height: 400px;
}

.slide-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.slide-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    color: white;
    padding: 30px;
}

.slide-content h2 {
    font-size: 32px;
    margin-bottom: 10px;
}

.slide-content p {
    font-size: 18px;
    margin-bottom: 20px;
    max-width: 600px;
}

.btn {
    display: inline-block;
    background: #667eea;
    color: white;
    padding: 12px 25px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    background: #5a67d8;
    transform: translateY(-2px);
}

/* Controls for slider */
.slider-controls {
    position: absolute;
    bottom: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
    z-index: 10;
}

.control-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.control-btn:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Category Section Styles */
.categories-section {
    padding: 30px 0;
    background: #f8fafc;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    padding: 0 20px;
}

.category-card {
    background: white;
    border-radius: 8px;
    padding: 15px 10px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.category-icon {
    font-size: 24px;
    margin-bottom: 10px;
    color: #667eea;
}

.category-name {
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

/* Products by Category Section */
.category-products-section {
    padding: 40px 0;
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 0 20px;
}

.category-title {
    font-size: 22px;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.view-all-category {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.view-all-category i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.view-all-category:hover i {
    transform: translateX(5px);
}

/* Product card adjustments for smaller icons */
.product-card .product-actions .add-to-cart-btn svg {
    width: 14px;
    height: 14px;
}

.product-card .product-actions .add-to-cart-btn {
    padding: 8px 15px;
    font-size: 14px;
}

/* Products Section */
.products-section {
    padding: 40px 0;
    background: #f8fafc;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 0 20px;
}

.section-title {
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    padding: 0 20px;
    margin-bottom: 40px;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.product-image {
    position: relative;
    height: 180px;
    overflow: hidden;
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .image-overlay {
    opacity: 1;
}

.view-details {
    color: white;
    font-weight: 600;
    padding: 8px 15px;
    border: 2px solid white;
    border-radius: 25px;
    font-size: 14px;
}

.discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ef4444;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.product-info {
    padding: 15px;
}

.product-name {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #2d3748;
}

.product-name a {
    color: #2d3748;
    text-decoration: none;
}

.product-name a:hover {
    color: #667eea;
}

.product-pricing {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.current-price {
    font-size: 16px;
    font-weight: 700;
    color: #2d3748;
}

.original-price {
    font-size: 14px;
    color: #718096;
    text-decoration: line-through;
}

.product-availability {
    margin-bottom: 12px;
}

.in-stock {
    color: #48bb78;
    font-size: 14px;
    font-weight: 500;
}

.out-of-stock {
    color: #ef4444;
    font-size: 14px;
    font-weight: 500;
}

.product-actions {
    margin-top: 15px;
}

.add-to-cart-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 10px 15px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 14px;
}

.add-to-cart-btn:hover {
    background: #5a67d8;
    transform: translateY(-1px);
}

.out-of-stock-btn {
    width: 100%;
    padding: 10px 15px;
    background: #e2e8f0;
    color: #718096;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: not-allowed;
    font-size: 14px;
}

.section-footer {
    text-align: center;
    padding: 0 20px;
}

.view-all-btn {
    display: inline-block;
    padding: 12px 25px;
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.view-all-btn:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .slide {
        height: 250px;
    }

    .slide-content {
        padding: 15px;
        text-align: center;
    }

    .slide-content h2 {
        font-size: 20px;
    }

    .slide-content p {
        font-size: 14px;
    }

    .btn {
        padding: 10px 18px;
        font-size: 14px;
    }

    .category-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .product-image {
        height: 150px;
    }

    .product-name {
        font-size: 14px;
    }

    .current-price {
        font-size: 14px;
    }

    .original-price {
        font-size: 12px;
    }

    .add-to-cart-btn {
        font-size: 13px;
        padding: 8px 12px;
    }
}

@media (max-width: 480px) {
    .slide {
        height: 200px;
    }

    .slide-content h2 {
        font-size: 18px;
    }

    .slide-content p {
        font-size: 13px;
    }

    .category-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .product-image {
        height: 130px;
    }

    .product-name {
        font-size: 13px;
    }

    .add-to-cart-btn {
        font-size: 12px;
        padding: 7px 10px;
    }
}

	    </style>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="assets/images/favicon.ico">

	</head>
    <body class="cnt-home">
	
		
	
		<!-- ============================================== HEADER ============================================== -->
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>

<!-- ============================================== HEADER : END ============================================== -->
<div class="body-content outer-top-xs" id="top-banner-and-menu">
	<div class="container">
	
	    <!-- Main Carousel Slider -->
        <div class="main-slider">
            <div class="slide">
                <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80" alt="Summer Sale" class="slide-img">
                <div class="slide-content">
                    <h2>Summer Sale Up To 50% Off</h2>
                    <p>Get the best deals on summer essentials. Limited time offer!</p>
                    <a href="index.php" class="btn">Shop Now</a>
                </div>
            </div>
            
            <div class="slider-controls">
                <div class="control-btn prev-btn">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="control-btn next-btn">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Categories Section -->
       
		
		<div class="furniture-container homepage-container">
		<div class="row">
		
			<div class="col-xs-12 col-sm-12 col-md-3 sidebar">
				<!-- ================================== TOP NAVIGATION ================================== -->
	
<!-- ================================== TOP NAVIGATION : END ================================== -->
			</div><!-- /.sidemenu-holder -->	
			
			<div class="col-xs-12 col-sm-12 col-md-9 homebanner-holder">
				<!-- ========================================== SECTION – HERO ========================================= -->
			


			
<!-- ========================================= SECTION – HERO : END ========================================= -->	

<!-- ============================================== INFO BOXES : END ============================================== -->		
			</div><!-- /.homebanner-holder -->
			
		</div><!-- /.row -->

        <!-- Products by Category Sections -->
        <?php
        $categories = mysqli_query($con, "SELECT * FROM category ORDER BY categoryName LIMIT 12");
        while ($category = mysqli_fetch_array($categories)) {
            $cat_id = $category['id'];
            $cat_name = $category['categoryName'];
        ?>
        <div class="category-products-section">
            <div class="category-header">
                <h2 class="category-title"><?php echo htmlentities($cat_name); ?></h2>
                <a href="category.php?cid=<?php echo $cat_id; ?>" class="view-all-category">View More <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="products-grid">
                <?php
                $products = mysqli_query($con, "SELECT * FROM products WHERE category = $cat_id LIMIT 12");
                while ($row = mysqli_fetch_array($products)) {
                ?>
                
                <div class="product-card">
                    <div class="product-image">
                        <a href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>" class="product-image-link">
                            <img src="admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>" 
                                 alt="<?php echo htmlentities($row['productName']); ?>"
                                 loading="lazy"
                                 class="product-img">
                            <div class="image-overlay">
                                <span class="view-details">View Details</span>
                            </div>
                        </a>
                        
                        <?php if($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                        <div class="discount-badge">
                            <span class="discount-percent">
                                <?php 
                                $discount = (($row['productPriceBeforeDiscount'] - $row['productPrice']) / $row['productPriceBeforeDiscount']) * 100;
                                echo round($discount) . '% OFF';
                                ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="product-details.php?pid=<?php echo htmlentities($row['id']); ?>">
                                <?php echo htmlentities($row['productName']); ?>
                            </a>
                        </h3>

                        <div class="product-pricing">
                            <span class="current-price">Kes. <?php echo number_format($row['productPrice']); ?></span>
                            <?php if($row['productPriceBeforeDiscount'] > $row['productPrice']): ?>
                            <span class="original-price">Kes. <?php echo number_format($row['productPriceBeforeDiscount']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="product-availability">
                            <?php if($row['stockQuantity'] > 0 ): ?>
                            <span class="in-stock">✓ In Stock</span>
                            <?php else: ?>
                            <span class="out-of-stock">✗ Out of Stock</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-actions">
                            <?php if($row['productAvailability'] == 'In Stock'): ?>
                            <a href="index.php?page=product&action=add&id=<?php echo $row['id']; ?>" 
                               class="add-to-cart-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                Add to Cart
                            </a>
                            <?php else: ?>
                           
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

		


<?php include('includes/footer.php');?>
	
	<script src="assets/js/jquery-1.11.1.min.js"></script>
	
	<script src="assets/js/bootstrap.min.js"></script>
	
	<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>
	
	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="assets/js/jquery.rateit.min.js"></script>
    <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>

	<!-- Add Tiny Slider for the carousel -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/min/tiny-slider.js"></script>
	
	<script>
		// Initialize the slider
		document.addEventListener('DOMContentLoaded', function() {
			var slider = tns({
				container: '.main-slider',
				items: 1,
				slideBy: 1,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplayButtonOutput: false,
				controls: false,
				nav: false,
				speed: 1000,
				responsive: {
					640: {
						items: 1
					},
					768: {
						items: 1
					},
					1024: {
						items: 1
					}
				}
			});
			
			// Custom controls
			document.querySelector('.prev-btn').addEventListener('click', function () {
				slider.goTo('prev');
			});
			
			document.querySelector('.next-btn').addEventListener('click', function () {
				slider.goTo('next');
			});
		});
	</script>
	
	<script>
		$(document).ready(function(){ 
			$(".changecolor").switchstylesheet( { seperator:"color"} );
			$('.show-theme-options').click(function(){
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
	

	

</body>
</html>