<?php
namespace App\Controller\Finansal;

class finansalActionController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }


      public function sabitOdemeKaydet() {

          $cariModel = $this->model("finansal", "finansalActionModel");

          $result = $cariModel->sabitOdemeEkle($this->request);

          if ($result) {

              \Dipa\Io\Log::write("Sabit Ödeme eklendi", self::$account_no, self::$userInfo["id"]);

              $this->header->result("success", "Sabit Ödeme eklendi")->to("finansal/aylik-sabit-odemeler");
          } else {

              \Dipa\Io\Log::write("sabit odeme eklenemedi", self::$account_no, self::$userInfo["id"]);

              $this->header->result("fail", "Sabit Ödeme Eklenemedi!")->back();
          }
    }

    public function sabitOdemeGuncelle() {

        $cariModel = $this->model("finansal", "finansalActionModel");

        $result = $cariModel->sabitOdemeGuncelle($this->request);

        if ($result) {

            \Dipa\Io\Log::write("Sabit Ödeme güncellendi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Sabit Ödeme güncellendi")->to("finansal/aylik-sabit-odemeler");
        } else {

            \Dipa\Io\Log::write("sabit odeme güncellendi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Sabit Ödeme güncellendi!")->back();
        }
    }


    public function aylikOdemeListesi(){

        $ay = self::$http_request->input("ay");
        $yil  =self::$http_request->input("yil");



        $finansalModel = $this->model("finansal", "finansalViewModel");

        $sabit_odemeler = $finansalModel->getAylikSabitOdemeler($ay,$yil);


        $aylar = [
            "01","02","03","04","05","06","07","08","09","10","11","12"
        ];

        $yillar = [
            "2019",
            "2020"
        ];


        return $this->view("finansal/aylik_odeme_listesi",
            ['bu_ay' => date("m-Y"),
                "aylar"=>$aylar,
                "yillar"=>$yillar,
                "mevcut_yil"=>$yil,
                "mevcut_ay"=>$ay,
                'sabit_odemeler' => $sabit_odemeler]);
    }

}