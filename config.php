<?php

        $main_url = "http://".$_SERVER["HTTP_HOST"];

        $url = "http://".$_SERVER["HTTP_HOST"];

        $main_login_url = $url."/login";

        $media_url = $url."/media/";

        $login_attack_url = $main_url."/login-attack";


return [
    /*
     * URL Adresleri
     */
    'manager_url' =>  $main_url ,
    'main_url' => $main_url,
    'main_login_url' => $main_login_url,
    'login_attack_url' => $login_attack_url,
    'url' => $url,
    'publicUrl' => $url . '/public',
    'assetsUrl' => $url . '/public/assets',
    'mediaUrl' => $media_url,
    'media100Url' => $url . '/public/w100px/',
    'mediacacheid' => 4,
    'system_type' => 'panel',
        'system_name'=> 'avp',
    /*
     * Tema Ayarları
     */
    'activeTemplate' => 'frogetorv',
    'activateLanguage' => 'tr',
    'title' => 'Avokado Yazılım Sistemleri',
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
    'key_id' => 1,
    'key' => 'CKXH2U9RPY3EFD70TLS1ZG4N8WQBOVI6AMJ5',
    'hash_key' => 'CKLH3U9YPY3EFD70TLS1ZG4N8WQYOVI6AMJ8',
    'mediakey'=>'asdasd'
];

