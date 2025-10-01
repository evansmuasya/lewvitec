<?php 
// session_start(); // Uncomment if needed
?>

<style>
/* ============================= */
/* Modern Navigation Styles */
/* ============================= */

.header-nav {
    background: linear-gradient(135deg, #d4af37 0%, #764ba2 100%);
    padding: 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1000;
}

.navbar-default {
    background: transparent;
    border: none;
    margin-bottom: 0;
    border-radius: 0;
}

.navbar-header {
    float: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 15px;
    min-height: 50px;
}

.navbar-brand-mobile {
    display: none;
    color: white;
    font-weight: 600;
    font-size: 18px;
}

/* Modern Hamburger Button */
.navbar-toggle {
    display: none;
    border: none;
    background: transparent;
    padding: 8px;
    cursor: pointer;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.navbar-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
}

.navbar-toggle .icon-bar {
    display: block;
    background: white;
    width: 22px;
    height: 2px;
    margin: 2px 0;
    transition: all 0.3s ease;
    border-radius: 1px;
}

.navbar-toggle.active .icon-bar:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.navbar-toggle.active .icon-bar:nth-child(2) {
    opacity: 0;
}

.navbar-toggle.active .icon-bar:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

.nav-bg-class {
    background: transparent;
}

.navbar-collapse {
    border: none;
    box-shadow: none;
    padding: 0;
    transition: all 0.3s ease;
}

.nav-outer {
    padding: 0;
}

.navbar-nav {
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    flex-wrap: wrap;
}

.navbar-nav > li {
    float: none;
    display: inline-block;
    position: relative;
}

.navbar-nav > li > a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: white !important;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    background: transparent !important;
}

.navbar-nav > li > a::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 3px;
    background: white;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.navbar-nav > li > a:hover::before,
.navbar-nav > li.active > a::before {
    width: 80%;
}

.navbar-nav > li > a:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    transform: translateY(-2px);
}

.nav-icon {
    margin-right: 8px;
    font-size: 16px;
    transition: transform 0.3s ease;
}

.navbar-nav > li > a:hover .nav-icon {
    transform: scale(1.2);
}

/* Dropdown Menu Styles */
.dropdown-menu {
    background: white;
    border: none;
    border-radius: 12px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    padding: 0;
    min-width: 200px;
    margin-top: 0;
    display: none;
    position: absolute;
    left: 0;
    top: 100%;
    z-index: 1000;
}

.dropdown:hover > .dropdown-menu {
    display: block;
}

.dropdown-menu > li > a {
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #f0f0f0;
}

.dropdown-menu > li:last-child > a {
    border-bottom: none;
}

.dropdown-menu > li > a:hover {
    background: #667eea;
    color: white;
    padding-left: 25px;
}

.dropdown-item-icon {
    margin-right: 10px;
    font-size: 14px;
}

/* Dropdown arrow styling */
.dropdown.yamm {
    position: relative;
}

