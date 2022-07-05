<?php


if (isset($_SERVER["HTTP_HOST"])) {

    if ($_SERVER["HTTP_HOST"] == "localhost") {

        $main_url = "http://localhost/manager";

        $url = "http://localhost/manager";
        
        $main_login_url = "http://localhost/manager/login";
        
    } else {

        $url = "https://dogusdicle.com/yonetim/manager";

        $main_login_url = "https://dogusdicle.com/yonetim/manager/login";

        $main_url = "https://dogusdicle.com/yonetim/manager";
    }
} else {
    $url = "http://localhost/avokado/web/avokadoyonetim";

    $main_url = "http://localhost/avokado/web/avokadoyonetim";

    $main_login_url = "http://localhost/avokado/web/avokadoyonetim/login";
}




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
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    ],
    'key' => ''
];

