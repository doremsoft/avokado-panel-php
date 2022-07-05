<?php

namespace App\Controller\Cari;

class cariViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function add($hesap_tur = 1) {


        $result = $this->paket_kontrol(["standart","gold","bronz","platinium","ucretsiz"],"hesap");

        $cari_limiti = 0;

        if(isset($result["paket_tanimlama_2"])){

            $cari_limiti = $result["paket_tanimlama_2"];

        }




        $cariModel = $this->model("cari", "cariViewModel");

       $toplam_cari =  $cariModel->toplam_cari_sayisi();


        if($toplam_cari > $cari_limiti){

            $this->header->to(self::$redirect_url["hesap"]);

            die();
        }



        if($hesap_tur == 1){

            parent::get_auth("musteri","yeni_musteri");

        }else if($hesap_tur == 2){
            parent::get_auth("tedarikci","yeni_tedarkci");

        }


        return $this->view("cari/cari-add", ['tur' => $hesap_tur]);
    }

    public function cariList($tip = null) {
        
      
       
       if($tip == 1){
           
          parent::get_auth("musteri","musteri_listesi");
         
       }else if($tip == 2){
            parent::get_auth("tedarikci","tedarikci_listesi");
       
       }

        $cariModel = $this->model("cari", "cariViewModel");

        $carilist = $cariModel->getCariList($tip);


        return $this->view("cari/cari-list", ['cariler' => $carilist,'tur'=>$tip , 'uri'=>"cari/list/".$tip]);
    }

    public function show($id) {

        $cariModel = $this->model("cari", "cariViewModel");

        $cari = $cariModel->getCari($id);

        $cari_hesap_ozeti = $cariModel->cariHesapOzeti($id);

        $cari_satislari = $cariModel->cariSatislar($id);

        $year = date("Y");

        return $this->view("cari/cari-show", [
                    'hesap_ozet' => $cari_hesap_ozeti,
                    'cari' => $cari,
            'satislar' => json_encode($cari_satislari),
            'yil'=>$year
                   ]);
    }

    public function edit($id) {

        $cariModel = $this->model("cari", "cariViewModel");

        $cari = $cariModel->getCari($id);

        return $this->view("cari/cari-edit", ['cari' => $cari]);
    }

    public function hesapHareketi($id) {

        $haraketTur = [
            1 => "Hepsi",
            2 => "Satışlar",
            3 => "Alımlar",
            4 => "Tahsilat",
            5 => "Ödeme",
            6 => "Ödeme & Tahsilat",
            7 => "Ürün Bazlı Döküm"
            
        ];

        return $this->view("cari/hesap_hareket", [
                    'hareketturleri' => $haraketTur,
                    'bas_tarih' => date("Y-m-d", strtotime("-7 day", strtotime(date("Y-m-d")))),
                    'bit_tarih' => date("Y-m-d"),
                        'bas_saat' => "00:00:00",
                    'bit_saat' =>"23:59:59",
                    'cari_id' => $id]);
    }




}
