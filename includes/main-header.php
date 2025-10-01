<?php 
// session_start(); // Uncomment if needed
?>

<div class="main-header">
    <div class="container">
        <div class="main-header-inner">
            
            <!-- Logo -->
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo.ico" alt="Logo">
                </a>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <form action="search-result.php" method="get">
                    <input type="text" name="q" placeholder="Search for products..." required>
                    <button type="submit">
                        <i class="icon search-icon"></i>
                    </button>
                </form>
            </div>

            <!-- Icons (Wishlist, Cart, Account) -->
            <div class="header-icons">
                <a href="my-wishlist.php" class="header-icon-link" title="Wishlist">
                    <i class="icon wishlist-icon"></i>
                </a>
                <a href="my-cart.php" class="header-icon-link cart-icon-link" title="My Cart">
                    <i class="icon cart-icon"></i>
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
                <a href="my-account.php" class="header-icon-link" title="My Account">
                    <i class="icon user-icon"></i>
                </a>
            </div>

        </div><!-- /.main-header-inner -->
    </div><!-- /.container -->
</div><!-- /.main-header -->

<style>
/* ============================= */
/* Main Header Styles */
/* ============================= */
.main-header {
    background: #fff;
    padding: 8px 0; /* compact height */
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    position: sticky;
    top: 0;
    z-index: 100;
}

.main-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}

/* Logo */
.logo img {
    height: 38px;
    max-height: 100%;
}

/* Search Bar */
.search-bar {
    flex: 1;
    max-width: 600px;
    margin: 0 20px;
}

.search-bar form {
    display: flex;
    border: 1px solid #d1d5db;
    border-radius: 25px;
    overflow: hidden;
    background: #f9fafb;
}

.search-bar input {
    flex: 1;
    border: none;
    padding: 8px 14px;
    font-size: 14px;
    outline: none;
    background: transparent;
}

.search-bar button {
    background: #667eea;
    color: #fff;
    border: none;
    padding: 0 16px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-bar button:hover {
    background: #5a67d8;
}

.search-icon {
    font-size: 16px;
}

/* Icons */
.header-icons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-icon-link {
    position: relative;
    font-size: 18px;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s ease;
}

.header-icon-link:hover {
    color: #667eea;
    transform: translateY(-1px);
}

/* Cart Badge */
.cart-count {
    position: absolute;
    top: -6px;
    right: -8px;
    background: #ef4444;
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 50%;
    min-width: 16px;
    text-align: center;
}

/* ============================= */
/* Responsive */
/* ============================= */
@media (max-width: 768px) {
    .main-header {
        padding: 6px 0;
    }
    
    .main-header-inner {
        flex-wrap: nowrap;
        gap: 10px;
    }
    
    .logo {
        flex-shrink: 0;
    }
    
    .logo img {
        height: 32px;
    }
    
    .search-bar {
        margin: 0;
        flex: 1;
        max-width: none;
    }
    
    .search-bar form {
        border-radius: 20px;
    }
    
    .search-bar input {
        padding: 6px 12px;
        font-size: 14px;
    }
    
    .search-bar button {
        padding: 0 12px;
    }
    
    .header-icons {
        flex-shrink: 0;
        gap: 10px;
    }
    
    .header-icon-link {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .main-header {
        padding: 4px 0;
    }
    
    .main-header-inner {
        gap: 8px;
    }
    
    .logo img {
        height: 28px;
    }
    
    .search-bar input {
        font-size: 13px;
        padding: 5px 10px;
    }
    
    .search-bar button {
        padding: 0 10px;
    }
    
    .header-icons {
        gap: 8px;
    }
    
    .header-icon-link {
        font-size: 15px;
    }
    
    .cart-count {
        font-size: 10px;
        padding: 1px 4px;
        min-width: 14px;
        top: -5px;
        right: -6px;
    }
}

/* Optional: Add search toggle for very small screens */
@media (max-width: 360px) {
    .search-bar {
        display: none;
    }
    
    .search-toggle {
        display: block;
    }
}

/* If you want to add a search toggle feature */
.search-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 18px;
    color: #374151;
    cursor: pointer;
}

.search-bar.active {
    display: block;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    padding: 10px;
    border-bottom: 1px solid #e5e7eb;
    z-index: 99;
}
</style>

<!-- Optional: Add this script if using search toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.querySelector('.search-toggle');
    const searchBar = document.querySelector('.search-bar');
    
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', function() {
            searchBar.classList.toggle('active');
        });
    }
});
</script>