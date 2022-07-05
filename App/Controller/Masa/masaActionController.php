<?php

namespace App\Controller\Masa;

use \Dipa\Controller;

class masaActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function grupKaydet() {

        $masaModel = $this->model("masa", "masaModel");

        $result = $masaModel->masaGrupEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("yeni masa grubu eklendi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("success", "Masa Grubu Eklendi")->to("masa/kategoriler");
        } else {

            \Dipa\Io\Log::write("yeni masa grubu eklenemedi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("fail", "Masa Grubu Eklenemedi!")->back();
        }
    }

    public function masaKaydet() {

        $masaModel = $this->model("masa", "masaModel");

        $result = $masaModel->masaKaydet($this->request);

        if ($result) {

            \Dipa\Io\Log::write("yeni masa eklendi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("success", "Masa Eklendi")->back();
        } else {

            \Dipa\Io\Log::write("yeni masa eklenemedi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("fail", "Masa Eklenemedi!")->back();
        }
    }

    public function kategoriSil() {

        $masaModel = $this->model("masa", "masaModel");

        $result = $masaModel->kategoriSil($this->request);

        if ($result) {

            \Dipa\Io\Log::write("masa grubu silindi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("success", "Masa Grubu Silindi")->to("masa/kategoriler");
        } else {

            \Dipa\Io\Log::write("yeni grubu silinemedi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("fail", "Masa Grubu Silinemedi!")->back();
        }
    }

    public function kategoriGuncelle() {


        $masaModel = $this->model("masa", "masaModel");

        $result = $masaModel->kategoriGuncelle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("masa grubu güncellendi", self::$account_no, self::$userInfo["id"]);



            echo "ok";
        } else {

            echo "fail";
        }
    }

    public function sil() {

        $masaModel = $this->model("masa", "masaModel");

        $result = $masaModel->sil($this->request);

        if ($result) {

            \Dipa\Io\Log::write("masa silindi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("success", "Masa  Silindi")->back();
        } else {
            \Dipa\Io\Log::write("masa silinemedi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("fail", "Masa  Silinemedi!")->back();
        }
    }

    public function guncelle() {


        $masaModel = $this->model("masa", "masaModel");

        $result = $masaModel->guncelle($this->request);

        if ($result) {
            \Dipa\Io\Log::write("masa güncellendi", self::$account_no, self::$userInfo["id"]);


            echo "ok";
        } else {
            \Dipa\Io\Log::write("masa güncellenemdi", self::$account_no, self::$userInfo["id"]);



            echo "fail";
        }
    }

}
