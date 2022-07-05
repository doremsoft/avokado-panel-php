<?php



  $url = "http://".$_SERVER["HTTP_HOST"]."/api";
return [
    /*
     * URL Adresleri
     */
        'manager_url' => "http://".$_SERVER["HTTP_HOST"]."/manager",
    'url' => $url,
    'publicUrl' => $url . '/public',
    'assetsUrl' => $url . '/public/assets',
    'mediaUrl' => $url . '/public/media/',
    'media100Url' => $url . '/public/w100px/',
    'mediacacheid' => 1,
    /*
     * Tema Ayarları
     */
    'activeTemplate' => 'default',
    'activateLanguage' => 'tr',
    'title' => 'Avokado Yazılım Sistemleri',
    'system_type'=>'api',
        'system_name'=> 'avpapi',
    'auth' => [
        'login_with_account_number' => true,
        'remember' => true,
        'register' => false,
        'resetpassword' => false
    ],
    'form' => [
        'csrfToken' => true
    ],
    /*
     * Db
     */
    'db' => [
        'lib' => 'pdo',
        'host' => 'localhost',
        'driver' => 'mysql',
        'masterDbName' => 'avokado',
        'database' => 'avokado',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    ],
    'key' => ''
];

