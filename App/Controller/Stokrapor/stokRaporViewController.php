<?php

namespace App\Controller\Stokrapor;

class stokRaporViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function stokHareketi($stok_id) {

        
        

        $model = $this->model("stok", "stokHaraketModel");
        $depolar = $model->depolariGetir();
        $haraketTur = [
            1 => "Hepsi",
            2 => "Giriş",
            3 => "Çıkış"
        ];



        return $this->view("stok-raporlar/stok_hareketi", [
                    'depolar' => $depolar,
                    'hareketturleri' => $haraketTur,
                    'stok_id' => $stok_id,
                    'bas_tarih' => date("Y-m-d", strtotime("-7 day", strtotime(date("Y-m-d")))),
                    'bit_tarih' => date("Y-m-d"),
                    'raporok' => 'no'
        ]);
    }

}
