<?php

namespace App\Controller\Kasa;

class kasaActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $kasaModel = $this->model("kasa", "kasalarModel");

        $result = $kasaModel->kasaEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("yeni kasa eklendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Kasa Eklendi")->to("kasa/kasalar");
        } else {


            \Dipa\Io\Log::write("yeni kasa eklenemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Kasa Eklenemedi!")->back();
        }
    }

    public function update() {

        $kasaModel = $this->model("kasa", "kasalarModel");

        $result = $kasaModel->kasaGuncelle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("kasa güncellendi", self::$account_no, self::$userInfo["id"]);

            echo "ok";
        } else {
            \Dipa\Io\Log::write("kasa güncellenemedi", self::$account_no, self::$userInfo["id"]);

            echo "fail";
        }
    }

    public function remove() {

        $kasaModel = $this->model("kasa", "kasalarModel");

        $result = $kasaModel->kasaSil($this->request);

        if ($result) {

            \Dipa\Io\Log::write("kasa silindi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("success", "Kasa Silindi")->to("kasa/kasalar");
        } else {

            \Dipa\Io\Log::write("kasa silinemedi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("fail", "Kasa Silinemedi!")->back();
        }
    }

    public function kasaHaraketEkle() {


        $kasaModel = $this->model("kasa", "kasalarModel");

        $result = $kasaModel->kasaHaraketKaydet($this->request);

        if ($result) {

            \Dipa\Io\Log::write("kasa haraket eklendi", self::$account_no, self::$userInfo["id"]);
            echo "ok";
        } else {


            \Dipa\Io\Log::write("kasa haraket eklenemedi", self::$account_no, self::$userInfo["id"]);

            echo "fail";
        }
    }

    public function kasaHaraketIptal() {


        $kasaModel = $this->model("kasa", "kasalarModel");

        $result = $kasaModel->kasaHaraketIptal($this->request);

        if ($result) {

            \Dipa\Io\Log::write("kasa haraket iptal edildi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Kasa haraket iptal edildi...")->to("kasa/kasa-raporlari");

        } else {

            \Dipa\Io\Log::write("Kasa haraket iptal edilemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "İşlem Başarısız Oldu!")->to("kasa/kasa-raporlari");
        }
    }

    public function virman() {


        if ($this->request->input("kaynak_kasa_id") != $this->request->input("hedef_kasa_id")) {


            if ($this->request->input("tutar") > 0) {


                $kasaModel = $this->model("kasa", "kasalarModel");

                $result = $kasaModel->kasalarArasiVirman($this->request);

                if ($result) {

                    \Dipa\Io\Log::write("kasa virman yapıldı", self::$account_no, self::$userInfo["id"]);


                    $this->header->result("success", "Virman Gerçekleştirildi....")->to("kasa/kasa-virman");
                } else {


                    \Dipa\Io\Log::write("kasa virman başarısız oldu", self::$account_no, self::$userInfo["id"]);


                    $this->header->result("fail", "İşlem Başarısız Oldu!")->back();
                }
            } else {


                \Dipa\Io\Log::write("kasa virman başarısız oldu: tutar 0dan büyük olmalı", self::$account_no, self::$userInfo["id"]);



                $this->header->result("fail", "Transfer Tutarı 0'dan Büyük Olmalı!")->back();
            }
        } else {


            \Dipa\Io\Log::write("kasa virman başarısız oldu:  Aykı kasaya virman yapılamaz", self::$account_no, self::$userInfo["id"]);



            $this->header->result("fail", "Aynı Kasaya Virman Yapılamaz!")->back();
        }
    }

    public function kasaListesi(){
        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();


        if($kasalar){

            echo json_encode([
            "status" =>1,
            "kasalar"=>$kasalar
        ]);
    }else{
            echo json_encode([
                "status" =>0
            ]);
        }



    }


}
