<?php
$this->route->any('/', 'App\Controller\Home\homeViewController@indexAction');
$this->route->any('/test', 'App\Controller\Home\homeViewController@testAction');


$this->route->get('/storage/*', function(){
    (new App\Controller\Image\ImageController())->getStorage($this->getArrayCopy());
});

$this->route->get('/storage-thumb/*', function(){
    (new App\Controller\Image\ImageController())->getStorageThumb($this->getArrayCopy());
});

$this->route->get('/storage-thumb-noi/*', function(){
    (new App\Controller\Image\ImageController())->getStorageThumb($this->getArrayCopy(),true);
});

$this->route->get('/storage-noi/*', function(){
    (new App\Controller\Image\ImageController())->getStorage($this->getArrayCopy(),true);
});

$this->route->any('/hesapdetaylari', 'App\Controller\Home\homeViewController@hesapDetaylari');
$this->route->any('/multimedia/?', 'App\Controller\Home\homeViewController@media');

//-------------Cari Rota Gruplama-------------//
$this->route->group('/cari', function() {
    $this->any('/add/*', "App\Controller\Cari\cariViewController@add");
    $this->any('/list', "App\Controller\Cari\cariViewController@cariList");
    $this->any('/list/*', "App\Controller\Cari\cariViewController@cariList");
    $this->any('/show/?', "App\Controller\Cari\cariViewController@show");
    $this->any('/edit/?', "App\Controller\Cari\cariViewController@edit");
    $this->post('/update', "App\Controller\Cari\cariActionController@update");
    $this->post('/save', "App\Controller\Cari\cariActionController@save");
    $this->post('/ara', "App\Controller\Cari\cariActionController@arama");
    $this->post('/hepsiara', "App\Controller\Cari\cariActionController@hepsiara");
    $this->any('/hesap-hareketleri/?', "App\Controller\Cari\cariViewController@hesapHareketi");
    $this->post('/hesap-hareketi-goster', "App\Controller\Cari\cariActionController@hesapHareketiGoster");
    $this->any('/hesap-gruplari', "App\Controller\Cari\cariViewController@hesapGruplari");
});
//-------------Cari Rota Gruplama-------------//
$this->route->group('/cari-fatura', function() {
    $this->any('/edit/?/?', "App\Controller\Cari\cariFaturaController@edit");
    $this->post('/update', "App\Controller\Cari\cariFaturaController@update");
});
//-------------Stok Rota Gruplama-------------//
$this->route->group('/stok', function() {
    $this->any('/add', "App\Controller\Stok\stokViewController@add");
    $this->any('/fastadd', "App\Controller\Stok\stokViewController@fastAdd");
    $this->any('/fast-price-update', "App\Controller\Stok\stokViewController@fastPriceUpdate");
    $this->post('/get-fast-price', "App\Controller\Stok\stokActionController@fastPriceUpdateStock");
    $this->any('/list', "App\Controller\Stok\stokViewController@stokList");
    $this->any('/list/?', "App\Controller\Stok\stokViewController@stokList");
    $this->any('/remove/?', "App\Controller\Stok\stokViewController@remove");
    $this->post('/removeok', "App\Controller\Stok\stokActionController@removeok");
    $this->any('/search', "App\Controller\Stok\stokViewController@search");
    $this->post('/search/?', "App\Controller\Stok\stokActionController@stokArama");
    $this->any('/show/?', "App\Controller\Stok\stokViewController@show");

    $this->any('/edit/*', "App\Controller\Stok\stokViewController@edit");


    $this->post('/fast-price-update-st', "App\Controller\Stok\stokActionController@fastPriceUpdate");
    $this->post('/update', "App\Controller\Stok\stokActionController@update");
    $this->post('/save', "App\Controller\Stok\stokActionController@save");
    $this->post('/remove', "App\Controller\Stok\stokActionController@save");
    $this->post('/stok-getir', "App\Controller\Stok\stokActionController@stokGetir");
    $this->post('/stok-seriile-getir', "App\Controller\Stok\stokActionController@stokGetirSeriNoIle");
    $this->post('/stok-getir-isimle', "App\Controller\Stok\stokActionController@stokGetirIsımle");
    $this->post('/stok-getir-isimle-depoya', "App\Controller\Stok\stokActionController@stokGetirIsımleDepoya");
    $this->any('/kritiktekiler', "App\Controller\Stok\stokViewController@stokKritiktekiler");
    $this->any('/cihaz-stoklari-json', "App\Controller\Stok\stokViewController@cihazStokListesiJson");
    $this->post('/oyver', "App\Controller\Stok\stokActionController@stokOyVer");
    $this->any('/webstatus/?/?', "App\Controller\Stok\stokViewController@webStatusChange");


    $this->any('/weblist', "App\Controller\Stok\stokViewController@stokWebList");
    $this->any('/weblist/?', "App\Controller\Stok\stokViewController@stokWebList");

});


