<?php

namespace App\Controller\Evraklar;

use Dipa\Controller;

class senetlerActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function kaydet() {



        if ($this->request->input("tutar") > 0 && $this->request->input("evrak_no") != "") {

            $evraklarModel = $this->model("evraklar", "senetModel");

            $kayit_id = $evraklarModel->senetEkle($this->request);

            if ($kayit_id) {

                \Dipa\Io\Log::write("Senet eklendi", self::$account_no, self::$userInfo["id"]);

                $odeme_tarih = $this->request->input("odemetarih");

                $senet_tur = $this->request->input("senet_tur");


                $odeme_tarih_onceki_gun =  date("Y-m-d", strtotime($odeme_tarih." -1 Day"));

                if($senet_tur == 1){

                    $tur = " Alacağınız ";

                }else   if($senet_tur == 2){

                    $tur = " Ödemeniz ";
                }

                $evraklarModel->bildirimEkle(new Controller(), "Senet ".$tur." Mevcut!", date("d.m.Y",strtotime($odeme_tarih))." Tarihinde ".$this->request->input("tutar")."₺ Senet {$tur} Bulunmakta", 8, $odeme_tarih_onceki_gun,Controller::$account_details["alarm_bildirim_saati"], Controller::$userInfo["id"],$kayit_id,"warning");
                $evraklarModel->bildirimEkle(new Controller(), "Senet ".$tur." Mevcut!","Bugün ".$this->request->input("tutar")."₺ Senet {$tur} Bulunmakta", 8,$odeme_tarih, Controller::$account_details["alarm_bildirim_saati"], Controller::$userInfo["id"],$kayit_id,"warning");


                echo "ok";

            } else {
                \Dipa\Io\Log::write("Senet eklenemedi", self::$account_no, self::$userInfo["id"]);
                echo "non";
            }
        } else {

            echo "inputeksik";
        }
    }

    public function senetListele() {


        $model = $this->model("evraklar", "senetModel");

        $senetler = $model->senetListesi($this->request);

        return $this->view("evraklar/senet-listesi", [
                    'senetler' => $senetler,
                    's_evrak_tip' => $this->request->input("evrak_tip"),
                    's_odeme_durum' => $this->request->input("odeme_durum"),
                    'bas_tarih' => $this->request->input("bas_tarih"),
                    'bit_tarih' => $this->request->input("bit_tarih"),
                    'action' => 'ok'
        ]);
    }

    public function iptal($id){


        $model = $this->model("evraklar", "senetModel");

        $sonuc = $model->senetIptal($id);


        if ($sonuc) {

            \Dipa\Io\Log::write("Senet iptal edildi :".$id, self::$account_no, self::$userInfo["id"]);

            $model->bildirimlerIptal(new Controller(), 8 ,$id);

            $this->header->result("success", "Senet iptal edildi")->to("senet");


        } else {
            \Dipa\Io\Log::write("Senet iptal edilemedi :".$id, self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Senet iptal edilemedi!")->back();
        }


    }


    public function odenemedi($id){


        $model = $this->model("evraklar", "senetModel");

        $sonuc = $model->senetOdenemedi($id);

        if ($sonuc) {

            \Dipa\Io\Log::write("Senet Ödenemedi Yapıldı :".$id, self::$account_no, self::$userInfo["id"]);

            $model->bildirimlerIptal(new Controller(), 8 ,$id);

            $this->header->result("success", "Senet Ödenemedi Yapıldı")->to("senet");

        } else {
            \Dipa\Io\Log::write("Senet Ödenemedi Yapılamadı! :".$id, self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Senet Ödenemedi Yapılamadı!")->back();
        }


    }

}
