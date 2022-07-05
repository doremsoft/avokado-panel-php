<?php

namespace App\Controller\Evraklar;

class senetlerViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function ekle() {


        return $this->view("evraklar/yeni-senet", ['bugun' => date("Y-m-d")]);
    }

    public function senetler() {



        return $this->view("evraklar/senet-listesi", [
                    'bas_tarih' => date("Y-m-d"),
                    'bit_tarih' => date("Y-m-d", strtotime("+1 month")),
                    'action' => 'non',
                    's_evrak_tip' => 3,
                    's_odeme_durum' => 0
        ]);
    }

    public function islem($senet_id) {



        $model = $this->model("evraklar", "senetModel");

        $senet = $model->senetDetaylari($senet_id);


        $model = null;

        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();

        $model = null;



        $model = $this->model("banka", "bankaModel");

        $hesaplar = $model->hesapOzetleri();



        return $this->view("evraklar/senet-islem", [
            'kasalar' => $kasalar,
            'hesaplar'=>$hesaplar,
            'bugun' => date("Y-m-d"),
                    'senet_id' => $senet_id,
                    'senet' => $senet
        ]);
    }

}
