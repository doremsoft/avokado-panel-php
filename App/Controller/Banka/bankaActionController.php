<?php

namespace App\Controller\Banka;

class bankaActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->bankaEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("Yeni Banka Eklendi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Banka Eklendi")->to("banka/bankalar");
        } else {


            \Dipa\Io\Log::write("Banka Eklenemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Banka Eklenemedi!")->back();
        }
    }

    public function update() {

        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->bankaGuncelle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("Banka Güncellendi", self::$account_no, self::$userInfo["id"]);
            echo "ok";
        } else {

            \Dipa\Io\Log::write("Banka Güncellenemedi", self::$account_no, self::$userInfo["id"]);
            echo "fail";
        }
    }

    public function remove() {
        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->bankaSil($this->request);

        if ($result) {
            \Dipa\Io\Log::write("Banka Silindi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Banka Silindi")->to("banka/bankalar");
        } else {


            \Dipa\Io\Log::write("Banka Silinemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Banka Silinemedi! Hesap Var İse Önce Hesapları Silin!")->back();
        }
    }

    public function hesapekle() {

        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->bankaHesapEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("Banka Hesabı Eklendi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Banka Hesabı Eklendi")->to("banka/banka-hesaplari/" . $this->request->input("banka_id"));
        } else {

            \Dipa\Io\Log::write("Banka Hesabı Eklenemedi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("fail", "Banka Hesabı Eklenemedi!")->back();
        }
    }

    public function hesapsil() {
        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->bankaHesapSil($this->request);

        if ($result) {


            \Dipa\Io\Log::write("banka hesabı silindi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("success", "Banka Hesabı Silindi")->to("banka/banka-hesaplari/" . $this->request->input("banka_id"));
        } else {


            \Dipa\Io\Log::write("Banka hesabı silinemedi", self::$account_no, self::$userInfo["id"]);
            $this->header->result("fail", "Banka Hesabı Silinemedi! ")->back();
        }
    }

    public function hesapguncelle() {

        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->hesapGuncelle($this->request);

        if ($result) {



            \Dipa\Io\Log::write("Banka hesap bilgileri güncellendi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Hesap Bilgileri Güncellendi")->to("banka/hesap/duzenle/" . $this->request->input("banka_id") . "/" . $this->request->input("hesap_id"));
        } else {


            \Dipa\Io\Log::write("Hesap bilgileri güncellenemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Hesap Bilgileri Güncellenemedi!")->back();
        }
    }

    public function hareketKaydet() {


        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->bankaHesapHareketEkle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("Banka haraket kayıt", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Hesap Hareketi Eklendi")->to("banka/hesap/hareket/" . $this->request->input("banka_id"));
        } else {
            \Dipa\Io\Log::write("Banka haraket kayıt başarısız", self::$account_no, self::$userInfo["id"]);
            $this->header->result("fail", "Hesap Hareketi Eklenemedi!")->back();
        }
    }

    public function hareketKaydetAjax() {


        $bankaModel = $this->model("banka", "bankaModel");

        $result = $bankaModel->bankaHesapHareketEkleAjax($this->request);

        if ($result) {
            echo "ok";
        } else {

            echo "fail";
        }
    }

    public function hareketListele() {

        $model = $this->model("banka", "bankaModel");

        $hesaplar = $model->hesapOzetleri();


        $rapor = $model->hesapHareketleri($this->request);



        $hareketturleri = [
            0 => 'Hepsi',
            1 => 'Gelir',
            2 => 'Gider'
        ];
        return $this->view("banka/hareket-raporlari", [
                    'hesaplar' => $hesaplar,
                    'hareketturleri' => $hareketturleri,
                    'bas_tarih' => $this->request->input("bas_tarih"),
                    'bit_tarih' => $this->request->input("bit_tarih"),
                    'raporok' => 'ok',
                    'raporlar' => $rapor,
                    'stur' => $this->request->input("stur"),
                    'shesap' => $this->request->input("shesap"),
        ]);
    }


    public function hesapListesiAl(){

        $model = $this->model("banka", "bankaModel");

        $hesaplar = $model->butunhesapListesi();


        if($hesaplar){

            echo json_encode([
                "status" => 1,
                "hesaplar"=>$hesaplar

            ]);

        }else{

            echo json_encode([
                "status" => 0
            ]);
        }

    }

}