$this->route->group('/integration', function() {

    $this->any('/n11/send/?', "App\Controller\Integration\\n11Controller@sendProduct");



});


$this->route->group('/finansal', function() {
    $this->any('/alacaklarim', "App\Controller\Alacaklar\alacaklarViewController@alacaklarList");
    $this->any('/borclarim', "App\Controller\Borclar\borclarViewController@borclarList");
    $this->any('/alacaklarim-guncel', "App\Controller\Alacaklar\alacaklarViewController@guncelAlacaklarList");
    $this->any('/borclarim-guncel', "App\Controller\Borclar\borclarViewController@guncelBorclarList");
    $this->any('/ozet-durum', "App\Controller\Finansal\\finansalViewController@finansalOzet");
    $this->any('/aylik-odeme-cari-secim', "App\Controller\Finansal\\finansalViewController@sabitOdemeCariSecim");
    $this->any('/aylik-sabit-odemeler', "App\Controller\Finansal\\finansalViewController@sabitOdemeler");
    $this->any('/yeni-aylik-sabit-odeme/*', "App\Controller\Finansal\\finansalViewController@yeniSabitOdeme");
    $this->post('/sabit-odeme-kayit', "App\Controller\Finansal\\finansalActionController@sabitOdemeKaydet");
    $this->post('/sabit-odeme-guncelle', "App\Controller\Finansal\\finansalActionController@sabitOdemeGuncelle");
    $this->any('/sabit-odeme-duzenle/*', "App\Controller\Finansal\\finansalViewController@sabitOdemeDuzenle");
    $this->post('/aylik-odeme-listesi-getir', "App\Controller\Finansal\\finansalActionController@aylikOdemeListesi");
    $this->any('/aylik-odeme-listesi', "App\Controller\Finansal\\finansalViewController@aylikOdemeListesi");
    $this->any('/odeme', "App\Controller\Finansal\\finansalViewController@odemeIndex");
    $this->any('/gelir-gider-ozet', "App\Controller\Finansal\\finansalViewController@gelirGiderOzet");
});
$this->route->group('/tahsilat', function() {
    $this->any('/',  "App\Controller\Finansal\\finansalViewController@tahsilatIndex");
    $this->any('/liste', "App\Controller\Finansal\\finansalViewController@tahsilatListe");

});
$this->route->group('/odemeler', function() {
    $this->any('/',  "App\Controller\Finansal\\finansalViewController@odemeIndex");
    $this->any('/liste', "App\Controller\Finansal\\finansalViewController@odemeListe");
});
$this->route->group('/stok-rapor', function() {
    $this->any('/stok-hareketi/?', "App\Controller\Stokrapor\stokRaporViewController@stokHareketi");
    $this->any('/stok-hareketleri', "App\Controller\Stokrapor\stokRaporViewController@stok");
    $this->any('/stoklar', "App\Controller\Stokrapor\stokRaporViewController@stok");
    $this->post('/stok-hareketi-goster', "App\Controller\Stokrapor\stokRaporActionController@stokHareketiGoster");
});
$this->route->group('/stok-birimler', function() {
    $this->any('/', "App\Controller\Stok\stokBirimlerViewController@index");
    $this->post('/save', "App\Controller\Stok\stokBirimlerActionController@save");
    $this->post('/update', "App\Controller\Stok\stokBirimlerActionController@update");
    $this->post('/remove', "App\Controller\Stok\stokBirimlerActionController@remove");
});
$this->route->group('/stok-gruplar', function() {
    $this->any('/', "App\Controller\Stok\stokGruplarViewController@index");
    $this->post('/save', "App\Controller\Stok\stokGruplarActionController@save");
    $this->post('/update', "App\Controller\Stok\stokGruplarActionController@update");
    $this->post('/remove', "App\Controller\Stok\stokGruplarActionController@remove");
});
$this->route->group('/stok-siniflar', function() {
    $this->any('/', "App\Controller\Stok\stokSiniflarViewController@index");
    $this->post('/save', "App\Controller\Stok\stokSiniflarActionController@save");
    $this->post('/update', "App\Controller\Stok\stokSiniflarActionController@update");
    $this->post('/remove', "App\Controller\Stok\stokSiniflarActionController@remove");
});
$this->route->group('/stok-depolar', function() {
    $this->any('/', "App\Controller\Stok\stokDepolarViewController@index");
    $this->any('/list', "App\Controller\Stok\stokDepolarViewController@depoList");
    $this->any('/show/?', "App\Controller\Stok\stokDepolarViewController@show");
    $this->post('/update', "App\Controller\Stok\stokDepolarActionController@update");
    $this->post('/save', "App\Controller\Stok\stokDepolarActionController@save");
    $this->post('/remove', "App\Controller\Stok\stokDepolarActionController@remove");
    $this->post('/urune-gore-listesi', "App\Controller\Stok\stokDepolarActionController@uruneGoreDepoListesi");
});
$this->route->group('/stok-raflar', function() {
    $this->any('/', "App\Controller\Stok\stokRaflarViewController@index");
    $this->post('/update', "App\Controller\Stok\stokRaflarActionController@update");
    $this->post('/save', "App\Controller\Stok\stokRaflarActionController@save");
    $this->post('/remove', "App\Controller\Stok\stokRaflarActionController@remove");
    $this->post('/getir', "App\Controller\Stok\stokRaflarActionController@getir");
});
$this->route->group('/stok-gozler', function() {
    $this->any('/', "App\Controller\Stok\stokGozlerViewController@index");
    $this->post('/update', "App\Controller\Stok\stokGozlerActionController@update");
    $this->post('/save', "App\Controller\Stok\stokGozlerActionController@save");
    $this->post('/remove', "App\Controller\Stok\stokGozlerActionController@remove");
});

