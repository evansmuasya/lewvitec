<?php
// Toggle between 'sandbox' and 'live'
define('PESAPAL_ENV', 'live'); // change to 'live' when ready

// Consumer keys
define('PESAPAL_CONSUMER_KEY', 'bp7DTgEtBDWUrOLgOAnWJOQBslXjIxuS');
define('PESAPAL_CONSUMER_SECRET', 'oBXP6QrQ/SqywS42vpcAISRAzmQ=');
define('PESAPAL_IPN_ID', '876071dd-651d-4acd-b0e2-db3eac3020a3');

// Base URLs
define('PESAPAL_BASE_URL_SANDBOX', 'https://cybqa.pesapal.com/pesapalv3');
define('PESAPAL_BASE_URL_LIVE', 'https://pay.pesapal.com/v3');

// Pick URL based on environment
if (PESAPAL_ENV === 'sandbox') {
    define('PESAPAL_BASE_URL', PESAPAL_BASE_URL_SANDBOX);
} else {
    define('PESAPAL_BASE_URL', PESAPAL_BASE_URL_LIVE);
}
?>
