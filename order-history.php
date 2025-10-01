<?php 
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
    {   
header('location:login.php');
}
else{

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

        <title>Order History</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
<style>	    
/* Add the modified CSS here */
.container {
  width: 100%;
  padding-right: 0;
  padding-left: 0;
  margin-right: auto;
  margin-left: auto;
}
</style>

        <link rel="shortcut icon" href="assets/images/favicon.ico">
        
        <style>
            :root {
                --primary-color: #3e63c9;
                --secondary-color: #f8f9fa;
                --accent-color: #ff6b6b;
                --text-color: #333;
                --light-text: #6c757d;
                --border-color: #eaeaea;
                --success-color: #28a745;
                --warning-color: #ffc107;
                --info-color: #17a2b8;
            }
            
            body {
                font-family: 'Roboto', sans-serif;
                color: var(--text-color);
                background-color: #f9fafb;
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
                color: var(--light-text);
            }
            
            .breadcrumb-inner li a {
                color: var(--primary-color);
                text-decoration: none;
            }
            
            .breadcrumb-inner li.active {
                color: var(--light-text);
            }
            
            .shopping-cart {
                background: white;
                border-radius: 10px;
                padding: 25px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.05);
                margin-bottom: 30px;
            }
            
            .page-title {
                font-size: 24px;
                font-weight: 600;
                margin-bottom: 25px;
                color: var(--text-color);
                padding-bottom: 15px;
                border-bottom: 2px solid var(--primary-color);
            }
            
            .table-responsive {
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            
            .table-bordered {
                border: 1px solid var(--border-color);
            }
            
            .table-bordered th {
                background-color: var(--secondary-color);
                font-weight: 600;
                color: var(--text-color);
                padding: 15px;
                text-align: center;
                border-bottom: 2px solid var(--primary-color);
            }
            
            .table-bordered td {
                padding: 15px;
                vertical-align: middle;
                text-align: center;
                border-color: var(--border-color);
            }
            
            .cart-image img {
                border-radius: 5px;
                max-width: 80px;
                height: auto;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            
            .cart-product-name-info h4 a {
                color: var(--text-color);
                text-decoration: none;
                font-weight: 600;
                transition: color 0.3s;
            }
            
            .cart-product-name-info h4 a:hover {
                color: var(--primary-color);
            }
            
            .cart-product-grand-total {
                font-weight: 600;
                color: var(--primary-color);
                font-size: 16px;
            }
            
            .cart-product-sub-total {
                color: var(--text-color);
                font-weight: 500;
            }
            
            .order-status {
                display: inline-block;
                padding: 5px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                text-transform: uppercase;
            }
            
            .status-completed {
                background-color: rgba(40, 167, 69, 0.1);
                color: var(--success-color);
            }
            
            .status-pending {
                background-color: rgba(255, 193, 7, 0.1);
                color: var(--warning-color);
            }
            
            .status-processing {
                background-color: rgba(23, 162, 184, 0.1);
                color: var(--info-color);
            }
            
            .btn-track {
                background-color: var(--primary-color);
                color: white;
                border: none;
                padding: 8px 15px;
                border-radius: 5px;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.3s;
                cursor: pointer;
            }
            
            .btn-track:hover {
                background-color: #2c52a7;
                transform: translateY(-2px);
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }
            
            .order-date {
                color: var(--light-text);
                font-size: 14px;
            }
            
            .payment-method-badge {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 15px;
                font-size: 12px;
                font-weight: 500;
                background-color: var(--secondary-color);
                color: var(--text-color);
            }
            
            .empty-orders {
                text-align: center;
                padding: 60px 20px;
                background: white;
                border-radius: 10px;
                box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            }
            
            .empty-orders i {
                font-size: 60px;
                color: var(--light-text);
                margin-bottom: 20px;
            }
            
            .empty-orders h3 {
                color: var(--text-color);
                margin-bottom: 15px;
            }
            
            .empty-orders p {
                color: var(--light-text);
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
            
            .order-card {
                background: white;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                border-left: 4px solid var(--primary-color);
            }
            
            .order-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid var(--border-color);
            }
            
            .order-id {
                font-weight: 600;
                color: var(--text-color);
            }
            
            .order-date-card {
                color: var(--light-text);
                font-size: 14px;
            }
            
            .order-card-body {
                display: flex;
                margin-bottom: 15px;
            }
            
            .order-image {
                flex: 0 0 80px;
                margin-right: 15px;
            }
            
            .order-image img {
                width: 100%;
                border-radius: 5px;
            }
            
            .order-details {
                flex: 1;
            }
            
            .order-product-name {
                font-weight: 600;
                margin-bottom: 5px;
            }
            
            .order-product-name a {
                color: var(--text-color);
                text-decoration: none;
            }
            
            .order-product-name a:hover {
                color: var(--primary-color);
            }
            
            .order-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                font-size: 14px;
                color: var(--light-text);
            }
            
            .order-meta-item {
                display: flex;
                align-items: center;
            }
            
            .order-meta-item i {
                margin-right: 5px;
            }
            
            .order-card-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 15px;
                border-top: 1px solid var(--border-color);
            }
            
            .order-total {
                font-weight: 600;
                color: var(--primary-color);
                font-size: 18px;
            }
            
            @media (max-width: 768px) {
                .shopping-cart {
                    padding: 15px;
                }
                
                .order-card-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .order-card-body {
                    flex-direction: column;
                }
                
                .order-image {
                    margin-right: 0;
                    margin-bottom: 15px;
                }
                
                .order-card-footer {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }
                
                .table-responsive {
                    overflow-x: auto;
                }
            }
        </style>
        
    <script language="javascript" type="text/javascript">
    var popUpWin=0;
    function popUpWindow(URLStr, left, top, width, height)
    {
     if(popUpWin)
    {
    if(!popUpWin.closed) popUpWin.close();
    }
    popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+600+',height='+600+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
    }
    </script>

    </head>
    <body class="cnt-home">
    
        
    
        <!-- ============================================== HEADER ============================================== -->
