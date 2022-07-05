<?php

namespace App\Controller\Tahsilat;

class tahsilatViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function cariSec() {

        return $this->view("tahsilat/cari-sec");
    }

    public function add($cari_id) {
        
        
        $cariModel = $this->model("cari", "cariViewModel");

        $cari = $cariModel->getCari($cari_id);
        
        

        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();

        $model = null;

        $model = $this->model("banka", "bankaModel");

        $hesaplar = $model->hesapOzetleri();
        

        return $this->view("tahsilat/add", [
            'cari'=>$cari,
                    'kasalar' => $kasalar,
                    'hesaplar'=>$hesaplar,  
                    'bugun' => date("Y-m-d"),
                    'cari_id' => $cari_id
        ]);
    }

}
