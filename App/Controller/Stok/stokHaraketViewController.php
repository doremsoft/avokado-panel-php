<?php

namespace App\Controller\Stok;

class stokHaraketViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function index() {

        $siniflar = [];

        $stokSiniflarModel = $this->model("stok", "stokSiniflarModel");

        $siniflar = $stokSiniflarModel->stokSiniflariAl();

        return $this->view("stok-siniflar/stok-siniflar-index", ['siniflar' => $siniflar]);
    }

    
     public function girisSecim() {




         $cariModel = $this->model("cari", "cariViewModel");

         $carilist = $cariModel->getCariList(2);



        return $this->view("stok-haraketler/stok-haraket-giris-getir", ['cariler' => $carilist,'tur'=>2 , 'uri'=>"stok-haraket/giris/"]);
    }
    
        public function cikisSecim() {


            $cariModel = $this->model("cari", "cariViewModel");

            $carilist = $cariModel->getCariList(1);


        return $this->view("stok-haraketler/stok-haraket-cikis-getir", ['cariler' => $carilist,'tur'=>1 , 'uri'=>"stok-haraket/cikis/"]);
    }
    
    
    public function giris($hesap_id, $evrak_tipi = 'non') {

        $bugun=date("Y-m-d");
        $model = $this->model("stok", "stokHaraketModel");
        $cari = $model->getCari($hesap_id);
        $depolar = $model->depolariGetir();

        $dovizmodel = $this->model("doviz", "dovizModel");
        $dovizkurlari =  $dovizmodel->getDovizList();


        return $this->view("stok-haraketler/stok-haraket-giris-ekle", [
            'type' => 0,
            'depolar'=>json_encode($depolar),
            'bugun'=>$bugun,
            'cari' => $cari,
            'hesap_id'=>$hesap_id,
            'evrak_tipi'=>$evrak_tipi,
            'dovizkurlari'=>json_encode($dovizkurlari)


            ]);
    }

    public function girisDuzenle($evrak_id){


        $bugun=date("Y-m-d");

        $model = $this->model("stok", "stokHaraketModel");


        $evrak_tipi = "non";


        $evrak = $model->getAlimEvrak($evrak_id);


        if($evrak){
            if($evrak["evrak_tur"] == "1"){
                $evrak_tipi = "siparis";
            }else   if($evrak["evrak_tur"] == "2"){
                $evrak_tipi = "fatura";
            }else   if($evrak["evrak_tur"] == "3"){
                $evrak_tipi = "fis";
            }else   if($evrak["evrak_tur"] == "0"){
                $evrak_tipi = "hareket";
            }

        }

        $dovizmodel = $this->model("doviz", "dovizModel");
        $dovizkurlari =  $dovizmodel->getDovizList();

        $evrak_kalemler =  $model->getAlimEvrakKalemler($evrak_id);

        $cari = $model->getCari($evrak["cari_id"]);

        $depolar = $model->depolariGetir();

        $kalemler = json_encode($evrak_kalemler);

        $kalemler = str_replace("'"," ",$kalemler);

        return $this->view("stok-haraketler/stok-haraket-giris-ekle", [
            'type' => 1,
            'depolar'=>json_encode($depolar),
            'bugun'=>$evrak["tarih"],
            'cari' => $cari,
            'hesap_id'=>$evrak["cari_id"],
            'evrak_data'=>$evrak,
            'evrak_kalemler'=>$kalemler,
            'evrak_id' => $evrak_id,
            'evrak_tipi'=>$evrak_tipi,
            'dovizkurlari'=>json_encode($dovizkurlari)

        ]);


    }

    public function cikisDuzenle($evrak_id ){

        $bugun=date("Y-m-d");

        $dovizmodel = $this->model("doviz", "dovizModel");
        $dovizkurlari =  $dovizmodel->getDovizList();

        $model = $this->model("stok", "stokHaraketModel");

        $evrak = $model->getCikisEvrak($evrak_id);

        $evrak_tipi = "non";


        if($evrak){
            if($evrak["evrak_tur"] == "1"){

                    $evrak_tipi = "siparis";

            }else   if($evrak["evrak_tur"] == "2"){

                $evrak_tipi = "fatura";

            }else   if($evrak["evrak_tur"] == "3"){

                $evrak_tipi = "fis";

            }else   if($evrak["evrak_tur"] == "0"){

                $evrak_tipi = "hareket";
            }

        }

        $evrak_kalemler =  $model->getsatisEvrakKalemler($evrak_id);

        $cari = $model->getCari($evrak["cari_id"]);

        $depolar = $model->depolariGetir();

        $kalemler = json_encode($evrak_kalemler);

        $kalemler = str_replace("'"," ",$kalemler);

        return $this->view("stok-haraketler/stok-haraket-cikis-ekle", [
            'type' => 1,
            'depolar'=>json_encode($depolar),
            'bugun'=>$evrak["tarih"],
            'cari' => $cari,
            'hesap_id'=>$evrak["cari_id"],
            'evrak_data'=>$evrak,
            'evrak_kalemler'=>$kalemler,
            'evrak_id' => $evrak_id,
            'evrak_tipi'=>$evrak_tipi,
            'dovizkurlari'=>json_encode($dovizkurlari)

        ]);


    }




    public function cikisSiparistenFatura($evrak_id){

        $siparisten_fatura = 1;

        $bugun=date("Y-m-d");

        $model = $this->model("stok", "stokHaraketModel");

        $evrak = $model->getCikisEvrak($evrak_id);

        $evrak_tipi = "non";



        if($evrak){
            if($evrak["evrak_tur"] == "1"){


                    $evrak_tipi = "siparistenfatura";

            }else   if($evrak["evrak_tur"] == "2"){

                $evrak_tipi = "fatura";

            }else   if($evrak["evrak_tur"] == "3"){

                $evrak_tipi = "fis";

            }else   if($evrak["evrak_tur"] == "0"){

                $evrak_tipi = "haraket";
            }

        }

        $evrak_kalemler =  $model->getsatisEvrakKalemler($evrak_id);

        $cari = $model->getCari($evrak["cari_id"]);

        $depolar = $model->depolariGetir();

        $dovizmodel = $this->model("doviz", "dovizModel");
        $dovizkurlari =  $dovizmodel->getDovizList();

        $kalemler = json_encode($evrak_kalemler);

        $kalemler = str_replace("'"," ",$kalemler);

        return $this->view("stok-haraketler/stok-haraket-cikis-ekle", [
            'type' => 1,
            'depolar'=>json_encode($depolar),
            'bugun'=>$evrak["tarih"],
            'cari' => $cari,
            'hesap_id'=>$evrak["cari_id"],
            'evrak_data'=>$evrak,
            'evrak_kalemler'=>$kalemler,
            'evrak_id' => $evrak_id,
            'evrak_tipi'=>$evrak_tipi,
            'dovizkurlari'=>json_encode($dovizkurlari)

        ]);


    }

    public function cikis($hesap_id, $evrak_tipi = 'non') {

        $bugun=date("Y-m-d");
        $model = $this->model("stok", "stokHaraketModel");
        $cari = $model->getCari($hesap_id);
        $depolar = $model->depolariGetir();

        $dovizmodel = $this->model("doviz", "dovizModel");
        $dovizkurlari =  $dovizmodel->getDovizList();

        return $this->view("stok-haraketler/stok-haraket-cikis-ekle", [
            'depolar'=>json_encode($depolar),
            'bugun'=>$bugun,
            'cari' => $cari,
            'hesap_id'=>$hesap_id,
            'evrak_tipi'=>$evrak_tipi,
            'dovizkurlari'=>json_encode($dovizkurlari)]);
    }
    
        public function icTransfer(){
            
             $bugun=date("Y-m-d");
        $model = $this->model("stok", "stokHaraketModel");
        $depolar = $model->depolariGetir();
          return $this->view("stok-haraketler/stok-ic-transfer", [
            'depolar'=>json_encode($depolar),
            'bugun'=>$bugun
         
           ]);
        
    }

}