<header class="header-style-1">
<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>
</header>
<!-- ============================================== HEADER : END ============================================== -->
<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="index.php">Home</a></li>
                <li class='active'>Order History</li>
            </ul>
        </div><!-- /.breadcrumb-inner -->
    </div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content outer-top-xs">
    <div class="container">
        <div class="row inner-bottom-sm">
            <div class="shopping-cart">
                <h2 class="page-title">Your Order History</h2>
                
                <?php 
                $query=mysqli_query($con,"select products.productImage1 as pimg1,products.productName as pname,products.id as proid,orders.productId as opid,orders.quantity as qty,products.productPrice as pprice,products.shippingCharge as shippingcharge,orders.paymentMethod as paym,orders.orderDate as odate,orders.id as orderid from orders join products on orders.productId=products.id where orders.userId='".$_SESSION['id']."' and orders.paymentMethod is not null");
                
                if(mysqli_num_rows($query) > 0) {
                ?>
                
                <!-- Mobile/Tablet View - Card Layout -->
                <div class="d-md-none">
                    <?php 
                    $cnt=1;
                    while($row=mysqli_fetch_array($query)) {
                        $qty = $row['qty'];
                        $price = $row['pprice'];
                        $shippcharge = $row['shippingcharge'];
                        $total = ($qty * $price) + $shippcharge;
                    ?>
                    <div class="order-card">
                        <div class="order-card-header">
                            <div class="order-id">Order #<?php echo $row['orderid']; ?></div>
                            <div class="order-date-card"><?php echo date('M j, Y', strtotime($row['odate'])); ?></div>
                        </div>
                        
                        <div class="order-card-body">
                            <div class="order-image">
                                <img src="admin/productimages/<?php echo $row['proid'];?>/<?php echo $row['pimg1'];?>" alt="<?php echo $row['pname']; ?>">
                            </div>
                            
                            <div class="order-details">
                                <div class="order-product-name">
                                    <a href="product-details.php?pid=<?php echo $row['opid'];?>">
                                        <?php echo $row['pname']; ?>
                                    </a>
                                </div>
                                
                                <div class="order-meta">
                                    <div class="order-meta-item">
                                        <i class="fa fa-cube"></i> Qty: <?php echo $qty; ?>
                                    </div>
                                    <div class="order-meta-item">
                                        <i class="fa fa-credit-card"></i> <?php echo $row['paym']; ?>
                                    </div>
                                    <div class="order-meta-item">
                                        <i class="fa fa-truck"></i> Shipping: Kes. <?php echo $shippcharge; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-card-footer">
                            <div class="order-total">Kes. <?php echo $total; ?></div>
                            <button class="btn-track" onclick="popUpWindow('track-order.php?oid=<?php echo htmlentities($row['orderid']);?>');">
                                <i class="fa fa-map-marker"></i> Track Order
                            </button>
                        </div>
                    </div>
                    <?php $cnt=$cnt+1; } ?>
                </div>
                
               
                
                <?php } else { ?>
                <div class="empty-orders">
                    <i class="fa fa-shopping-bag"></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders yet. Start shopping to see your order history here.</p>
                    <a href="index.php" class="btn btn-primary">Start Shopping</a>
                </div>
                <?php } ?>
            </div><!-- /.shopping-cart -->
        </div> <!-- /.row -->
        
        <!-- ============================================== BRANDS CAROUSEL ============================================== -->
        <?php echo include('includes/brands-slider.php');?>
        <!-- ============================================== BRANDS CAROUSEL : END ============================================== -->
    </div><!-- /.container -->
</div><!-- /.body-content -->
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

    <!-- For demo purposes – can be removed on production -->
    
    <script src="switchstylesheet/switchstylesheet.js"></script>
    
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
    <!-- For demo purposes – can be removed on production : End -->
</body>
</html>
<?php } ?>