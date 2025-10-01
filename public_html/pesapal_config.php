<?php
// Toggle between 'sandbox' and 'live'
define('PESAPAL_ENV', 'live'); // change to 'live' when ready

// Consumer keys
define('PESAPAL_CONSUMER_KEY', 'bp7DTgEtBDWUrOLgOAnWJOQBslXjIxuS');
define('PESAPAL_CONSUMER_SECRET', 'oBXP6QrQ/SqywS42vpcAISRAzmQ=');
define('PESAPAL_IPN_ID', '1fed5e55-1458-4df3-b900-db4a1b59d231
');

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
