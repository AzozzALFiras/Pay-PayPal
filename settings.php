<?php
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

require_once __DIR__ . '/vendor/autoload.php';

// DATABASE Configuration
define('DBC_HOST', 'localhost');
define('DBC_USER', 'user');
define('DBC_PASS', 'pass');
define('DBC_DB', 'paypal');

// PayPal API Settings
$paypal = new ApiContext(
    new OAuthTokenCredential(
        // Check the mode on 20. line and change
        // Client ID Live or Sandbox
        '',

        // Client Secret Live or Sandbox
        ''
    )
);

$paypal->setConfig([
    'mode' => 'live', // live or sandbox
    'http.ConnectionTimeOut' => 30,

    // Log Settings
    'log.logEnabled' => false,
    'log.FileName' => '',
    'log.LogLevel' => 'FINE',
    'validation.level' => 'log'
]);
?>