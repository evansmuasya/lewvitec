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
	    
	    <!-- Custom CSS for Prime Audio Solutions style -->
	    <style>
        :root {
            --primary-color: #1a56db;
            --secondary-color: #1e429f;
            --accent-color: #3b82f6;
            --light-color: #f8fafc;
            --dark-color: #1f2937;
            --text-color: #374151;
            --border-color: #e5e7eb;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 60px;
            border-radius: 0 0 20px 20px;
        }
        
        .hero-content {
            max-width: 600px;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .hero-btn {
            display: inline-block;
            background: white;
            color: var(--primary-color);
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            color: var(--primary-color);
        }
        
        /* Categories Section */
        .categories-section {
            padding: 60px 0;
            background: var(--light-color);
        }
        
        .section-title {
            text-align: center;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 50px;
            color: var(--dark-color);
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }
        
        .category-card {
            background: white;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid var(--border-color);
        }
        
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .category-icon {
            width: 70px;
            height: 70px;
            background: var(--light-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary-color);
            font-size: 28px;
        }
        
        .category-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        /* Products Section */
        .products-section {
            padding: 80px 0;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .product-image {
            position: relative;
            height: 220px;
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
            top: 15px;
            right: 15px;
            background: #ef4444;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-name {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .product-name a {
            color: var(--dark-color);
            text-decoration: none;
        }
        
        .product-name a:hover {
            color: var(--primary-color);
        }
        
        .product-pricing {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .current-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .original-price {
            font-size: 1rem;
            color: #6b7280;
            text-decoration: line-through;
        }
        
        .product-availability {
            margin-bottom: 15px;
        }
        
        .in-stock {
            color: #10b981;
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
            gap: 8px;
            width: 100%;
            padding: 12px 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 15px;
        }
        
        .add-to-cart-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            color: white;
        }
        
        .out-of-stock-btn {
            width: 100%;
            padding: 12px 15px;
            background: #f3f4f6;
            color: #9ca3af;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: not-allowed;
            font-size: 15px;
        }
        
        /* Featured Brands Section */
        .brands-section {
            padding: 60px 0;
            background: var(--light-color);
        }
        
        .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 30px;
            align-items: center;
        }
        
        .brand-logo {
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
            max-height: 60px;
            width: auto;
        }
        
        .brand-logo:hover {
            filter: grayscale(0%);
            opacity: 1;
        }
        
        /* Call to Action Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            border-radius: 20px;
            margin: 60px 0;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .cta-subtitle {
            font-size: 1.25rem;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.9;
        }
        
        .cta-btn {
            display: inline-block;
            background: white;
            color: var(--primary-color);
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            color: var(--primary-color);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 20px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 25px;
            }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
                text-align: center;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.125rem;
            }
            
            .section-title {
                font-size: 1.75rem;
            }
            
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .category-card {
                padding: 20px 15px;
            }
            
            .category-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .product-image {
                height: 180px;
            }
            
            .cta-title {
                font-size: 2rem;
            }
            
            .cta-subtitle {
                font-size: 1.125rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.75rem;
            }
            
            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .brands-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
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
	
	<!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Premium Audio Solutions</h1>
                <p class="hero-subtitle">Experience sound like never before with our high-quality audio equipment and accessories.</p>
                <a href="#products" class="hero-btn">Shop Now</a>
            </div>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Our Categories</h2>
            <div class="categories-grid">
                <?php
                $categories = mysqli_query($con, "SELECT * FROM category ORDER BY categoryName LIMIT 6");
                $category_icons = array(
                    'Headphones' => 'fas fa-headphones',
                    'Speakers' => 'fas fa-volume-up',
                    'Microphones' => 'fas fa-microphone',
                    'Amplifiers' => 'fas fa-bolt',
                    'Accessories' => 'fas fa-cable',
                    'Home Audio' => 'fas fa-home',
                    'Car Audio' => 'fas fa-car',
                    'Studio Equipment' => 'fas fa-sliders-h',
                    'DJ Equipment' => 'fas fa-compact-disc'
                );
                
                while ($category = mysqli_fetch_array($categories)) {
                    $cat_name = $category['categoryName'];
                    $cat_slug = $category['slug'];
                    $icon = isset($category_icons[$cat_name]) ? $category_icons[$cat_name] : 'fas fa-music';
                ?>
                <div class="category-card" onclick="window.location.href='/products/<?php echo $cat_slug; ?>/'">
                    <div class="category-icon">
                        <i class="<?php echo $icon; ?>"></i>
                    </div>
                    <h3 class="category-name"><?php echo htmlentities($cat_name); ?></h3>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    
    <!-- Featured Products Section -->
    <section class="products-section" id="products">
        <div class="container">
            <h2 class="section-title">Featured Products</h2>
            <div class="products-grid">
                <?php
                $featured_products = mysqli_query($con, "SELECT p.*, c.slug as cat_slug, s.s_slug as subcat_slug 
                                                      FROM products p 
                                                      JOIN category c ON p.category = c.id 
                                                      JOIN subcategory s ON p.subCategory = s.id 
                                                      WHERE p.productAvailability = 'In Stock'
                                                      ORDER BY p.id DESC 
                                                      LIMIT 8");
                while ($row = mysqli_fetch_array($featured_products)) {
                    // Generate proper product URL with slugs
                    $product_url = !empty($row['p_slug']) 
                        ? "/products/{$row['cat_slug']}/{$row['subcat_slug']}/{$row['p_slug']}/" 
                        : "/product-details.php?pid=" . htmlentities($row['id']);
                ?>
                
                <div class="product-card">
                    <div class="product-image">
                        <a href="<?php echo $product_url; ?>" class="product-image-link">
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
                            <a href="<?php echo $product_url; ?>">
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
                            <a href="/index.php?action=add&id=<?php echo $row['id']; ?>" 
                               class="add-to-cart-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                Add to Cart
                            </a>
                            <?php else: ?>
                            <button class="out-of-stock-btn" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    
    <!-- Products by Category Sections -->
    <?php
    $categories = mysqli_query($con, "SELECT * FROM category ORDER BY categoryName LIMIT 4");
    while ($category = mysqli_fetch_array($categories)) {
        $cat_id = $category['id'];
        $cat_name = $category['categoryName'];
        $cat_slug = $category['slug'];
    ?>
    <div class="products-section">
        <div class="container">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 class="section-title"><?php echo htmlentities($cat_name); ?></h2>
                <a href="/products/<?php echo $cat_slug; ?>/" class="view-all-category" style="color: var(--primary-color); font-weight: 600; text-decoration: none; display: flex; align-items: center;">
                    View More <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                </a>
            </div>
            
            <div class="products-grid">
                <?php
                $products = mysqli_query($con, "SELECT p.*, c.slug as cat_slug, s.s_slug as subcat_slug 
                                              FROM products p 
                                              JOIN category c ON p.category = c.id 
                                              JOIN subcategory s ON p.subCategory = s.id 
                                              WHERE p.category = $cat_id 
                                              LIMIT 4");
                while ($row = mysqli_fetch_array($products)) {
                    // Generate proper product URL with slugs
                    $product_url = !empty($row['p_slug']) 
                        ? "/products/{$row['cat_slug']}/{$row['subcat_slug']}/{$row['p_slug']}/" 
                        : "/product-details.php?pid=" . htmlentities($row['id']);
                ?>
                
                <div class="product-card">
                    <div class="product-image">
                        <a href="<?php echo $product_url; ?>" class="product-image-link">
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
                            <a href="<?php echo $product_url; ?>">
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
                            <a href="/index.php?action=add&id=<?php echo $row['id']; ?>" 
                               class="add-to-cart-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                Add to Cart
                            </a>
                            <?php else: ?>
                            <button class="out-of-stock-btn" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <!-- Brands Section -->
    <section class="brands-section">
        <div class="container">
            <h2 class="section-title">Trusted Brands</h2>
            <div class="brands-grid">
                <img src="https://via.placeholder.com/150x60/1a56db/ffffff?text=JBL" alt="JBL" class="brand-logo">
                <img src="https://via.placeholder.com/150x60/1a56db/ffffff?text=Sony" alt="Sony" class="brand-logo">
                <img src="https://via.placeholder.com/150x60/1a56db/ffffff?text=Bose" alt="Bose" class="brand-logo">
                <img src="https://via.placeholder.com/150x60/1a56db/ffffff?text=Sennheiser" alt="Sennheiser" class="brand-logo">
                <img src="https://via.placeholder.com/150x60/1a56db/ffffff?text=Audio-Technica" alt="Audio-Technica" class="brand-logo">
                <img src="https://via.placeholder.com/150x60/1a56db/ffffff?text=Shure" alt="Shure" class="brand-logo">
            </div>
        </div>
    </section>
    
    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Upgrade Your Sound Experience?</h2>
            <p class="cta-subtitle">Browse our extensive collection of premium audio equipment and find the perfect solution for your needs.</p>
            <a href="/products/" class="cta-btn">View All Products</a>
        </div>
    </section>

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
		// Smooth scrolling for anchor links
		document.querySelectorAll('a[href^="#"]').forEach(anchor => {
			anchor.addEventListener('click', function (e) {
				e.preventDefault();
				
				const targetId = this.getAttribute('href');
				if(targetId === '#') return;
				
				const targetElement = document.querySelector(targetId);
				if(targetElement) {
					window.scrollTo({
						top: targetElement.offsetTop - 100,
						behavior: 'smooth'
					});
				}
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