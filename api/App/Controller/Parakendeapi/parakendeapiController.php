<?php

namespace App\Controller\Parakendeapi;

use \Dipa\Api\Apicontrol;

class parakendeapiController extends \Dipa\Controller {

    private $api;

    public function __construct() {

        parent::__construct(false);

        $this->api = new Apicontrol(self::$http_request);
    }

    public function getLastId() {

        $owner_id = self::$http_request->input("owner_id");

        $model = $this->model("Parakendeapi", "parakendeModel");

        $result = $model->getId($owner_id);

        if ($result) {

            $this->api->result(1, $result);
        } else {

            $this->api->result(0, 0);
        }
      \Dipa\Io\Log::write("last id", self::$http_request->input("account_no"),self::$http_request->input("user_id"), "software");
        
    }

    public function getUpdatedProducts() {

        $model = $this->model("Parakendeapi", "parakendeModel");

        $owner_id = self::$http_request->input("owner_id");

        $productresult = $model->getUpdated(self::$http_request->input("last_id"), $owner_id);

        $productCategoriesResult = $model->getCategoriesUpdated(self::$http_request->input("last_id"), $owner_id);

        $result = $this->api->getDefaultResult(1);


        $result["products"] = $productresult;

        $result["categories"] = $productCategoriesResult;


        if (self::$http_request->input("device_type") == "adisyon") {

            $result["tags"] = $model->getTags($owner_id);
        }


        array_walk_recursive($result,function(&$item){$item=strval($item);});


        echo json_encode($result);
    }

    public function accountSearch() {

        if (self::$http_request->has("query")) {

            $model = $this->model("Parakendeapi", "parakendeModel");

            $query = self::$http_request->input("query");

            $owner = self::$http_request->input("owner");

            $cari = $model->cariArama($query, $owner);

            if ($cari) {

                $durum = 1;
            } else {

                $durum = 0;
            }


            $sonuc = $this->api->getDefaultResult(1);
            $sonuc['query'] = $query;
            $sonuc['operation_status'] = $durum;
            $sonuc['accounts'] = $cari;
            echo json_encode($sonuc);
        } else {

            echo json_encode($this->api->getDefaultResult(3));
        }
    }


    public function accountSearchWithPhone() {

        if (self::$http_request->has("phone")) {

            $model = $this->model("Parakendeapi", "parakendeModel");

            $query = self::$http_request->input("phone");

            $owner = self::$http_request->input("owner");

            $cari = $model->cariAramaTelefonile($query, $owner);

            if ($cari) {

                $durum = 1;
            } else {

                $durum = 0;
            }

            $sonuc = $this->api->getDefaultResult(1);
            $sonuc['query'] = $query;
            $sonuc['operation_status'] = $durum;
            $sonuc['account'] = $cari;

            echo json_encode($sonuc);
        } else {

            echo json_encode($this->api->getDefaultResult(3));
        }
    }

    public function newAccount() {

        $cari_adi_input = self::$http_request->input("cari_adi_input");
        $cari_yekili_input = self::$http_request->input("cari_yekili_input");
        $cari_telefon_input = self::$http_request->input("cari_telefon_input");
        $cari_mail_input = self::$http_request->input("cari_mail_input");
        $cari_unvan_input = self::$http_request->input("cari_unvan_input");
        $cari_vergino_input = self::$http_request->input("cari_vergino_input");
        $cari_vergi_daire_input = self::$http_request->input("cari_vergi_daire_input");
        $cari_fatura_adres_input = self::$http_request->input("cari_fatura_adres_input");

        if ($cari_adi_input != "null" &&
                $cari_yekili_input != "null" &&
                $cari_telefon_input != "null" &&
                $cari_unvan_input != "null" &&
                $cari_vergino_input != "null" &&
                $cari_vergi_daire_input != "null" &&
                $cari_fatura_adres_input != "null") {

            $model = $this->model("Parakendeapi", "parakendeModel");

            echo $model->cariEkle(self::$http_request);
        } else {

            echo "nullinput";
        }
    }

    public function getAccountInfo() {

        $model = $this->model("Parakendeapi", "parakendeModel");

        $cariresult = $model->getCari(self::$http_request);

        if ($cariresult) {

            $result = $this->api->getDefaultResult(1);

            $result["result"] = $cariresult;


            echo json_encode($result);
        } else {

            echo json_encode($this->api->getDefaultResult(0));
        }
        
   
    }

