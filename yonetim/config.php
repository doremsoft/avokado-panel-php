<?php


$main_url = "http://".$_SERVER["HTTP_HOST"]."/yonetim";
$url = "http://".$_SERVER["HTTP_HOST"]."/yonetim";
$main_login_url = "http://".$_SERVER["HTTP_HOST"]."/yonetim/login";


return [
    /*
     * URL Adresleri
     */
        'main_url' => $main_url,
    'main_login_url' => $main_login_url,
    'url' => $url,
    'publicUrl' => $url . '/public',
    'assetsUrl' => $url . '/public/assets',
    'mediaUrl' => $url . '/public/media/',
    'media100Url' => $url . '/public/w100px/',
    'mediacacheid' => 1,
        'system_type'=>'yonetim',
    /*
     * Tema Ayarları
     */
    'activeTemplate' => 'frogetorv',
    'activateLanguage' => 'tr',
    'title' => 'Avokado Yazılım Sistemleri',
     'system_type'=>'yonetim',
         'system_name'=> 'avpyonetim',
    'auth' => [
        'login_with_account_number' => false,
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
        'password' => '1234',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    ],
    'key' => ''
];

