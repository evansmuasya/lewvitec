<?php
// Toggle between 'sandbox' and 'live'
define('PESAPAL_ENV', 'live'); // change to 'live' when ready

// Consumer keys
define('PESAPAL_CONSUMER_KEY', 'bp7DTgEtBDWUrOLgOAnWJOQBslXjIxuS');
define('PESAPAL_CONSUMER_SECRET', 'oBXP6QrQ/SqywS42vpcAISRAzmQ=');
define('PESAPAL_IPN_ID', 'da8fce76-8478-4371-adad-db3eaea6d38c');

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
