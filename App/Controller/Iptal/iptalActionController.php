<?php

namespace App\Controller\Iptal;

class iptalActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function satisEvrak() {

        $model = $this->model("iptal", "satisFaturaIptalModel");

        $result = $model->satisIptal($this->request->input("evrak_id"), $this->request->input("iptal_not"));

        if ($result) {

                  \Dipa\Io\Log::write("Satış evrakı iptal edildi:" . $this->request->input("evrak_id"), self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Satış Evrak İptal Edildi")->to("iptal/satis-evrak-iptal/" . $this->request->input("evrak_id"));
       
            } else {

                  \Dipa\Io\Log::write("Satış evrakı iptal edilemedi:".$model->getErrorMsj(), self::$account_no, self::$userInfo["id"]);
                  
                  
            $this->header->result("fail", "Satış Evrak İptal Hatası! Mesaj:".$model->getErrorMsj())->to("iptal/satis-evrak-iptal/" . $this->request->input("evrak_id"));
        }
    }


    public function alimEvrak() {

        $model = $this->model("iptal", "alimIptalModel");

        $result = $model->alimIptal(
            $this->request->input("evrak_id"),
            $this->request->input("iptal_not"));

        if ($result) {

            \Dipa\Io\Log::write("Alım evrakı iptal edildi:" . $this->request->input("evrak_id"), self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Alım Evrak İptal Edildi")->to("iptal/alim-evrak-iptal/" . $this->request->input("evrak_id"));

        } else {

            \Dipa\Io\Log::write("Alım evrakı iptal edilemedi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("fail", "Alım Evrak İptal Hatası! ")->to("iptal/alim-evrak-iptal/" . $this->request->input("evrak_id"));
        }
    }

}