$this->route->group('/masa', function() {
    $this->any('/kategorilist', "App\Controller\Masa\masaController@masaKategoriList");
    $this->any('/kategoriler', "App\Controller\Masa\masaController@masaKategoriler");
    $this->post('/grupkaydet', "App\Controller\Masa\masaActionController@grupKaydet");
    $this->any('/islem/?', "App\Controller\Masa\masaController@masaIslem");
    $this->post('/kategori/remove', "App\Controller\Masa\masaActionController@kategoriSil");
    $this->post('/kategori/update', "App\Controller\Masa\masaActionController@kategoriGuncelle");
    $this->post('/remove', "App\Controller\Masa\masaActionController@sil");
    $this->post('/update', "App\Controller\Masa\masaActionController@guncelle");
    $this->post('/masakaydet', "App\Controller\Masa\masaActionController@masaKaydet");
});
$this->route->group('/stok-haraket', function() {
    $this->any('/', "App\Controller\Stok\stokHaraketViewController@index");
    $this->any('/giris', "App\Controller\Stok\stokHaraketViewController@girisSecim");
    $this->any('/cikis', "App\Controller\Stok\stokHaraketViewController@cikisSecim");
    $this->any('/giris/?', "App\Controller\Stok\stokHaraketViewController@giris");
    $this->any('/giris/?/?', "App\Controller\Stok\stokHaraketViewController@giris");
    $this->any('/giris-duzenle/?', "App\Controller\Stok\stokHaraketViewController@girisDuzenle");
    $this->post('/alim-update', "App\Controller\Stok\stokHaraketActionController@girisGuncelle");
    $this->any('/cikis/?', "App\Controller\Stok\stokHaraketViewController@cikis");
    $this->any('/cikis/?/?', "App\Controller\Stok\stokHaraketViewController@cikis");
    $this->any('/cikis-duzenle/*', "App\Controller\Stok\stokHaraketViewController@cikisDuzenle");


    $this->any('/cikis-siparisten-fatura/*', "App\Controller\Stok\stokHaraketViewController@cikisSiparistenFatura");


    $this->post('/cikis-update', "App\Controller\Stok\stokHaraketActionController@cikisGuncelle");
    $this->any('/ic-transfer', "App\Controller\Stok\stokHaraketViewController@icTransfer");
    $this->post('/ic-transfer-yap', "App\Controller\Stok\stokHaraketActionController@icTransferYap");
    $this->post('/girisler-getir', "App\Controller\Stok\stokHaraketActionController@girisHaraketGetir");
    $this->post('/cikislar-getir', "App\Controller\Stok\stokHaraketActionController@cikisHaraketGetir");
    $this->post('/serinolulari-getir', "App\Controller\Stok\stokHaraketActionController@serinolulariGetir");
    $this->post('/update', "App\Controller\Stok\stokHaraketActionController@update");
    $this->post('/save', "App\Controller\Stok\stokGozlerActionController@save");
    $this->post('/remove', "App\Controller\Stok\stokGozlerActionController@remove");
    $this->post('/append', "App\Controller\Stok\stokHaraketActionController@girisKaydet");
    $this->post('/paketle', "App\Controller\Stok\stokHaraketActionController@stokPaketle");
    $this->post('/cikis-kaydet', "App\Controller\Stok\stokHaraketActionController@cikisKaydet");
    $this->post('/siparisten-fatura', "App\Controller\Stok\stokHaraketActionController@siparistenFatura");


    $this->post('/ozel-urunden-cikar', "App\Controller\Stok\stokHaraketActionController@ozelUrundenCikar");
});
$this->route->group('/help', function() {
    $this->any('/message', "App\Controller\Help\helpViewController@message");
});
$this->route->group('/kasa', function() {
    $this->any('/', "App\Controller\Kasa\kasaViewController@index");
    $this->any('/index', "App\Controller\Kasa\kasaViewController@kasaIndex");
    $this->any('/kasalar', "App\Controller\Kasa\kasaViewController@kasalar");
    $this->any('/kasa-haraket-duzenle/*', "App\Controller\Kasa\kasaViewController@ykasaHaraketDuzenle");
    $this->any('/yeni-haraket', "App\Controller\Kasa\kasaViewController@yeniKasaHaraket");
    $this->post('/kasa-haraket-ekle', "App\Controller\Kasa\kasaActionController@kasaHaraketEkle");
    $this->post('/save', "App\Controller\Kasa\kasaActionController@save");
    $this->post('/update', "App\Controller\Kasa\kasaActionController@update");
    $this->post('/remove', "App\Controller\Kasa\kasaActionController@remove");
    $this->any('/kasa-raporlari', "App\Controller\Kasa\kasaViewController@kasaRaporlari");
    $this->any('/kasa-iptal-raporlari', "App\Controller\Kasa\kasaViewController@kasaIptalRaporlari");
    $this->post('/kasa-iptal-raporlari-getir', "App\Controller\Kasa\kasaViewController@kasaIptalRaporlariGoster");
    $this->post('/islem-iptal', "App\Controller\Kasa\kasaActionController@kasaHaraketIptal");
    $this->any('/kasa-raporlari-goster', "App\Controller\Kasa\kasaViewController@kasaRaporlariGoster");
    $this->any('/kasa-virman', "App\Controller\Kasa\kasaViewController@kasaVirman");
    $this->post('/virman-ok', "App\Controller\Kasa\kasaActionController@virman");
    $this->post('/kasa-listesi', "App\Controller\Kasa\kasaActionController@kasaListesi");
});
$this->route->group('/banka', function() {
    $this->any('/', "App\Controller\Banka\bankaViewController@index");
    $this->any('/bankalar', "App\Controller\Banka\bankaViewController@bankalar");
    $this->post('/save', "App\Controller\Banka\bankaActionController@save");
    $this->post('/update', "App\Controller\Banka\bankaActionController@update");
    $this->post('/remove', "App\Controller\Banka\bankaActionController@remove");
    $this->any('/banka-listesi', "App\Controller\Banka\bankaViewController@bankaListesi");
    $this->any('/banka-hesaplari/?', "App\Controller\Banka\bankaViewController@bankaHesaplari");
    $this->any('/sec', "App\Controller\Banka\bankaViewController@bankaSec");
    $this->post('/hesap/ekle', "App\Controller\Banka\bankaActionController@hesapekle");
    $this->post('/hesap/remove', "App\Controller\Banka\bankaActionController@hesapsil");
    $this->any('/hesap/duzenle/?/?', "App\Controller\Banka\bankaViewController@hesapDuzenle");
    $this->post('/hesap/guncelle', "App\Controller\Banka\bankaActionController@hesapguncelle");
    $this->any('/hesap/hareket/?', "App\Controller\Banka\bankaViewController@bankaHareket");
    $this->post('/hesap/guncelle', "App\Controller\Banka\bankaActionController@hesapguncelle");
    $this->post('/hesap/hareket-kaydet', "App\Controller\Banka\bankaActionController@hareketKaydet");
    $this->any('/hesap-ozetleri', "App\Controller\Banka\bankaViewController@hesapOzetleri");
    $this->any('/hareket-raporlari', "App\Controller\Banka\bankaViewController@hareketRaporlari");
    $this->post('/hesap-hareketi-goster', "App\Controller\Banka\bankaActionController@hareketListele");
    $this->post('/hesap/hareket-kaydet-ajax', "App\Controller\Banka\bankaActionController@hareketKaydetAjax");
    $this->post('/hesap-listesi-al', "App\Controller\Banka\bankaActionController@hesapListesiAl");
});
$this->route->group('/tahsilat', function() {
    $this->any('/cari-secim', "App\Controller\Tahsilat\\tahsilatViewController@cariSec");
    $this->any('/add/?', "App\Controller\Tahsilat\\tahsilatViewController@add");
});
$this->route->group('/cek', function() {
    $this->any('/tahsilat-ekle', "App\Controller\Evraklar\ceklerViewController@cekEkle");
    $this->any('/tahsilat-kaydet', "App\Controller\Evraklar\ceklerViewController@cekEkle");
});
$this->route->group('/odeme', function() {
    $this->any('/cari-secim', "App\Controller\Odeme\odemeViewController@cariSec");
    $this->any('/add/?', "App\Controller\Odeme\odemeViewController@add");
});
$this->route->group('/satislar', function() {

    $this->any('/', "App\Controller\Satislar\satislarViewController@index");
    $this->any('/iki-tarih-gore-satislar', "App\Controller\Satislar\satislarViewController@ikiTarihArasiSatisRaporu");
    $this->any('/iki-tarih-gore-satislar/*', "App\Controller\Satislar\satislarViewController@ikiTarihArasiSatisRaporuAnaliz");

});
$this->route->group('/alimlar', function() {
    $this->any('/', "App\Controller\Alimlar\alimlarViewController@index");
    $this->any('/iki-tarih-gore-alimlar', "App\Controller\Alimlar\alimlarViewController@ikiTarihArasiAlimRaporu");
});
$this->route->group('/fatura', function() {
    $this->any('/', "App\Controller\Fatura\\faturaViewController@index");
    $this->any('/show/?/?', "App\Controller\Fatura\\faturaViewController@show");
    $this->any('/yeni-fatura', "App\Controller\Fatura\\faturaViewController@yeniFatura");
    $this->any('/liste', "App\Controller\Fatura\\faturaViewController@faturalar");
});

