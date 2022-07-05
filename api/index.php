<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Istanbul');
header('Content-Type: application/json');
//=====================================
define('DS', "/");
define('BASE_PATH', __DIR__ . DS);
define('APP_DIR', BASE_PATH . 'App');
define('SYSTEM_DIR', BASE_PATH . 'system');
define('CORE_DIR', SYSTEM_DIR . DS . 'core');
define('LIB_DIR', SYSTEM_DIR . DS . 'library');
define('PUBLIC_DIR', BASE_PATH . 'public');
define('LOG_PATH', __DIR__. DS."..".DS."..".DS."storage");
//=====================================
//require 'vendor' . DS . 'autoload.php';
//require '../dipacorevendors/vendor' . DS . 'autoload.php';
require '../dipacorevendors/vendor/autoload.php';
(new \Dipa\App())->run();



