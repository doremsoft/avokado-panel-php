<?php

namespace App\Controller\Cari;

class cariActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $cariModel = $this->model("cari", "cariActionModel");

        $result = $cariModel->addCari($this->request);

        if ($result) {

            \Dipa\Io\Log::write("Cari hesap eklendi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Cari Eklendi")->to("cari/add/" . $this->request->input("tur"));
        } else {
            \Dipa\Io\Log::write("cari hesap eklenemedi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("fail", "Cari Hesap Eklenemedi!")->back();
        }
    }

    public function update() {

        $id = $this->request->input("id");

        $cariModel = $this->model("cari", "cariActionModel");

        $result = $cariModel->cariUpdate($this->request, $id);

        if ($result) {
            \Dipa\Io\Log::write("cari hesap güncellendi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("success", "Cari Hesap Güncellendi")->to("cari/edit/$id");
        } else {
            \Dipa\Io\Log::write("cari hesap güncellenemedi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("fail", "Cari Hesap Güncellenemedi!")->back();
        }
    }

    public function hepsiara() {
        if ($this->request->has("query")) {

            $carimodel = $this->model("cari", "cariActionModel");

            $query = $this->request->input("query");


            if ($this->request->input("tur") != null) {
                $tur = $this->request->input("tur");
            } else {

                $tur = 1;
            }

            $cari = $carimodel->cariHepsiArama($query, $tur);

            if ($cari) {

                $durum = "ok";
            } else {

                $durum = "non";
            }


            $sonuc = [
                'query' => $query,
                'durum' => $durum,
                'cari' => $cari
            ];

            echo json_encode($sonuc);
        } else {
            echo "non";
        }
    }

    public function arama() {
        if ($this->request->has("query")) {

            $carimodel = $this->model("cari", "cariActionModel");

            $query = $this->request->input("query");


            if ($this->request->input("tur") != null) {
                $tur = $this->request->input("tur");
            } else {

                $tur = 1;
            }

            $cari = $carimodel->cariArama($query, $tur);

            if ($cari) {

                $durum = "ok";
            } else {

                $durum = "non";
            }


            $sonuc = [
                'query' => $query,
                'durum' => $durum,
                'cari' => $cari
            ];

            echo json_encode($sonuc);
        } else {
            echo "non";
        }
    }

    public function hesapHareketiGoster() {

        $carimodel = $this->model("cari", "cariActionModel");

        $haraketTur = [
            1 => "Hepsi",
            2 => "Satış Faturaları",
            3 => "Alım Faturaları",
            4 => "Tahsilat",
            5 => "Ödeme",
            6 => "Ödeme & Tahsilat",
            7 => "Ürün Bazlı Döküm"
        ];

        $raporlar = $carimodel->hareketGetir($this->request);

        $cari = $carimodel->getCari($this->request->input("cari_id"));

        return $this->view("cari/hesap_hareket", [
                    'hareketturleri' => $haraketTur,
                    'stur' => $this->request->input("stur"),
                    'bas_tarih' => $this->request->input("bas_tarih"),
                    'bit_tarih' => $this->request->input("bit_tarih"),
                    'bas_saat' => $this->request->input("bas_saat"),
                    'bit_saat' => $this->request->input("bit_saat"),
                    'raporok' => 'ok',
                    'raporlar' => $raporlar,
                    'cari' => $cari,
                    'cari_id' => $this->request->input("cari_id")]);
    }

}