$this->route->group('/fis', function() {
    $this->any('/', "App\Controller\Fis\\fisViewController@index");
    $this->any('/show/?/?', "App\Controller\Fis\\fisViewController@show");
    $this->any('/yeni-fis', "App\Controller\Fis\\fisViewController@yenifis");
    $this->any('/liste', "App\Controller\Fis\\fisViewController@fisler");
    $this->any('/giris', "App\Controller\Fis\\fisViewController@giris");
    $this->any('/cikis', "App\Controller\Fis\\fisViewController@cikis");
});

$this->route->group('/hareket', function() {
    $this->any('/', "App\Controller\Fis\\fisViewController@index");
    $this->any('/show/?/?', "App\Controller\Fis\\fisViewController@show");
    $this->any('/yeni-fis', "App\Controller\Fis\\fisViewController@yenifis");
    $this->any('/liste', "App\Controller\Fis\\fisViewController@fisler");
    $this->any('/giris', "App\Controller\Fis\\fisViewController@giris");
    $this->any('/cikis', "App\Controller\Fis\\fisViewController@cikis");
});






$this->route->group('/parakende-api', function() {
    $this->post('/login', "App\Controller\Parakendeapi\parakendeapiAutController@login");
    $this->post('/get-last-change-id', "App\Controller\Parakendeapi\parakendeapiController@getLastId");
    $this->post('/get-last-stocks', "App\Controller\Parakendeapi\parakendeapiController@getUpdatedProducts");
    $this->post('/account-search', "App\Controller\Parakendeapi\parakendeapiController@accountSearch");
    $this->post('/new-current-account', "App\Controller\Parakendeapi\parakendeapiController@newAccount");
    $this->post('/retail-sale-complete', "App\Controller\Parakendeapi\parakendeapiController@retailSaleComplete");
    $this->post('/current-account-details', "App\Controller\Parakendeapi\parakendeapiController@getAccountInfo");
    $this->post('/account-invoice-info-update', "App\Controller\Parakendeapi\parakendeapiController@getAccountInvoiceUpdateInfo");
    $this->post('/update-control', "App\Controller\Parakendeapi\parekendeUpdateController@updateControl");
});
$this->route->group('/parametreler', function() {
    $this->any('/', "App\Controller\Parametreler\parametrelerViewController@index");
});
$this->route->group('/excel-import', function() {
    $this->any('/stok-guncelleme-index', "App\Controller\Import\\excelViewController@stokGuncellemeListesi");
    $this->post('/stok-update-upload', "App\Controller\Import\\excelActionController@stokUpdateUpload");
    $this->any('/stok-list', "App\Controller\Import\\excelViewController@stokListesi");
    $this->any('/stok-upload', "App\Controller\Import\\excelActionController@stokUpload");

    $this->any('/stok-adetler-upload', "App\Controller\Import\\excelViewController@stokAdetGuncelleme");

    $this->post('/stok-adetler-guncelle', "App\Controller\Import\\excelActionController@stokAdetlerGuncelle");
});
$this->route->group('/excel-export', function() {
    $this->post('/stok-guncelleme-list-export', "App\Controller\Export\\excelActionController@stokGuncellemeListesi");
    $this->post('/selected-stocks', "App\Controller\Export\\excelActionController@seciliStoklar");
    $this->any('/stok-excel-hazirla', "App\Controller\Export\\exportViewController@prepareExcelExport");
});
$this->route->group('/xml-import', function() {
    $this->any('/', "App\Controller\Import\xmlViewController@anasayfa");
    $this->any('/stok-yukle', "App\Controller\Import\xmlViewController@stokListesiEkle");
    $this->any('/stok-yukle/?', "App\Controller\Import\xmlViewController@stokListesiEkle");
    $this->post('/stok-upload', "App\Controller\Import\importActionController@xmlStokEkleGuncelle");
    $this->post('/xml-analiz', "App\Controller\Import\xmlActionController@xmlAnaliz");
    $this->post('/xml-accept', "App\Controller\Import\xmlActionController@xmlImport");
    $this->post('/xml-download', "App\Controller\Import\xmlActionController@xmlDownload");

    $this->any('/xml-re-download/?', "App\Controller\Import\xmlActionController@xmlReDownload");

    $this->any('/favori-cikart/?', "App\Controller\Import\xmlActionController@favoriCikart");

    $this->any('/favori-al/?', "App\Controller\Import\xmlActionController@favoriAl");




});

