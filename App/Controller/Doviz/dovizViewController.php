<?php

namespace App\Controller\Doviz;

class dovizViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function dovizList() {

        $dovizModel = $this->model("doviz", "dovizModel");

        $dovizlist = $dovizModel->dovizleriAl();

        return $this->view("doviz/doviz-list", ['dovizler' => $dovizlist]);
    }

    public function kurlariCek() {


        $dovizModel = $this->model("doviz", "dovizModel");

        $result = $dovizModel->kurlariGuncelle();

        if ($result) {

            \Dipa\Io\Log::write("Doviz kurlari guncellendi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Kurlar Güncellendi")->to("doviz/list");
        } else {
            \Dipa\Io\Log::write("Doviz kurlari guncellenemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Kurlar Güncellenemedi!")->to("doviz/list");
        }
    }

    public function kurGuncelle() {


        $dovizModel = $this->model("doviz", "dovizModel");

        $result = $dovizModel->kurGuncelle($this->request->input("id"), $this->request->input("value"));

        if ($result) {

            echo "ok";
        } else {

            echo "non";
        }
    }

}
