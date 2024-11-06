<?php

if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == 's.localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1')
{
  define('IS_DEVELOPMENT_SERVER', true);
} else
{
  define('IS_DEVELOPMENT_SERVER', false);
}

$dbServer = IS_DEVELOPMENT_SERVER ? 'localhost' : "sql211.infinityfree.com";
$dbName = IS_DEVELOPMENT_SERVER ? 'forcerm' : "if0_37531463_travel_notes";
$dbUser = IS_DEVELOPMENT_SERVER ? 'root' : "if0_37531463";
$dbPassword = IS_DEVELOPMENT_SERVER ? '' : "ForceRM123";

// Database Params
define('DB_HOST', $dbServer);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPassword);
define('DB_CHARSET', 'utf8');

// Pentru producție
define('ASSETS_VERSION', time());

define('SECTIONS_PATH', './app/views/sections/');

// App Root
define('APP_ROOT', dirname(dirname(__FILE__)));
// URL Root
define('URL_ROOT', '/');
define('BASE_NAME', $_SERVER['HTTP_HOST']);
// Site Name
define('SITE_NAME', 'Travel Notes');
// App Version
define('APP_VERSION', '1.0.1');
define('APP_DATE', 'AUG 02, 2018');
define('APP_DATE_TIME_FORMAT', 'd/m/Y H:i:s');