$this->route->group('/xml-export', function() {
    $this->any('/index', "App\Controller\Export\xmlViewController@index");
    $this->post('/ip-ekle', "App\Controller\Export\xmlViewController@ipEkle");
    $this->post('/ip-iptal', "App\Controller\Export\xmlViewController@ipiptal");
    $this->post('/servis-ac-kapa', "App\Controller\Export\xmlViewController@acKapa");

});

$this->route->group('/servis', function() {
    $this->any('/xml/?/?', "App\Controller\Export\xmlController@stokAktar");
    $this->any('/xml/?/?/?', "App\Controller\Export\xmlController@stokAktar");


});

$this->route->group('/yazilimlar', function() {
    $this->any('/', "App\Controller\Yazilim\yazilimViewController@index");
    $this->any('/edit/?', "App\Controller\Yazilim\yazilimViewController@edit");
    $this->post('/update', "App\Controller\Yazilim\yazilimActionController@update");
});
$this->route->any('/barkod', "App\Controller\Barkod\barkodViewController@barkodlar");
$this->route->group('/senet', function() {
    $this->any('/', "App\Controller\Evraklar\senetlerViewController@senetler");
    $this->post('/senet-listele', "App\Controller\Evraklar\senetlerActionController@senetListele");
    $this->any('/ekle', "App\Controller\Evraklar\senetlerViewController@ekle");
    $this->post('/kaydet', "App\Controller\Evraklar\senetlerActionController@kaydet");
    $this->any('/islem/?', "App\Controller\Evraklar\senetlerViewController@islem");
    $this->any('/iptal/?', "App\Controller\Evraklar\senetlerActionController@iptal");
    $this->any('/odenmedi/?', "App\Controller\Evraklar\senetlerActionController@odenemedi");

});
$this->route->group('/download', function() {
    $this->any('/list', "App\Controller\Download\downloadViewController@downloadList");
    $this->post('/barkod-win-download', "App\Controller\Download\downloadActionController@barkodWindows");
});
$this->route->group('/doviz', function() {
    $this->any('/list', "App\Controller\Doviz\dovizViewController@dovizList");
    $this->post('/kurlari-cek', "App\Controller\Doviz\dovizViewController@kurlariCek");
    $this->post('/update', "App\Controller\Doviz\dovizViewController@kurGuncelle");
});
$this->route->group('/iptal', function() {
    $this->any('/satis-evrak-iptal/*', "App\Controller\Iptal\iptalViewController@satisEvrakIptalOnay");
    $this->any('/alim-evrak-iptal/*', "App\Controller\Iptal\iptalViewController@alimEvrakIptalOnay");
    $this->post('/satis-evrak', "App\Controller\Iptal\iptalActionController@satisEvrak");
    $this->post('/alim-evrak', "App\Controller\Iptal\iptalActionController@alimEvrak");
});
$this->route->group('/tag', function() {
    $this->any('/list', "App\Controller\Tagler\\taglerViewController@tagList");
    $this->post('/append', "App\Controller\Tagler\\taglerActionController@append");
    $this->post('/update', "App\Controller\Tagler\\taglerActionController@update");
    $this->post('/tag-search', "App\Controller\Tagler\\taglerActionController@search");
});
$this->route->group('/marka', function() {
    $this->any('/list', "App\Controller\Marka\markaViewController@markaList");
    $this->any('/logo-list', "App\Controller\Marka\markaViewController@markaLogoList");
    $this->post('/append', "App\Controller\Marka\markaActionController@append");
    $this->post('/update', "App\Controller\Marka\markaActionController@update");

    $this->post('/logo-list-update', "App\Controller\Marka\markaActionController@logoUpdate");

});
$this->route->group('/backup', function() {
    $this->post('/remove-db-backup', "App\Controller\Backup\backupActionController@removeDbBackup");
    $this->any('/list', "App\Controller\Backup\backupViewController@backupList");
    $this->post('/sqlimport', "App\Controller\Backup\backupActionController@sqlImport");
    $this->post('/db-backup', "App\Controller\Backup\backupActionController@dbBackup");
});

