<?php

namespace App\Controller\Tagler;

class taglerActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function append() {



        $tagModel = $this->model("tagler", "taglerModel");

        $result = $tagModel->addTag($this->request);

        if ($result) {


            \Dipa\Io\Log::write("yeni etiket eklendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Etiket Eklendi")->to("tag/list");
        } else {

            \Dipa\Io\Log::write("yeni etiket eklenemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Etiket Eklenemedi!")->back();
        }
    }

    public function update() {

        $tagModel = $this->model("tagler", "taglerModel");

        $result = $tagModel->tagGuncelle($this->request);


        if ($result) {
            \Dipa\Io\Log::write("etiket güncellendi", self::$account_no, self::$userInfo["id"]);

            echo "ok";
        } else {
            \Dipa\Io\Log::write("etiket güncellenemedi", self::$account_no, self::$userInfo["id"]);

            echo "fail";
        }
    }

}
