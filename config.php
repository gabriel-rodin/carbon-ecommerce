<?php
define('BASEURL', $_SERVER['DOCUMENT_ROOT'].'/');

/**
 * Sample database configs
 * Here is where you will set your database credentials
 */
define('DB_HOST', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'gh_carbon_ecommerce');

define('APP_TIMEZONE', 'Asia/Manila');

define('CART_COOKIE', 'SBwi72UCklwiqzz2');
define('CART_COOKIE_EXPIRE',time() + (86400 * 30)); //seconds of day x 30
define('TAXRATE',0.12); // %12 tax rate

define('CURRENCY', 'php');
define('CHECKOUTMODE','TEST'); // Change test to live when you are ready to go live

if (CHECKOUTMODE == 'TEST') {
  define('STRIPE_PRIVATE','sk_test_NXiQBRmDjWsmJcsh1XRLdHvy');
  define('STRIPE_PUBLIC','pk_test_2fT0Con7tdkeTVXAxAj24EhZ');
}
if (CHECKOUTMODE == 'LIVE') {
  define('STRIPE_PRIVATE',''); //GET LIVE CODE IN STRIPE
  define('STRIPE_PUBLIC','');
}
