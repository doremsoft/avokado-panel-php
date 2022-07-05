<?php
namespace App\Controller\Marka;

class markaActionController extends \Dipa\Controller
{
 public function __construct() {
        parent::__construct(true);
    }

    public function append() {



        $markaModel = $this->model("marka", "markaModel");

        $result = $markaModel->addMarka($this->request);

        if ($result) {

               \Dipa\Io\Log::write("Yeni marka eklendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Marka Eklendi")->to("marka/list");
        } else {

               \Dipa\Io\Log::write("yeni marka eklenemedi", self::$account_no, self::$userInfo["id"]);


               
            $this->header->result("fail", "Marka Eklenemedi!")->back();
        }
    }

    public function update() {

        $markaModel = $this->model("marka", "markaModel");

        $result = $markaModel->markaGuncelle($this->request);
        
        
        if ($result) {

               \Dipa\Io\Log::write("marka güncellendi", self::$account_no, self::$userInfo["id"]);



            echo "ok";
        } else {
               \Dipa\Io\Log::write("marka güncellenemdi", self::$account_no, self::$userInfo["id"]);



            echo "fail";
        }
        
    }


    public function logoUpdate() {

        $markaModel = $this->model("marka", "markaModel");

        $result = $markaModel->markaResimlerGuncelle($this->request);

        \Dipa\Io\Log::write("marka resimler güncellendi", self::$account_no, self::$userInfo["id"]);


        $this->header->result("success", "marka resimleri güncellendi")->back();


    }


}
