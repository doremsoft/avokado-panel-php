<?php

namespace App\Controller\Home;

use \Dipa\Controller;
use \Dipa\Db\Dbsync;

class homeViewController extends Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function indexAction() {


        $model = $this->model("home", "homeModel");

        $duyurular = $model->duyulariAl();

        $satisRaporlar = $model->satisRaporlari();





        return $this->view("home/index", [
                    'duyurular' => $duyurular,
                    'satis_raporlari' => $satisRaporlar,
                    'hesap_detayi' => Controller::$account_details,
            'bugun'=>date("Y-m-d"),
            'saat'=>date("H:i", strtotime("+1 hour"))

        ]);
    }


    public function hesapDetaylari(){

        $model = $this->model("home", "homeModel");

        $satisRaporlar = $model->satisRaporlari();

        return $this->view("home/hesap_detaylari", [
            'satis_raporlari' => $satisRaporlar,
            'hesap_detayi' => Controller::$account_details

        ]);


    }

    public function media($goruntuleme_klasor){

    if($goruntuleme_klasor == "public"){

        $this->paket_kontrol(["depolama"],"depolama");

    }else  if($goruntuleme_klasor == "private"){

        $this->paket_kontrol(["depolama"],"depolama");

    }


        $media_kod = Controller::$account_details["media_key"];


        return $this->view("home/media", [
            'goruntuleme_klasor'=>$goruntuleme_klasor,
            'media_kod'=>md5($media_kod),
            'hesap_kod'=>self::$account_no
        ]);


    }

    public function getInfo() {
        phpinfo();
    }

    public function testAction() {



        echo "<img src='http://localhost/avokado/web/avokadopanel/image/deneme.png'/>";



    }

}