.dropdown.yamm > a.dropdown-toggle {
    position: absolute;
    right: -15px;
    top: 50%;
    transform: translateY(-50%);
    padding: 0;
    width: 20px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropdown.yamm > a.dropdown-toggle .caret {
    border-top: 6px solid white;
    border-right: 6px solid transparent;
    border-left: 6px solid transparent;
    margin: 0;
    transition: transform 0.3s ease;
}

.dropdown.yamm:hover > a.dropdown-toggle .caret {
    transform: rotate(180deg);
}

/* Category link with dropdown */
.dropdown.yamm > a:first-child {
    padding-right: 30px !important;
}

/* Admin Link Special Styling */
.admin-link {
    background: rgba(255, 255, 255, 0.15) !important;
    margin-left: 10px;
    border-radius: 8px;
}

.admin-link:hover {
    background: rgba(255, 255, 255, 0.25) !important;
}

/* ============================= */
/* Mobile Responsive Design */
/* ============================= */
@media (max-width: 991px) {
    .navbar-header {
        display: flex;
        padding: 0 10px;
        min-height: 45px;
    }
    
    .navbar-brand-mobile {
        display: block;
        font-size: 16px;
    }
    
    .navbar-toggle {
        display: flex;
    }
    
    .navbar-nav {
        flex-direction: column;
        width: 100%;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
    }
    
    .navbar-nav.active {
        max-height: 500px; /* Adjust based on content */
    }
    
    .navbar-nav > li {
        display: block;
        width: 100%;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .navbar-nav > li:last-child {
        border-bottom: none;
    }
    
    .navbar-nav > li > a {
        padding: 12px 15px;
        justify-content: space-between;
        font-size: 14px;
    }
    
    .navbar-nav > li > a::before {
        display: none;
    }
    
    .nav-icon {
        margin-right: 10px;
        font-size: 14px;
    }
    
    .admin-link {
        margin: 0;
        border-radius: 0;
        background: rgba(255, 255, 255, 0.1) !important;
    }
    
    /* Mobile Dropdown Styles */
    .dropdown.yamm > a.dropdown-toggle {
        position: static;
        transform: none;
        padding: 12px 15px;
        width: auto;
        height: auto;
        justify-content: flex-end;
        order: 2;
    }
    
    .dropdown.yamm > a:first-child {
        padding-right: 15px !important;
        order: 1;
        flex: 1;
    }
    
    .dropdown.yamm > a:first-child,
    .dropdown.yamm > a.dropdown-toggle {
        display: flex;
        align-items: center;
    }
    
    .dropdown-menu {
        position: static;
        float: none;
        width: 100%;
        margin-top: 0;
        background: rgba(0, 0, 0, 0.2);
        box-shadow: none;
        border-radius: 0;
        display: none;
        animation: none;
    }
    
    .dropdown.yamm.active > .dropdown-menu {
        display: block;
    }
    
    .dropdown-menu > li > a {
        color: white;
        border-bottom-color: rgba(255, 255, 255, 0.1);
        padding-left: 30px;
        font-size: 13px;
    }
    
    .dropdown-menu > li > a:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: white;
    }
    
    .dropdown-item-icon {
        font-size: 12px;
    }
    
    .caret {
        border-top: 4px solid white;
        border-right: 4px solid transparent;
        border-left: 4px solid transparent;
    }
}

@media (max-width: 480px) {
    .navbar-header {
        min-height: 40px;
        padding: 0 8px;
    }
    
    .navbar-brand-mobile {
        font-size: 15px;
    }
    
    .navbar-toggle {
        width: 36px;
        height: 36px;
    }
    
    .navbar-toggle .icon-bar {
        width: 18px;
        height: 2px;
        margin: 1.5px 0;
    }
    
    .navbar-nav > li > a {
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .nav-icon {
        font-size: 13px;
        margin-right: 8px;
    }
    
    .dropdown-menu > li > a {
        padding: 8px 12px 8px 25px;
    }
}

/* Animation for dropdown */
.animate-dropdown .dropdown-menu {
    animation: fadeInUp 0.3s ease forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Smooth transitions */
.navbar-collapse {
    transition: all 0.4s ease;
}
</style>

<div class="header-nav animate-dropdown">
    <div class="container">
        <div class="yamm navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <span class="navbar-brand-mobile">Menu</span>
                <button class="navbar-toggle" type="button" aria-label="Toggle navigation">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="nav-bg-class">
                <div class="navbar-collapse">
                    <div class="nav-outer">
                        <ul class="nav navbar-nav">
                            <li class="active dropdown yamm-fw">
                                <a href="index.php" class="dropdown-toggle">
                                    <span class="nav-icon">🏠</span>
                                    Home
                                </a>
                            </li>

                            <?php 
                            $sql=mysqli_query($con,"SELECT id, categoryName FROM category LIMIT 9");
                            while($row=mysqli_fetch_array($sql)) {
                                $catId = $row['id'];
                                $subSql=mysqli_query($con,"SELECT id, subcategory FROM subcategory WHERE categoryid='$catId'");
                                $hasSubcategories = mysqli_num_rows($subSql) > 0;
                            ?>
                            <li class="dropdown yamm">
                                <a href="category.php?cid=<?php echo $catId;?>">
                                    <span class="nav-icon"></span>
                                    <?php echo $row['categoryName'];?>
                                </a>
                                <?php if($hasSubcategories): ?>
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php 
                                        mysqli_data_seek($subSql, 0);
                                        while($subRow=mysqli_fetch_array($subSql)) { ?>
                                            <li>
                                                <a href="sub-category.php?scid=<?php echo $subRow['id'];?>">
                                                    <span class="dropdown-item-icon">➡️</span>
                                                    <?php echo $subRow['subcategory'];?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                            <?php } ?>

                            
                        
                        </ul><!-- /.navbar-nav -->
                        <div class="clearfix"></div>                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarNav = document.querySelector('.navbar-nav');
    
    // Toggle mobile menu
    if (navbarToggle && navbarNav) {
        navbarToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navbarNav.classList.toggle('active');
        });
    }
    
    // Mobile dropdown toggle
    const dropdownToggles = document.querySelectorAll('.dropdown.yamm > a.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                e.stopPropagation();
                
                const parent = this.parentElement;
                const wasActive = parent.classList.contains('active');
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown.yamm.active').forEach(item => {
                    if (item !== parent) {
                        item.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                if (!wasActive) {
                    parent.classList.add('active');
                } else {
                    parent.classList.remove('active');
                }
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991) {
            if (!e.target.closest('.dropdown.yamm')) {
                document.querySelectorAll('.dropdown.yamm.active').forEach(item => {
                    item.classList.remove('active');
                });
            }
        }
    });
    
    // Close menu when clicking on a link (on mobile)
    if (window.innerWidth <= 991) {
        const navLinks = document.querySelectorAll('.navbar-nav a:not(.dropdown-toggle)');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navbarToggle.classList.remove('active');
                navbarNav.classList.remove('active');
                
                // Also close any open dropdowns
                document.querySelectorAll('.dropdown.yamm.active').forEach(item => {
                    item.classList.remove('active');
                });
            });
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            // Reset mobile states when switching to desktop
            navbarToggle.classList.remove('active');
            navbarNav.classList.remove('active');
            document.querySelectorAll('.dropdown.yamm.active').forEach(item => {
                item.classList.remove('active');
            });
        }
    });
});
</script>