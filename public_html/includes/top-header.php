<?php 
// session_start(); // Uncomment if needed
?>

<div class="top-bar">
    <div class="container">
        <div class="header-top-inner">
            <div class="cnt-account">
                <ul class="list-unstyled">
                    <?php if(strlen($_SESSION['login'])): ?>
                        <li class="welcome-user">
                            <a href="#">
                                <i class="icon user-icon"></i>
                                <span class="welcome-text">Welcome, <?php echo htmlentities($_SESSION['username']); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a href="my-account.php">
                            <i class="icon account-icon"></i>
                            <span>My Account</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="my-wishlist.php">
                            <i class="icon wishlist-icon"></i>
                            <span>Wishlist</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="my-cart.php" class="cart-link">
                            <i class="icon cart-icon"></i>
                            <span>My Cart</span>
                            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div><!-- /.cnt-account -->

            <div class="right-menu">
                <ul class="list-unstyled">
                    <?php if(strlen($_SESSION['login']) == 0): ?>
                        <li>
                            <a href="login.php" class="right-menu-items">
                                <i class="icon login-icon"></i>
                                <span>Login</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="logout.php" class="right-menu-items">
                                <i class="icon logout-icon"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="track-orders.php" class="right-menu-items">
                            <i class="icon track-icon"></i>
                            <span>Track Order</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="clearfix"></div>
        </div><!-- /.header-top-inner -->
    </div><!-- /.container -->
</div><!-- /.top-bar -->

<style>
/* Modern Top Header Styles */
.top-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 0;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}


.header-top-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.cnt-account ul, .right-menu ul {
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
}

.cnt-account li, .right-menu li {
    position: relative;
}

.cnt-account a, .right-menu a {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.3s ease;
    position: relative;
}

.cnt-account a:hover, .right-menu a:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-1px);
}

.icon {
    font-size: 16px;
    width: 18px;
    text-align: center;
}

.cart-link {
    position: relative;
}

.cart-count {
    background: #ff4757;
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 50%;
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 18px;
    text-align: center;
}

.welcome-user .welcome-text {
    font-weight: 500;
}

.right-menu {
    margin-left: auto; /* This pushes the right menu to the far right */
}

.right-menu ul {
    gap: 5px; /* Reduced gap between right menu items */
}

.right-menu-items {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    transition: all 0.3s ease;
    margin-left: 5px; /* Small margin between right menu items */
}

.right-menu-items:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-top-inner {
        flex-direction: column;
        gap: 10px;
    }
    
    .cnt-account ul, .right-menu ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }
    
    .right-menu {
        margin-left: 0;
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .cnt-account ul, .right-menu ul {
        gap: 5px;
    }
    
    .cnt-account a, .right-menu a {
        padding: 6px 8px;
        font-size: 12px;
    }
    
    .icon {
        font-size: 14px;
    }
}
/* ============================= */
/* Compact Top Header Styles */
/* ============================= */
.top-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 13px; /* smaller font */
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    padding: 5px 0; /* reduced height */
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-top-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

/* Account & Right Menu */
.cnt-account ul,
.right-menu ul {
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
    gap: 8px; /* reduced gap */
}

.cnt-account li,
.right-menu li {
    list-style: none;
}

.cnt-account a,
.right-menu a {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px; /* tighter buttons */
    border-radius: 16px;
    font-weight: 500;
    transition: all 0.2s ease;
    font-size: 12px; /* smaller text */
}

.cnt-account a:hover,
.right-menu a:hover {
    background: rgba(255,255,255,0.15);
}

/* Icons */
.icon {
    font-size: 14px; /* smaller icons */
    min-width: 16px;
    text-align: center;
}

/* Cart Count Badge */
.cart-link {
    position: relative;
}

.cart-count {
    background: #ff4757;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 50%;
    position: absolute;
    top: -5px;
    right: -6px;
    min-width: 16px;
    text-align: center;
}

/* Welcome text */
.welcome-user .welcome-text {
    font-weight: 500;
    font-size: 12px;
}

/* Right Menu */
.right-menu {
    margin-left: auto;
}

.right-menu-items {
    background: rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 4px 10px; /* smaller */
}

/* ============================= */
/* Responsive */
/* ============================= */
@media (max-width: 768px) {
    .header-top-inner {
        flex-direction: column;
        gap: 6px;
    }

    .cnt-account ul,
    .right-menu ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
    }

    .right-menu {
        margin-left: 0;
        width: 100%;
        justify-content: center;
        display: flex;
    }
}

@media (max-width: 480px) {
    .cnt-account a,
    .right-menu a {
        padding: 3px 6px;
        font-size: 11px;
    }

    .icon {
        font-size: 13px;
    }

    .welcome-user .welcome-text {
        display: none; /* hide welcome text on very small screens */
    }
}

</style>

<script>
// Add subtle hover effects
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('.cnt-account a, .right-menu a');
    
    links.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>