$this->route->group('/etkinlik', function() {
    $this->any('/takvim', "App\Controller\Etkinlik\\etkinlikViewController@takvim");
    $this->any('/etkinlik-al', "App\Controller\Etkinlik\\etkinlikViewController@etkinlikAl");
    $this->post('/takvime-ekle', "App\Controller\Etkinlik\\etkinlikViewController@takvimEtkinlikEkle");
    $this->post('/takvim-etkinlik-iptal', "App\Controller\Etkinlik\\etkinlikViewController@takvimEtkinlikIptal");
    $this->post('/takvimde-guncelle', "App\Controller\Etkinlik\\etkinlikViewController@takvimEtkinlikGuncelle");
    $this->any('/todo-list', "App\Controller\Etkinlik\\etkinlikViewController@todoList");
});


$this->route->group('/post', function() {
    $this->any('/show/?', "App\Controller\Post\postActionController@show");
    $this->any('/remove/?', "App\Controller\Post\postActionController@remove");
    $this->any('/comment-status/?', "App\Controller\Post\postActionController@commentStatus");
    $this->post('/send', "App\Controller\Post\postActionController@sendPost");
    $this->post('/get-posts-with-id', "App\Controller\Post\postActionController@getPostsWithID");
    $this->post('/comments/?', "App\Controller\Post\postActionController@commentsLoad");
    $this->post('/comment/add', "App\Controller\Post\postActionController@commentAppend");
});


