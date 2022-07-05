<?php

namespace App\Controller\Stokrapor;

class stokRaporActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function stokHareketiGoster() {



        $stok_id = self::$http_request->input("stok_id");
        $sdepo = self::$http_request->input("sdepo");
        $bas_tarih = self::$http_request->input("bas_tarih");
        $bit_tarih = self::$http_request->input("bit_tarih");
        $stur = self::$http_request->input("stur");
   


        $model = $this->model("stokrapor","stokRaporModel");

        $rapor = $model->detayliStokHaraketGoster($stok_id,$sdepo,$bas_tarih,$bit_tarih,$stur);


        $model2 = $this->model("stok", "stokHaraketModel");
        $depolar = $model2->depolariGetir();
        $haraketTur = [1 => "Hepsi", 2 => "Giriş", 3 => "Çıkış"];
   

        return $this->view("stok-raporlar/stok_hareketi", [
                    'depolar' => $depolar,
                    'hareketturleri' => $haraketTur,
                    'stok_id' => $stok_id,
                    'bas_tarih' => $bas_tarih,
                    'bit_tarih' => $bit_tarih,
                    'raporok' => 'ok',
                    'sdepo'=>$sdepo,
                    'stur' =>$stur,
           
                    'raporlar'=>$rapor
        ]);
    }

}
