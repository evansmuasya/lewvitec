<?php
session_start();
include('includes/config.php');
include('includes/header.php');
?>

<div class="container" style="padding: 50px 0;">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">Payment Failed</h3>
                </div>
                <div class="panel-body text-center">
                    <i class="fa fa-times-circle fa-5x text-danger" style="margin: 20px 0;"></i>
                    <h2>Payment Processing Failed</h2>
                    <p>There was an issue processing your payment. Please try again.</p>
                    <a href="cart.php" class="btn btn-primary">Return to Cart</a>
                    <a href="index.php" class="btn btn-default">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>