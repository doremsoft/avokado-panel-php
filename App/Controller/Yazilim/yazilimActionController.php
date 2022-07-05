<?php

namespace App\Controller\Yazilim;

class yazilimActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function update() {

        $model = $this->model("yazilim", "yazilimModel");


        if (trim(self::$http_request->input("takma_adi")) != "") {


            $result = $model->guncelle($this->request);

            if ($result) {

                   \Dipa\Io\Log::write("yazılım ayarları güncellendi", self::$account_no, self::$userInfo["id"]);


                   
                $this->header->result("success", "Bilgiler Güncellendi")->back();
            } else {
                
                    \Dipa\Io\Log::write("yazılım ayarları güncellenemedi", self::$account_no, self::$userInfo["id"]);


                    
                $this->header->result("fail", "Bilgiler Güncellenemdi!")->back();
            }
        } else {

            $this->header->result("fail", "Bütün Alan ve Seçimleri Gerçekleştirin!")->back();
        }
    }

}
