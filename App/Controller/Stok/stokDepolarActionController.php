<?php

namespace App\Controller\Stok;

class stokDepolarActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $stokDepolarModel = $this->model("stok", "stokDepolarModel");

        $result = $stokDepolarModel->stokDepoEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("stok  depo eklendi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("success", "Stok Depo Eklendi")->to("stok-depolar");
        } else {


            \Dipa\Io\Log::write("stok  depo eklenemedi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("fail", "Stok Depo Eklenemedi!")->back();
        }
    }

    public function update() {

        $stokDepolarModel = $this->model("stok", "stokDepolarModel");

        $result = $stokDepolarModel->stokDepoGuncelle($this->request);

        if ($result) {
            \Dipa\Io\Log::write("stok  depo güncelennedi", self::$account_no, self::$userInfo["id"]);


            echo "ok";
        } else {

            \Dipa\Io\Log::write("stok  depo güncellenemdi", self::$account_no, self::$userInfo["id"]);


            echo "fail";
        }
    }

    public function remove() {

        $stokDepolarModel = $this->model("stok", "stokDepolarModel");

        $result = $stokDepolarModel->stokDepoSil($this->request);

        if ($result) {
            \Dipa\Io\Log::write("stok  depo silindi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Stok Depo Silindi")->to("stok-depolar");
        } else {

            \Dipa\Io\Log::write("stok  depo silinemedi", self::$account_no, self::$userInfo["id"]);



            $this->header->result("fail", "Stok Depo Silinemedi!")->back();
        }
    }

    public function uruneGoreDepoListesi() {

        if ($this->request->has("urun_id")) {

            $stokModel = $this->model("stok", "stokDepolarModel");

            $urun_id = $this->request->input("urun_id");



            $stok = $stokModel->urununDepolardakiListesi($urun_id);

            if ($stok) {

                $durum = "ok";
            } else {

                $durum = "non";
            }


            $sonuc = [
                'durum' => $durum,
                'stok' => $stok
            ];

            echo json_encode($sonuc);
        } else {
            echo "non";
        }
    }

}
