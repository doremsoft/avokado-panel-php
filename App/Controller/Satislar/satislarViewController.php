<?php

namespace App\Controller\Satislar;

use \Dipa\Controller;

class satislarViewController extends Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function index(){


        $model = $this->model("home", "homeModel");

        $duyurular = $model->duyulariAl();

        $satisRaporlar = $model->satisRaporlari();

        $sonyedigunSatisRaporlari = $model->sonyedigunSatisRaporlari();


        $gecen_hafta = date('Y-m-d', strtotime('-1 week'));

        $gecen_hafta_bugun = $sonyedigunSatisRaporlari[$gecen_hafta];

        $bugun_toplam_satis = $satisRaporlar["bugun"];



        if ($gecen_hafta_bugun == 0) {

            $a1 = intval($bugun_toplam_satis);
        } else if ($gecen_hafta_bugun > 0) {
            $a1 = (($bugun_toplam_satis - $gecen_hafta_bugun) / $gecen_hafta_bugun) * 100;
        }





        if ($a1 < 0) {

            $gecenebugun_durum = "dw";
        } else if ($a1 == 0) {
            $gecenebugun_durum = "-";
        } else {
            $gecenebugun_durum = "up";
        }

        $gecenebugun = [
            'yuzde' => number_format($a1, 2, '.', ''),
            'durum' => $gecenebugun_durum
        ];

        $bu_hafta_toplam_satis = 0;


        foreach ($sonyedigunSatisRaporlari as $key => $value) {

            if ($gecen_hafta != $key) {
                $bu_hafta_toplam_satis = $bu_hafta_toplam_satis + $value;
            }
        }







        $model2 = $this->model("satislar", "satislarModel");

        $satislar = $model2->satislar();


        $sonsatislar = $model2->sonSatislar();



        return $this->view("satislar/index", [
            'satislar' => json_encode($satislar),
            'satis_raporlari' => $satisRaporlar,
            'son7gun' => $sonyedigunSatisRaporlari,
            'gecenhaftayabugun' => $gecenebugun,
            'buhaftasatislar' => $bu_hafta_toplam_satis,
            'sonsatislar'=>$sonsatislar
        ]);
    }






    public function ikiTarihArasiSatisRaporu() {


        $bas_tarih = date("Y-m-d");

        $bit_tarih = date("Y-m-d");

        $bit_saat = "23:59:59";
        $bas_saat = "00:00:00";

        $satislar = false;
        $satislarOzet = false;

        $tur = "non";

        if ($this->request != null) {

            if ($this->request->has("bas_tarih")) {
                $bas_tarih = $this->request->input("bas_tarih");
            }

            if ($this->request->has("bas_saat")) {
                $bas_saat = $this->request->input("bas_saat");
            }


            if ($this->request->has("tur")) {


                    $tur = $this->request->input("tur");


            }


            if ($this->request->has("bit_tarih")) {
                $bit_tarih = $this->request->input("bit_tarih");
            }

            if ($this->request->has("bit_saat")) {
                $bit_saat = $this->request->input("bit_saat");
            }
        }


        if ($this->request != null) {

            $model = $this->model("satislar", "satislarModel");


            if ($tur == "hbr") {


                $satislar = $model->ikitariheGoreSatislar($bas_tarih, $bit_tarih , $bas_saat , $bit_saat);

                $satislarOzet = $model->ikitariheGoreSatislarOzet($bas_tarih, $bit_tarih, $bas_saat , $bit_saat);
            } else {

                $satislar = $model->ikitariheGoreSatislarUbr($bas_tarih, $bit_tarih, $bas_saat , $bit_saat);

                $satislarOzet = $model->ikitariheGoreSatislarOzetUbr($bas_tarih, $bit_tarih, $bas_saat , $bit_saat);
            }
        }


        return $this->view("satislar/iki-tarih-arasi-satis-raporu", [
                    'tur' => $tur,
                    'ozet' => $satislarOzet,
                    'satislar' => $satislar,
                    'bas_tarih' => $bas_tarih,
                    'bit_tarih' => $bit_tarih,
                    'bas_saat' => $bas_saat,
                    'bit_saat' => $bit_saat
        ]);
    }

    public function ikiTarihArasiSatisRaporuAnaliz() {


        $bas_tarih = date("Y-m-d");

        $bit_tarih = date("Y-m-d");

        $bit_saat = "23:59:59";
        $bas_saat = "00:00:00";

        $satislar = false;
        $satislarOzet = false;

        $tur = "non";

        if ($this->request != null) {

            if ($this->request->has("bas_tarih")) {
                $bas_tarih = $this->request->input("bas_tarih");
            }

            if ($this->request->has("bas_saat")) {
                $bas_saat = $this->request->input("bas_saat");
            }



            if ($this->request->has("bit_tarih")) {
                $bit_tarih = $this->request->input("bit_tarih");
            }

            if ($this->request->has("bit_saat")) {
                $bit_saat = $this->request->input("bit_saat");
            }
        }


        if ($this->request != null) {

            $model = $this->model("satislar", "satislarModel");



            $satislar = $model->ikitariheGoreSatislarAnaliz($bas_tarih, $bit_tarih , $bas_saat , $bit_saat);


        }


        return $this->view("satislar/iki-tarih-arasi-satis-raporu-analiz", [

            'satislar' => json_encode($satislar),
            'bas_tarih' => $bas_tarih,
            'bit_tarih' => $bit_tarih,
            'bas_saat' => $bas_saat,
            'bit_saat' => $bit_saat
        ]);
    }



}
