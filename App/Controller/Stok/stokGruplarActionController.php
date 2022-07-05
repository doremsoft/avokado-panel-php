<?php

namespace App\Controller\Stok;

class stokGruplarActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $stokGruplarModel = $this->model("stok", "stokGruplarModel");

        $result = $stokGruplarModel->stokGrupEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("stok grubu eklendi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("success", "Stok Grup Eklendi")->to("stok-gruplar");
        } else {
            \Dipa\Io\Log::write("stok grubu eklenemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Stok Grup Eklenemedi!")->back();
        }
    }

    public function update() {

        $stokGruplarModel = $this->model("stok", "stokGruplarModel");

        $result = $stokGruplarModel->stokGrupGuncelle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("stok grubu güncellendi", self::$account_no, self::$userInfo["id"]);

            echo "ok";
        } else {

            \Dipa\Io\Log::write("stok grubu güncellenemdi", self::$account_no, self::$userInfo["id"]);


            echo "fail";
        }
    }

    public function remove() {

        $stokGruplarModel = $this->model("stok", "stokGruplarModel");

        $result = $stokGruplarModel->stokGrupSil($this->request);

        if ($result) {

            \Dipa\Io\Log::write("stok grubu silindi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Stok Grup Silindi")->to("stok-gruplar");
        } else {
            \Dipa\Io\Log::write("stok grubu silinemedi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("fail", "Stok Grup Silinemedi!")->back();
        }
    }

}
