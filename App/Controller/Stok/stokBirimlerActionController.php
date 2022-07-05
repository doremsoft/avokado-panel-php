<?php

namespace App\Controller\Stok;

class stokBirimlerActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $stokBirimlerModel = $this->model("stok", "stokBirimlerModel");

        $result = $stokBirimlerModel->stokBirimEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("stok birimi eklendi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("success", "Stok Birim Eklendi")->to("stok-birimler");
        } else {

            \Dipa\Io\Log::write("stok birimi eklenemedi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("fail", "Stok Birim Eklenemedi!")->back();
        }
    }

    public function update() {

        $stokBirimlerModel = $this->model("stok", "stokBirimlerModel");

        $result = $stokBirimlerModel->stokBirimGuncelle($this->request);

        if ($result) {
            \Dipa\Io\Log::write("stok birimi güncellendi", self::$account_no, self::$userInfo["id"]);

            echo "ok";
        } else {
            \Dipa\Io\Log::write("stok birimi güncellenemedi", self::$account_no, self::$userInfo["id"]);

            echo "fail";
        }
    }

    public function remove() {

        $stokBirimlerModel = $this->model("stok", "stokBirimlerModel");

        $result = $stokBirimlerModel->stokBirimSil($this->request);

        if ($result) {
            \Dipa\Io\Log::write("stok birimi silindi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Stok Birim Silindi")->to("stok-birimler");
        } else {
            \Dipa\Io\Log::write("stok birimi silinemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Stok Birim Silinemedi!")->back();
        }
    }

}
