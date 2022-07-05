<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Istanbul');
//=====================================
define('DS', "/");
define('BASE_PATH', __DIR__ . DS);
define('APP_DIR', BASE_PATH . 'App');
define('SYSTEM_DIR', BASE_PATH . 'system');
define('CORE_DIR', SYSTEM_DIR . DS . 'core');
define('LIB_DIR', SYSTEM_DIR . DS . 'library');
define('PUBLIC_DIR', BASE_PATH . 'public');
define('TEMP_DIR', PUBLIC_DIR.DS . 'temp');
define('MEDIA_DIR', BASE_PATH . DS."media");
define('STORAGE_PATH',  __DIR__.DS."storage");
define('BACKUP_PATH', SYSTEM_DIR. DS."backup");
define('LOG_PATH', __DIR__. DS."storage");

//=====================================
//require '../dipacorevendors/vendor' . DS . 'autoload.php';

require 'dipacorevendors/vendor/autoload.php';
(new \Dipa\App())->run();



