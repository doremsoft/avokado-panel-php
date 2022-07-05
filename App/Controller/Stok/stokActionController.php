<?php

namespace App\Controller\Stok;

class stokActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function save() {
        $stokModel = $this->model("stok", "stokActionModel");

        $noframe = "";
        
        if(self::$http_request->input("noframe") == "ok"){
            
            $noframe = "?noframe=ok";
            
        }

        if (trim(self::$http_request->input("stok_adi")) != "") {

            if (self::$http_request->input("stok_barkod_no") == null ||
                    trim(self::$http_request->input("stok_barkod_no")) == "" ||
                    empty(trim(self::$http_request->input("stok_barkod_no")))) {

                $result = $stokModel->addStok($this->request);

                if ($result) {

                    \Dipa\Io\Log::write("Stok eklendi", self::$account_no, self::$userInfo["id"]);


                    $this->header->result("success", "Stok Eklendi")->to("stok/add".$noframe);
                } else {
                    \Dipa\Io\Log::write("Stok eklenemedi", self::$account_no, self::$userInfo["id"]);

                    $this->header->result("fail", "Stok Hesap Eklenemedi!")->back();
                }
            } else {

                if ($stokModel->stokBarkodKontrol(self::$http_request->input("stok_barkod_no"))) {

                    \Dipa\Io\Log::write("stok eklenemdi barkod mevcut", self::$account_no, self::$userInfo["id"]);


                    $this->header->result("fail", "Barkod Numarası " . self::$http_request->input("stok_barkod_no") . " Mevcut!")->back();
                } else {

                    $result = $stokModel->addStok($this->request);

                    if ($result) {
                        \Dipa\Io\Log::write("stok eklendi", self::$account_no, self::$userInfo["id"]);



                        if ($this->request->input("page") != NULL) {


                            $this->header->result("success", self::$http_request->input("stok_adi") . " Stok Eklendi")->to("stok/fastadd".$noframe);
                        } else {

                            $this->header->result("success", self::$http_request->input("stok_adi") . " Stok Eklendi")->to("stok/add".$noframe);
                        }
                    } else {

                        \Dipa\Io\Log::write("stok eklenemedi", self::$account_no, self::$userInfo["id"]);


                        $this->header->result("fail", "Stok Hesap Eklenemedi!")->back();
                    }
                }
            }
        } else {

            $this->header->result("fail", "Stok Adı Boş Bırakılamaz!")->back();
        }
    }

    public function update() {
        $stokModel = $this->model("stok", "stokActionModel");


        if (trim(self::$http_request->input("stok_adi")) != "") {

            if (self::$http_request->input("stok_barkod_no") == null || trim(self::$http_request->input("stok_barkod_no")) == "" ||
                    empty(trim(self::$http_request->input("stok_barkod_no")))) {
                $result = $stokModel->updateStok($this->request);

                if ($result) {

                    \Dipa\Io\Log::write("stok güncellendi", self::$account_no, self::$userInfo["id"]);


                   $this->header->result("success", "Stok Güncellendi.. Barkod:".self::$http_request->input("stok_barkod_no"))->back();
                } else {
                    \Dipa\Io\Log::write("stok güncellenemedi", self::$account_no, self::$userInfo["id"]);



                    $this->header->result("fail", "Stok Güncellenemdi!")->back();
                }
            } else {

                if ($stokModel->stokGuncellemeBarkodKontrol(self::$http_request->input("stok_barkod_no"), self::$http_request->input("stok_id"))) {

                    \Dipa\Io\Log::write("stok güncellenemedi barkod mevcut", self::$account_no, self::$userInfo["id"]);


                    $this->header->result("fail", "Barkod Numarası " . self::$http_request->input("stok_barkod_no") . " Mevcut!")->back();
                } else {

                    $result = $stokModel->updateStok($this->request);

                    if ($result) {

                        \Dipa\Io\Log::write("stok güncellendi", self::$account_no, self::$userInfo["id"]);


                        $this->header->result("success", "Stok Güncellendi. Barkod:".self::$http_request->input("stok_barkod_no"))->back();
                    } else {
                        \Dipa\Io\Log::write("stok güncellenemedi", self::$account_no, self::$userInfo["id"]);


                        $this->header->result("fail", "Stok Güncellenemdi!")->back();
                    }
                }
            }
        } else {

            $this->header->result("fail", "Stok Adı Boş Bırakılamaz!")->back();
        }
    }

    public function fastPriceUpdate() {


        $stokModel = $this->model("stok", "stokActionModel");


        if (trim(self::$http_request->input("stok_satis_fiyati")) != "") {


            $result = $stokModel->updateStokPrice($this->request);

            if ($result) {
                
               \Dipa\Io\Log::write("stok fiyatları güncellendi", self::$account_no, self::$userInfo["id"]);


               

                $this->header->result("success", self::$http_request->input("stok_ad") . " Stok Fiyatları Güncellendi")->to("stok/fast-price-update");
            } else {
                
                     
               \Dipa\Io\Log::write("stok fiyatları güncellenemedi", self::$account_no, self::$userInfo["id"]);


               
                $this->header->result("success", self::$http_request->input("stok_ad") . " Stok Fiyatları Güncellenemedi!")->to("stok/fast-price-update");
            }
        } else {

            $this->header->result("fail", "Stok Fiyatı Boş Bırakılamaz!")->back();
        }
    }

    public function stokGetirSeriNoIle() {


        if ($this->request->input("serino")) {

            $stokModel = $this->model("stok", "stokModel");


            $stok = $stokModel->stokSeriIleGetir($this->request->input("serino"));

            if ($stok) {
                $durum = "ok";

                $stok["stok_standart_adet"] = (float)  $stok["stok_standart_adet"];

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

    public function stokGetir() {


        if ($this->request->input("kod")) {

            $stokModel = $this->model("stok", "stokModel");


            $stok = $stokModel->stokKodIleGetir($this->request->input("kod"));

            if ($stok) {
                $durum = "ok";
                $stok["stok_standart_adet"] = (float)  $stok["stok_standart_adet"];
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

    public function stokGetirIsımle() {


        if ($this->request->has("qr")) {

            $stokModel = $this->model("stok", "stokModel");

            $query = $this->request->input("qr");


            $ust_stoklar = 0;

            if($this->request->has("ust")){


                $ust_stoklar = $this->request->input("ust");

            }


            $stok = $stokModel->stokIsımIleGetir($query,$ust_stoklar);

            if ($stok) {

                $durum = "ok";
            } else {

                $durum = "non";
            }


            $sonuc = [
                'query' => $query,
                'durum' => $durum,
                'stok' => $stok
            ];

            echo json_encode($sonuc);
        } else {
            echo "non";
        }
    }

    public function stokArama() {


        if ($this->request->has("query")) {

            $stokModel = $this->model("stok", "stokActionModel");

            $query = $this->request->input("query");

            $mevcutta = 0;

            if ($this->request->has("mevcutta")) {
                $mevcutta= $this->request->input("mevcutta");
            }

            $stok = $stokModel->stokArama($query,$mevcutta);

            if ($stok) {

                $durum = "ok";
            } else {

                $durum = "non";
            }


            $sonuc = [
                'fullsearch' => 'ok',
                'query' => $query,
                'durum' => $durum,
                'stok' => $stok
            ];

            echo json_encode($sonuc);
        } else {
            echo "non";
        }
    }

    public function stokGetirIsımleDepoya() {


        if ($this->request->has("query")) {

            $stokModel = $this->model("stok", "stokModel");

            $query = $this->request->input("query");


            $stok = $stokModel->stokIsımIleGetirDepoIcın($query);

            if ($stok) {

                $durum = "ok";
            } else {

                $durum = "non";
            }


            $sonuc = [
                'query' => $query,
                'durum' => $durum,
                'stok' => $stok
            ];

            echo json_encode($sonuc);
        } else {
            echo "non";
        }
    }

    public function removeok() {

        $stok_id = $this->request->input("stok_id");

        $stokHareketModel = $this->model("stok", "stokHaraketModel");

        $kontrol_durum = $stokHareketModel->stokHareketKontrol($stok_id);

        if ($kontrol_durum == 1) {


            $stokModel = $this->model("stok", "stokModel");


            if ($stokModel->remove($stok_id)) {
     \Dipa\Io\Log::write("stok silindi", self::$account_no, self::$userInfo["id"]);


                $this->header->result("success", "Stok Silindi")->to("stok/list");
            } else {
                
                  \Dipa\Io\Log::write("stok silinemedi", self::$account_no, self::$userInfo["id"]);


                $this->header->result("fail", "Stok Silinemedi!")->back();
            }
        } else {

            $this->header->result("fail", "Stok Hareketi Yüzünden Silinemiyor!")->back();
        }
    }

    public function fastPriceUpdateStock() {

        $stok_barkod_no = $this->request->input("stok_barkod_no");

        $stokHareketModel = $this->model("stok", "stokModel");

        $kontrol_durum = $stokHareketModel->stokKodIleGetir($stok_barkod_no);

        if ($kontrol_durum) {

            return $this->view("stok/fast-update-price-stok", [
                        'stok' => $kontrol_durum]);
        } else {

            $this->header->result("fail", "Stok Bulunamadı: " . $stok_barkod_no)->back();
        }
    }



    public function stokOyVer() {

        $stokModel = $this->model("stok", "stokModel");

        $oy_durum = $stokModel->stokOyKaydet($this->request);

        if ($oy_durum) {
            echo  "ok";
        } else {

            echo  "non";
        }
    }

}