    public function getAccountInvoiceUpdateInfo() {

        $model = $this->model("Parakendeapi", "parakendeModel");

        $result = $model->faturaBilgileriniGuncelle(self::$http_request);

        if ($result) {

            $this->api->result(1, $result);
        } else {

            $this->api->result(0, $result);
        }
    }

    public function retailSaleComplete() {

        $model = $this->model("Parakendeapi", "parakendeModel");

        $wresult = $model->parakendeSatisCikis(self::$http_request, $this->api);

        if ($wresult) {

            $result = $this->api->getDefaultResult(1);

            if (is_array($wresult)) {

                $result["reinit"] = 1;
            }

            $result["result"] = 1;

            echo json_encode($result);
        } else {

            echo json_encode($this->api->getDefaultResult(0));
        }
    }

    public function getOfflineUser() {

        $model = $this->model("Parakendeapi", "parakendeModel");

        $result = $model->offlineKullaniciAl(self::$http_request);

        if ($result) {

            $sonuc = $this->api->getDefaultResult(1);
            $sonuc['user_status'] = 1;
            $sonuc['user_mail'] = $result["email"];
            $sonuc['name'] = $result["name"];
            $sonuc['surname'] = $result["surname"];
            $sonuc['id'] = $result["id"];
            $sonuc['admin'] = $result["admin"];
            echo json_encode($sonuc);
        } else {


            $sonuc = $this->api->getDefaultResult(1);

            $sonuc['user_status'] = 0;
            echo json_encode($sonuc);
        }
    }

    public function getTables() {


        $model = $this->model("Parakendeapi", "parakendeModel");

        $table_group = $model->masaKategorileriAl(self::$http_request);
        $tables = $model->masalariAl(self::$http_request);

        $sonuc = $this->api->getDefaultResult(1);
        $sonuc['table_group'] = $table_group;
        $sonuc['tables'] = $tables;

        echo json_encode($sonuc);
    }

    public function getExchangeRate() {

        $model = $this->model("Parakendeapi", "parakendeModel");
        $kurlar = $model->dovizKurlariAl(self::$http_request);
        $sonuc = $this->api->getDefaultResult(1);
        $sonuc['kurlar'] = $kurlar;

        echo json_encode($sonuc);
    }



    public function dbDeploy() {
        $db_json = self::$http_request->input("dbjson");

        $deployresult = true;


        $model = $this->model("Parakendeapi", "parakendeModel");
        
        
        $device_type= self::$http_request->input("device_type");


        $deployresult = $model->deployDb($db_json,$device_type);

        if ($deployresult) {

            $result = $this->api->getDefaultResult(1);

            $result["result"] = "deploy";
            $result["sql"] = $deployresult;

            echo json_encode($result);
            
        } else {
              $result = $this->api->getDefaultResult(0);
              $result["result"] = "no";

            echo json_encode($result);
        }
    }
    
        public function getBarcode() {

        $owner_id = self::$http_request->input("owner_id");
        $barcode_no = self::$http_request->input("barcode");

        $model = $this->model("Parakendeapi", "parakendeModel");

        $result = $model->getBarcode($barcode_no, $owner_id);

        if ($result) {

            $search_result = $this->api->getDefaultResult(0);
            $search_result["searchstatus"] = 2;
            $search_result['stock'] = [];


        } else {


            $apiserver_url = "https://avokadoyazilim.com/api/parakende-api/barcode-archive-search";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiserver_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $archive_result = curl_exec($ch);

            $archive_result = json_decode($archive_result , true);

            $search_result = $this->api->getDefaultResult(0);
            $search_result["searchstatus"] = 0;
            $search_result['stock'] = [];
        }

        echo json_encode($search_result);
    }


    public function getArchiveBarcode(){

        $barcode_no = self::$http_request->input("barcode");

        $model = $this->model("Parakendeapi", "parakendeModel");

        $result =  $model->getArchiveBarcode($barcode_no);


        if($result){
          echo  json_encode(["status"=>1 , "result"=>$result]);
        }else{
            echo  json_encode(["status"=>0]);

        }

    }


    
    public function addStok(){
        

        $model = $this->model("Parakendeapi", "parakendeModel");

        $result = $model->addStok(self::$http_request);
        
          if ($result) {

            $search_result = $this->api->getDefaultResult(1);
            $search_result["addstatus"] = 1;
      
        } else {

            $search_result = $this->api->getDefaultResult(0);
            $search_result["addstatus"] = 0;
         
        }

        echo json_encode($search_result);
        
    }

}
