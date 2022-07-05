<?php

namespace App\Controller\Banka;

class bankaViewController extends \Dipa\Controller {
    
    
    public function __construct() {
        parent::__construct(true);
    }


    public function index() {

        $model = $this->model("banka", "bankaModel");

        $rapor = $model->hesapSonHareketleri();



        $hareketturleri = [
            0 => 'Hepsi',
            1 => 'Gelir',
            2 => 'Gider'
        ];
        return $this->view("banka/index", [

            'hareketturleri' => $hareketturleri,
            'raporok' => 'ok',
            'raporlar' => $rapor,
        ]);


    }


    public function bankalar() {
        
        
       parent::get_auth("banka");

        $model = $this->model("banka", "bankaModel");

        $bankalar = $model->bankaListesi();

        return $this->view("banka/bankalar", [
                    'bankalar' => $bankalar
        ]);
    }

    public function bankaListesi() {

        $model = $this->model("banka", "bankaModel");

        $bankalar = $model->bankaListesi();


        return $this->view("banka/bankalistesi", [
                    'bankalar' => $bankalar
        ]);
    }

    public function bankaHesaplari($banka_id) {

        $model = $this->model("banka", "bankaModel");

        $hesaplar = $model->hesapListesi($banka_id);

        $banka = $model->bankaDetaylari($banka_id);

        return $this->view("banka/bankahesaplari", [
                    'banka_detay' => $banka,
                    'banka_id' => $banka_id,
                    'hesaplar' => $hesaplar
        ]);
    }

    public function hesapDuzenle($banka_id, $hesap_id) {

        $model = $this->model("banka", "bankaModel");

        $hesap = $model->hesapGetir($hesap_id);

        $banka = $model->bankaDetaylari($banka_id);

        return $this->view("banka/hesap_duzenle", [
                    'banka_detay' => $banka,
                    'banka_id' => $banka_id,
                    'hesap' => $hesap
        ]);
    }

    public function bankaSec() {

        $model = $this->model("banka", "bankaModel");

        $bankalar = $model->bankaListesi();


        return $this->view("banka/bankasec", [
                    'bankalar' => $bankalar
        ]);
    }

    public function bankaHareket($banka_id) {



        $model = $this->model("banka", "bankaModel");

        $hesaplar = $model->hesapListesi($banka_id);

        $banka = $model->bankaDetaylari($banka_id);

        return $this->view("banka/banka_hareket", [
                    'bugun' => date("Y-m-d"),
                    'banka_detay' => $banka,
                    'banka_id' => $banka_id,
                    'hesaplar' => $hesaplar
        ]);
    }

    public function hesapOzetleri() {



        $model = $this->model("banka", "bankaModel");

        $ozet = $model->hesapOzetleri();



        return $this->view("banka/hesap_ozetleri", [
                    'hesaplar' => $ozet
        ]);
    }

    public function hareketRaporlari() {



        $model = $this->model("banka", "bankaModel");

        $hesaplar = $model->hesapOzetleri();


        $hareketturleri = [
            0 => 'Hepsi',
            1 => 'Girişler',
            2 => 'Çıkışlar'
        ];
        return $this->view("banka/hareket-raporlari", [
                    'hesaplar' => $hesaplar,
                    'hareketturleri' => $hareketturleri,
                    'bas_tarih' => date("Y-m-d"),
                    'bit_tarih' => date("Y-m-d"),
                    'raporok' => 'off'
        ]);
    }

}
