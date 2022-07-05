<?php

namespace App\Controller\Mobile;

use \Dipa\Api\Mobileapi;
use Dipa\Support\Curl;

class mobileController extends \Dipa\Controller {

    private $api;

    public function __construct() {

        parent::__construct(false);

        header('Content-Type: application/json');

        $this->api = new Mobileapi(self::$http_request);
    }

    public function getStok() {

        $model = $this->model("mobile", "mobileModel");

        $gruplar = $model->stokGruplariAl($this->request);

        $sresult = $model->stokAl($this->request);

        $dovizler = $model->dovizKurlariAl($this->request);


        if ($sresult) {

            $result = $this->api->getDefaultResult(1);

            $result["stok_status"] = 1;
            $result["satis_fiyat"] = $sresult["stok_satis_fiyati"];
            $result["kdv_oran"] = $sresult["stok_kdv_oran"];
            $result["stok_adi"] = $sresult["stok_adi"];

            if (!isset($sresult["stok_doviz"])) {

                $sresult["stok_doviz"] = "TL";

            }else{

                $sresult["stok_doviz"] = strtoupper($sresult["stok_doviz"]);

            }



            $result["doviz_kur"] = 1;

            if(isset($dovizler[$sresult["stok_doviz"]])){

                $result["doviz_kur"] = $dovizler[$sresult["stok_doviz"]];
            }

            $result["stok_doviz"] = $sresult["stok_doviz"];
            $result["stok_adet"] = $sresult["stok_adet"];
            $result["detay"] = $sresult;
            $result["stok_id"] = $sresult["stok_id"];
            $result["stok_gruplar"] = $gruplar;


            echo json_encode($result);
        } else {

            $result = $this->api->getDefaultResult(0);
            $result["stok_status"] = 0;
            $result["stok_gruplar"] = $gruplar;

            echo json_encode($result);
        }
    }

    public function stokFastPriceUpdate() {

        $model = $this->model("mobile", "mobileModel");

        $sresult = $model->stokHizliFiyatGuncelle($this->request);

        if ($sresult) {

            $result = $this->api->getDefaultResult(1);

            $result["update_status"] = 1;

            echo json_encode($result);
        } else {

            $result = $this->api->getDefaultResult(0);

            $result["update_status"] = 0;

            echo json_encode($result);
        }
    }

    public function stokUpdate() {

        $model = $this->model("mobile", "mobileModel");

        $sresult = $model->stokGuncelle($this->request);

        if ($sresult) {

            $result = $this->api->getDefaultResult(1);

            $result["update_status"] = 1;

            echo json_encode($result);
        } else {

            $result = $this->api->getDefaultResult(0);

            $result["update_status"] = 0;

            echo json_encode($result);
        }
    }

    public function stokAdd() {

        $model = $this->model("mobile", "mobileModel");

        $sresult = $model->stokEkle($this->request);

        if ($sresult) {

            $result = $this->api->getDefaultResult(1);

            $result["insert_status"] = 1;

            echo json_encode($result);
        } else {

            $result = $this->api->getDefaultResult(0);

            $result["insert_status"] = 0;

            echo json_encode($result);
        }
    }

    public function getAllStocks() {

        $model = $this->model("mobile", "mobileModel");

        $sresult = $model->butunStoklariAl($this->request);

        if ($sresult) {

            $result = $this->api->getDefaultResult(1);

            $result["select_status"] = 1;

             $result["stocks"] = $sresult;

            echo json_encode($result);
        } else {

            $result = $this->api->getDefaultResult(0);

            $result["select_status"] = 0;

            echo json_encode($result);
        }
    }

    public function importStockCount(){

        $model = $this->model("mobile", "mobileModel");

        $sresult = $model->sayimlariAktar($this->request);

        if ($sresult) {

            $result = $this->api->getDefaultResult(1);

            $result["import_status"] = 1;


            echo json_encode($result);
        } else {

            $result = $this->api->getDefaultResult(0);

            $result["import_status"] = 0;

            echo json_encode($result);
        }


    }

    public function stokSearch(){


        $model = $this->model("mobile", "mobileModel");

        $sresult = $model->stokAra($this->request);

        if ($sresult) {

            $result = $this->api->getDefaultResult(1);

            $result["search_status"] = 1;


            $result["stok_list"] = $sresult;


            echo json_encode($result);

        } else {

            $result = $this->api->getDefaultResult(0);

            $result["search_status"] = 0;

            echo json_encode($result);
        }

}

public function abonelikPaketleri(){

    $info = $this->api->getAccountInfo();

    $account_no = $info["account_id"];

    $secure = $info["secure_key"];

    $res = Curl::post(\Dipa\App::getConfig("manager_url")."/hesap/paket-liste",[],   $account_no , $secure,true);

    $sresult = json_decode($res,true);

    if ($sresult) {

        if($sresult["login"] == 1){

            $result = $this->api->getDefaultResult(1);

            $result["paketler"] = $sresult["paketler"];

            echo json_encode($result);

        }else{
            $result = $this->api->getDefaultResult(0);

            $result["remote_login"] = 0;

            echo json_encode($result);

        }

    } else {

        $result = $this->api->getDefaultResult(0);

        echo json_encode($result);
    }

}

    public function krediTutari(){

        $info = $this->api->getAccountInfo();

        $account_no = $info["account_id"];

        $secure = $info["secure_key"];

        $res = Curl::post(\Dipa\App::getConfig("manager_url")."/hesap/kalan-kredi",[],   $account_no , $secure,true);

        $sresult = json_decode($res,true);

        if ($sresult) {

            if($sresult["login"] == 1){

                $result = $this->api->getDefaultResult(1);

                $result["kredi"] = $sresult["kredi"];

                echo json_encode($result);

            }else{
                $result = $this->api->getDefaultResult(0);

                $result["remote_login"] = 0;

                echo json_encode($result);

            }

        }else{

            $result = $this->api->getDefaultResult(0);

            echo json_encode($result);

        }
    }


    public function siparisEkle(){

        $info = $this->api->getAccountInfo();

        $account_no = $info["account_id"];
        $secure = $info["secure_key"];




        $urun_adi = $this->request->input("urun_adi");
        $urun_kod = $this->request->input("urun_kod");
        $urun_tutar = $this->request->input("urun_tutar");
        $siparis_no = $this->request->input("siparis_no");
        $odeme_kod =  $this->request->input("odeme_kod");

        $res = Curl::post(\Dipa\App::getConfig("manager_url")."/hesap/siparis-ekle",[
            "urun_adi"=>$urun_adi,
            "urun_kod"=>$urun_kod,
            "urun_tutar"=>$urun_tutar,
            "siparis_no"=>$siparis_no,
            "odeme_kod"=> $odeme_kod
        ], $account_no , $secure,true);

        $sresult = json_decode($res,true);

        if ($sresult) {

            if($sresult["login"] == 1){

                $result = $this->api->getDefaultResult(1);

                $result["siparis_result"] = $sresult["siparis_result"];

                echo json_encode($result);

            }else{

                $result = $this->api->getDefaultResult(0);

                $result["remote_login"] = 0;

                echo json_encode($result);

            }

        }else{

            $result = $this->api->getDefaultResult(0);

            echo json_encode($result);

        }
    }


}