$this->route->group('/siparis', function() {
    $this->any('/', "App\Controller\Siparis\siparisViewController@index");
    $this->any('/islem/?', "App\Controller\Siparis\siparisViewController@islem");
    $this->any('/durum-degis/?/?', "App\Controller\Siparis\siparisViewController@durumDegistir");
    $this->any('/gecmis', "App\Controller\Siparis\siparisViewController@gecmis");
    $this->any('/iptaller', "App\Controller\Siparis\siparisViewController@iptaller");
    $this->any('/kargodaki', "App\Controller\Siparis\siparisViewController@kargodaki");
    $this->post('/kargo-no-guncelle', "App\Controller\Siparis\siparisViewController@kargoNoGuncelle");

});

$this->route->group('/bildirim', function() {
    $this->any('/liste', "App\Controller\Bildirim\bildirimController@index");
    $this->any('/enson', "App\Controller\Bildirim\bildirimController@index");
    $this->any('/liste/?', "App\Controller\Bildirim\bildirimController@index");
    $this->any('/remove/*', "App\Controller\Bildirim\bildirimController@remove");
    $this->post('/kontrol', "App\Controller\Bildirim\bildirimController@kontrol");
    $this->post('/yenibildirimler', "App\Controller\Bildirim\bildirimController@yenibildirimler");
    $this->post('/okunduyap', "App\Controller\Bildirim\bildirimController@okunduyap");
    $this->post('/ekle', "App\Controller\Bildirim\bildirimController@ekle");
});


