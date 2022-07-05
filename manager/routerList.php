<?php

$this->route->any('/', 'App\Controller\Home\homeViewController@indexAction');


$this->route->group('/hesaplar', function() {
    $this->any('/list', "App\Controller\Hesaplar\hesaplarViewController@list");
    $this->any('/add', "App\Controller\Hesaplar\hesaplarViewController@add");
    $this->any('/edit/?', "App\Controller\Hesaplar\hesaplarViewController@edit");
    $this->post('/update', "App\Controller\Hesaplar\hesaplarViewController@update");
    $this->post('/append', "App\Controller\Hesaplar\hesaplarViewController@append");
});


$this->route->group('/bildirim', function() {
    $this->any('/add', "App\Controller\Bildirim\bildirimController@add");
    $this->post('/send', "App\Controller\Bildirim\bildirimController@send");
});

$this->route->group('/database', function() {
    $this->any('/update-prepare/?', "App\Controller\Database\dbActionController@updatePrepare");
    $this->post('/update', "App\Controller\Database\dbActionController@update");
    $this->any('/arge-update', "App\Controller\Database\dbActionController@argeUpdate");
    $this->any('/get-stocks/?', "App\Controller\Database\dbActionController@getStocks");
    $this->post('/all-db-update', "App\Controller\Database\dbActionController@allDbUpdate");
});

$this->route->group('/test', function() {
    $this->any('/deploy-test', "App\Controller\Test\\testViewController@deploy");
    $this->post('/deploy-test-ok', "App\Controller\Test\\testViewController@deployOk");
});


$this->route->group('/hesap', function() {
    $this->post('/detaylar', "App\Controller\Hesap\hesapActionController@detaylar");
    $this->post('/kalan-kredi', "App\Controller\Hesap\hesapActionController@kalanKredi");
    $this->post('/siparis-ekle', "App\Controller\Hesap\hesapActionController@siparisEkle");
    $this->post('/paketler', "App\Controller\Hesap\hesapActionController@paketler");
    $this->post('/paket-liste', "App\Controller\Hesap\hesapActionController@paketListesi");
});





///////////////////STANDART ROTALAR//////////////////////////////////////////
//-------------Oturum İşlemleri-------------//
$this->route->any('/login', 'App\Controller\Auth\authViewController@login')->as('login');
$this->route->any('/register', 'App\Controller\Auth\authViewController@register')->as('register');
$this->route->any('/reset-password', 'App\Controller\Auth\authViewController@resetPassword')->as('reset-password');
$this->route->post('/logout', 'App\Controller\Auth\authActionController@logout')->as('logout');
$this->route->post('/login-attack', 'App\Controller\Auth\authActionController@loginAttack')->as('login-attack');
//-------------Kullanıcı Rota Gruplama-------------//
$this->route->group('/user', function() {
    $this->any('/edit', "App\Controller\User\userViewController@edit");
    $this->post('/update', "App\Controller\User\userActionController@update");
    $this->any('/password-edit', "App\Controller\User\userViewController@passwordEdit");
    $this->post('/password-update', "App\Controller\User\userActionController@passwordUpdate");
});

