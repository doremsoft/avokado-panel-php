<?php

namespace App\Controller\Stok;

class stokHaraketActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function girisKaydet() {

        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->girisEkle($this->request);

        $evrak = $this->request->input("evrak_tur");

        $evrak_adi = "";


        if($evrak == 1){

            $evrak_adi = "Sipariş";

        }else if($evrak == 0){

            $evrak_adi = "Stok Giriş Haraket Evrak";

        }else if($evrak == 2){

            $evrak_adi = "Alım Fatura";
        }else if($evrak == 3){

            $evrak_adi = "Alım Fişi";
        }



        $result_type = 0;

        if($this->request->has("resulttype")){

            $result_type = $this->request->input("resulttype");
        }

        if ($result) {
            \Dipa\Io\Log::write("yeni ".$evrak_adi." kayıt edildi", self::$account_no, self::$userInfo["id"]);
            if($result_type == 0){

                $this->header->result("success", "Yeni ".$evrak_adi." Eklendi")->back();
            }else{

              echo json_encode([
                  'status'=>1,
                  'msg' =>"Yeni ".$evrak_adi." Eklendi"
              ]);
            }


        } else {

            \Dipa\Io\Log::write("yeni ".$evrak_adi."  kayıt edilemedi!", self::$account_no, self::$userInfo["id"]);


            if($result_type == 0){

                $this->header->result("fail", "Yeni ".$evrak_adi."  Eklenemedi!")->back();
            }else{

                echo json_encode([
                    'status'=>0,
                    'msg' =>"Yeni ".$evrak_adi." Eklenemedi!"
                ]);
            }

        }
    }

    public function girisGuncelle(){


        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->girisGuncelle($this->request);
        $evrak = $this->request->input("evrak_tur");

        $evrak_adi = "";



        if($evrak == 1){

            $evrak_adi = "Sipariş";

        }else if($evrak == 0){

            $evrak_adi = "Stok Giriş Haraket Evrak";

        }else if($evrak == 2){

            $evrak_adi = "Alım Fatura";
        }else if($evrak == 3){

            $evrak_adi = "Alım Fişi";
        }




        $result_type = 0;

        if($this->request->has("resulttype")){

            $result_type = $this->request->input("resulttype");
        }

        if ($result) {
            \Dipa\Io\Log::write($evrak_adi." kayıt Güncellendi", self::$account_no, self::$userInfo["id"]);
            if($result_type == 0){

                $this->header->result("success", $evrak_adi." Güncellendi")->back();
            }else{

                echo json_encode([
                    'status'=>1,
                    'msg' =>$evrak_adi." Güncellendi"
                ]);
            }


        } else {

            \Dipa\Io\Log::write($evrak_adi."  güncellenemedi!", self::$account_no, self::$userInfo["id"]);


            if($result_type == 0){

                $this->header->result("fail", $evrak_adi."  Güncellenemedi!")->back();
            }else{

                echo json_encode([
                    'status'=>0,
                    'msg' =>$evrak_adi." Güncellenemedi!"
                ]);
            }

        }






    }

    public function siparistenFatura(){


        $evrak = $this->request->input("evrak_tur");

        $evrak_adi = "";



            $evrak_adi = "Siparişten Fatura ";


        $result_type = 0;

        if($this->request->has("resulttype")){

            $result_type = $this->request->input("resulttype");
        }


        $model = $this->model("stok", "stokHaraketModel");


        $result = $model->cikisEkle($this->request , 1);


        if ($result) {

            \Dipa\Io\Log::write($evrak_adi." kayıt Eklendi", self::$account_no, self::$userInfo["id"]);


            if($result_type == 0){


                $this->header->result("success", $evrak_adi." Eklendi")->back();

            }else{

                echo json_encode([
                    'status'=>1,
                    'msg' =>$evrak_adi." Eklendi"
                ]);
            }



        } else {
            \Dipa\Io\Log::write($evrak_adi."  eklenemedi!", self::$account_no, self::$userInfo["id"]);



            if($result_type == 0){

                $this->header->result("fail", $evrak_adi."  Eklenemedi!")->back();
            }else{

                echo json_encode([
                    'status'=>0,
                    'msg' =>$evrak_adi." Eklenemedi!"
                ]);
            }
        }



    }


    public function cikisKaydet() {



        $evrak = $this->request->input("evrak_tur");

        $evrak_adi = "";

        if($evrak == 1){

            $evrak_adi = "Sipariş";

        }else if($evrak == 0){

            $evrak_adi = "Stok Çıkış Haraket Evrak";

        }else if($evrak == 2){

            $evrak_adi = "Satış Fatura";
        }else if($evrak == 3){

            $evrak_adi = "Satış Fişi";
        }



        $result_type = 0;

        if($this->request->has("resulttype")){

            $result_type = $this->request->input("resulttype");
        }



        $model = $this->model("stok", "stokHaraketModel");


        $result = $model->cikisEkle($this->request);


        if ($result) {

            \Dipa\Io\Log::write($evrak_adi." kayıt Eklendi", self::$account_no, self::$userInfo["id"]);


            if($result_type == 0){


                $this->header->result("success", $evrak_adi." Eklendi")->back();
            }else{

                echo json_encode([
                    'status'=>1,
                    'msg' =>$evrak_adi." Eklendi"
                ]);
            }



        } else {
            \Dipa\Io\Log::write($evrak_adi."  eklenemedi!", self::$account_no, self::$userInfo["id"]);



            if($result_type == 0){

                $this->header->result("fail", $evrak_adi."  Eklenemedi!")->back();
            }else{

                echo json_encode([
                    'status'=>0,
                    'msg' =>$evrak_adi." Eklenemedi!"
                ]);
            }
        }
    }


    public function cikisGuncelle(){


        $model = $this->model("stok", "stokHaraketModel");


        $result = $model->cikisGuncelle($this->request);

        $evrak = $this->request->input("evrak_tur");

        $evrak_adi = "";

        if($evrak == 1){

            $evrak_adi = "Sipariş";

        }else if($evrak == 0){

            $evrak_adi = "Stok Çıkış Haraket Evrak";

        }else if($evrak == 2){

            $evrak_adi = "Satış Fatura";
        }else if($evrak == 3){

            $evrak_adi = "Satış Fişi";
        }



        $result_type = 0;

        if($this->request->has("resulttype")){

            $result_type = $this->request->input("resulttype");
        }

        if ($result) {

            \Dipa\Io\Log::write("stok çıkış haraket Güncellendi", self::$account_no, self::$userInfo["id"]);


            if($result_type == 0){


                $this->header->result("success", $evrak_adi." Güncellendi")->back();

            }else{

                echo json_encode([
                    'status'=>1,
                    'msg' =>$evrak_adi." Güncellendi"
                ]);
            }






        } else {

            \Dipa\Io\Log::write("stok çıkış haraket güncellenemedi edilemedi!", self::$account_no, self::$userInfo["id"]);
            if($result_type == 0){


                $this->header->result("success", $evrak_adi." Güncellenemedi")->back();

            }else{

                echo json_encode([
                    'status'=>0,
                    'msg' =>$evrak_adi." Güncellenemedi!"
                ]);
            }

        }


    }


    public function girisHaraketGetir() {



        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->girisleriCek($this->request);


        if ($result) {

            $durum = "ok";
        } else {

            $durum = "non";
        }


        $sonuc = [
            'durum' => $durum,
            'stok' => $result
        ];

        echo json_encode($sonuc);
    }

    public function cikisHaraketGetir() {



        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->cikislariCek($this->request);



        if ($result) {

            $durum = "ok";
        } else {

            $durum = "non";
        }


        $sonuc = [
            'durum' => $durum,
            'stok' => $result
        ];

        echo json_encode($sonuc);
    }

    public function serinolulariGetir() {

        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->serinolulariCek($this->request);


        if ($result) {

            $durum = "ok";
        } else {

            $durum = "non";
        }


        $sonuc = [
            'durum' => $durum,
            'stok' => $result
        ];

        echo json_encode($sonuc);
    }

    public function icTransferYap() {


        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->icTransfer($this->request);


        if ($result) {
            \Dipa\Io\Log::write("stok iç transfer yapıldı", self::$account_no, self::$userInfo["id"]);


            echo "ok";
        } else {

            \Dipa\Io\Log::write("stok iç transfer yapılamadı", self::$account_no, self::$userInfo["id"]);

            echo "non";
        }
    }

    public function ozelUrundenCikar() {

        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->ozeldenCikar($this->request);


        if ($result) {


            \Dipa\Io\Log::write("stok özel üründen çıkarıldı", self::$account_no, self::$userInfo["id"]);


            echo "ok";
        } else {
            \Dipa\Io\Log::write("stok özel üründen çıkarılamadı", self::$account_no, self::$userInfo["id"]);

            echo "non";
        }
    }


    public function stokPaketle(){

        $model = $this->model("stok", "stokHaraketModel");

        $result = $model->stokPaketle($this->request);


        if ($result) {

            \Dipa\Io\Log::write("Paket Stok Oluşturuldu", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Stoktan Paket Oluşturuldu")->back();
        } else {
            \Dipa\Io\Log::write("Paket Stok oluşturulamadı!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Paket Oluşturulamadı!")->back();
        }


    }

}