$this->route->group('/web', function() {
    $this->any('/', "App\Controller\Web\webViewController@index");
    $this->post('/save', "App\Controller\Web\webActionController@save");
    $this->post('/add', "App\Controller\Web\webActionController@add");
    $this->any('/page-list', "App\Controller\Web\webViewController@pageList");
    $this->post('/ayar-guncelle', "App\Controller\Web\webActionController@ayarGuncelle");
    $this->post('/ayar-sifirla', "App\Controller\Web\webActionController@ayarSifirla");
    $this->post('/bilgileri-guncelle', "App\Controller\Web\webActionController@bilgileriGuncelle");
    $this->any('/gorunum', "App\Controller\Web\webViewController@gorunum");
    $this->any('/sayfalar', "App\Controller\Web\webViewController@sayfalar");
    $this->post('/sayfalari-sifirla', "App\Controller\Web\webActionController@sayfalariSifirla");
    $this->any('/sayfa-duzenle/?', "App\Controller\Web\webViewController@editPage");
    $this->post('/sayfa-guncelle', "App\Controller\Web\webActionController@sayfaGuncelle");
    $this->any('/sayfa-tasarim-duzenle/?', "App\Controller\Web\webViewController@editTasarimPage");
    $this->post('/sayfa-tasarim-guncelle', "App\Controller\Web\webActionController@sayfaTasarimGuncelle");
    $this->any('/sayfa-editor/?', "App\Controller\Web\webViewController@sayfaEditor");
    $this->any('/tema-editor', "App\Controller\Web\webViewController@temaEditor");
    $this->post('/tema-sablon-guncelle', "App\Controller\Web\webActionController@temaSablonGuncelle");
    $this->post('/tema-sablon-ekle', "App\Controller\Web\webActionController@temaSablonOlustur");
    $this->any('/site-ayarlar', "App\Controller\Web\webViewController@siteAyarlar");
    $this->any('/site-bilgileri', "App\Controller\Web\webViewController@siteBilgiler");
    $this->any('/media', "App\Controller\Web\webViewController@media");
    $this->any('/menuler', "App\Controller\Web\webViewController@menuler");
    $this->any('/eticaret', "App\Controller\Web\webViewController@eticaret");
    $this->any('/yeni-sayfa-ekle', "App\Controller\Web\webActionController@yeniSayfaEkle");
    $this->any('/sayfa-html-govde/?', "App\Controller\Web\webViewController@sayfaHtmlGovde");
    $this->post('/sayfa-html-guncelle', "App\Controller\Web\webActionController@sayfaHtmlGuncelle");
    $this->any('/sayfa-tema-ayarlar/?', "App\Controller\Web\webViewController@sayfaTemaAyarlar");
    $this->post('/sayfa-tema-ayarlari-guncelle', "App\Controller\Web\webActionController@sayfaTemaAyarlariGuncelle");
    $this->any('/galeriler', "App\Controller\Web\webViewController@galeriler");
    $this->any('/slider', "App\Controller\Web\webViewController@slider");
    $this->any('/slayt-duzenle/?', "App\Controller\Web\webViewController@slideEdit");
    $this->post('/yeni-slayt-kaydet', "App\Controller\Web\webActionController@slaytOlustur");
    $this->post('/slayt-guncelle', "App\Controller\Web\webActionController@slaytGuncelle");
    $this->post('/slayt-kalem-ekle', "App\Controller\Web\webActionController@slaytKalemEkle");
    $this->any('/uye', "App\Controller\Web\webViewController@uyeAyarlari");
});

$this->route->group('/hesap-paketleri', function() {
    $this->any('/i/?', "App\Controller\Hpaketler\hesappaketleriViewController@goster");
});



$this->route->group('/work', function() {
    $this->any('/', "App\Controller\Work\workViewController@index");
    $this->any('/yeni-kayit', "App\Controller\Work\workViewController@yeniKayit");

});


///////////////////STANDART ROTALAR//////////////////////////////////////////
//-------------Oturum İşlemleri-------------//
$this->route->any('/login', 'App\Controller\Auth\authViewController@login')->as('login');
$this->route->any('/register', 'App\Controller\Auth\authViewController@register')->as('register');
$this->route->any('/reset-password', 'App\Controller\Auth\authViewController@resetPassword')->as('reset-password');
$this->route->post('/logout', 'App\Controller\Auth\authActionController@logout')->as('logout');
$this->route->post('/login-attack', 'App\Controller\Auth\authActionController@loginAttack')->as('login-attack');

$this->route->any('/mobile-login-attack', 'App\Controller\Auth\authActionController@mobileloginAttack');
$this->route->get('/remote-login-attack', 'App\Controller\Auth\authActionController@remoteLoginAttack');
//-------------Kullanıcı Rota Gruplama-------------//
$this->route->group('/user', function() {
    $this->any('/profile/*', "App\Controller\User\userViewController@profil");
    $this->any('/edit', "App\Controller\User\userViewController@edit");
    $this->any('/add', "App\Controller\User\userViewController@add");
    $this->any('/edit/*', "App\Controller\User\userViewController@edit");
    $this->any('/auth/*', "App\Controller\User\userViewController@yetki");
    $this->any('/list', "App\Controller\User\userViewController@list");
    $this->post('/update', "App\Controller\User\userActionController@update");
    $this->post('/append', "App\Controller\User\userActionController@append");
    $this->post('/auth-update', "App\Controller\User\userActionController@authUpdate");
    $this->any('/password-edit', "App\Controller\User\userViewController@passwordEdit");
    $this->post('/password-update', "App\Controller\User\userActionController@passwordUpdate");
    $this->post('/fatura-gorunum-degistir', "App\Controller\User\userActionController@faturaGorunumDegistir");
});

