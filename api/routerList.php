<?php
$this->route->group('/parakende-api', function() {
    $this->any('/login', "App\Controller\Parakendeapi\parakendeapiAutController@login");
    $this->post('/remote-login', "App\Controller\Parakendeapi\parakendeapiAutController@remoteLogin");
    $this->any('/get-last-change-id', "App\Controller\Parakendeapi\parakendeapiController@getLastId");
    $this->any('/get-last-stocks', "App\Controller\Parakendeapi\parakendeapiController@getUpdatedProducts");
    $this->post('/account-search', "App\Controller\Parakendeapi\parakendeapiController@accountSearch");
    $this->post('/account-search-with-phone', "App\Controller\Parakendeapi\parakendeapiController@accountSearchWithPhone");
    $this->post('/new-current-account', "App\Controller\Parakendeapi\parakendeapiController@newAccount");
    $this->post('/retail-sale-complete', "App\Controller\Parakendeapi\parakendeapiController@retailSaleComplete");
    $this->post('/current-account-details', "App\Controller\Parakendeapi\parakendeapiController@getAccountInfo");
    $this->post('/account-invoice-info-update', "App\Controller\Parakendeapi\parakendeapiController@getAccountInvoiceUpdateInfo");
    $this->post('/update-control', "App\Controller\Parakendeapi\parekendeUpdateController@updateControl");
    $this->post('/get-offline-user', "App\Controller\Parakendeapi\parakendeapiController@getOfflineUser");
    $this->post('/get-tables', "App\Controller\Parakendeapi\parakendeapiController@getTables");
    $this->post('/get-exchange-rate', "App\Controller\Parakendeapi\parakendeapiController@getExchangeRate");
    $this->post('/db-deploy', "App\Controller\Parakendeapi\parakendeapiController@dbDeploy");
    $this->post('/barcode-search', "App\Controller\Parakendeapi\parakendeapiController@getBarcode");
    $this->post('/barcode-archive-search', "App\Controller\Parakendeapi\parakendeapiController@getArchiveBarcode");
    $this->post('/add-stok', "App\Controller\Parakendeapi\parakendeapiController@addStok");
});


$this->route->group('/mobile', function() {
    $this->post('/login', "App\Controller\Mobile\mobileAutController@login");
    $this->any('/bildirim-kontrol', "App\Controller\Mobile\bildirimController@kontrol");
    $this->post('/bildirim-goruldu', "App\Controller\Mobile\bildirimController@bildirimGoruldu");
    $this->any('/get-mobile-stok', "App\Controller\Mobile\mobileController@getStok");
    $this->post('/stok-update', "App\Controller\Mobile\mobileController@stokUpdate");
    $this->post('/stok-fast-price-update', "App\Controller\Mobile\mobileController@stokFastPriceUpdate");
    $this->any('/stok-add', "App\Controller\Mobile\mobileController@stokAdd");
    $this->post('/stok-all-stocks', "App\Controller\Mobile\mobileController@getAllStocks");
    $this->any('/import-stock-counts', "App\Controller\Mobile\mobileController@importStockCount");
    $this->any('/stok-search', "App\Controller\Mobile\mobileController@stokSearch");
    $this->any('/abonelik-paketleri', "App\Controller\Mobile\mobileController@abonelikPaketleri");
    $this->any('/kredi-tutari', "App\Controller\Mobile\mobileController@krediTutari");
    $this->any('/siparis-ekle', "App\Controller\Mobile\mobileController@siparisEkle");
});
