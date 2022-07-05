<?php

namespace App\Controller\Kasa;

use \Dipa\Controller;

class kasaViewController extends Controller {

    public function __construct() {
        parent::__construct(true);
    }


    public function index() {


        return $this->view("kasa/index");
    }


    public function kasaIndex(){


        $model = $this->model("kasa", "kasalarModel");
        $kasa_dokum = $model->sonHaraketler();

       $kasalar =  $model->kasaListesi();

       $durumlar = [];

       if($kasalar){
           foreach ($kasalar as $key => $val){

               $veriler = $model->kasaDurumu($val["id"]);


               if(isset($veriler["kasa_adi"])){



                   $durumlar[] =[

                       "kasa_adi"=>$val["kasa_adi"],
                       "gelirler"=> $veriler["gelirler"],
                       "giderler"=> $veriler["giderler"]


                   ];



               }else{

                   $durumlar[] = [

                       "kasa_adi"=>$val["kasa_adi"],
                       "gelirler"=> 0,
                       "giderler"=>0


                   ];
               }




           }
       }


        return $this->view("kasa/kasa-index",[
            'sonharaketler'=>$kasa_dokum,
            'kasadurumlari'=>$durumlar
        ]);
    }

    public function kasalar() {

        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();


        return $this->view("kasa/kasalar", [
                    'kasalar' => $kasalar
        ]);
    }

    public function yeniKasaHaraket() {

        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();


        return $this->view("kasa/yeni-haraket", [
                    'kasalar' => json_encode($kasalar),
                    'bugun' => date("Y-m-d")
        ]);
    }

    public function kasaRaporlari() {

        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();


        return $this->view("kasa/kasa-raporu", [
                    'kasalar' => $kasalar,
                    'bugun' => date("Y-m-d")
        ]);
    }


    public function kasaIptalRaporlari(){


        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();


        return $this->view("kasa/kasa-iptal-raporu", [
            'kasalar' => $kasalar,
            'bugun' => date("Y-m-d")
        ]);



    }

    public function kasaIptalRaporlariGoster() {

        $bas_tarih = "";
        $bit_tarih = "";
        $kasa_id = "";
        $tip = "";

        if (self::$http_request != null) {
            $bas_tarih = self::$http_request->input("bas_tarih");
            $bit_tarih = self::$http_request->input("bit_tarih");
            $kasa_id = self::$http_request->input("kasa_id");
            $tip = self::$http_request->input("tip");
        }


        $tipler = [
            '0' => 'Hepsi',
            '1' => 'Yanlızca Tahsilatlar',
            '2' => 'Yanlızca Ödemeler'
        ];


        $paginate_count = 20;

        $model = $this->model("kasa", "kasalarModel");
        $kasalar = $model->kasaListesi();
        $kasa_dokum = $model->kasaIptalDokumuGoster($kasa_id, $tip, $bas_tarih, $bit_tarih, $paginate_count);

        return $this->view("kasa/kasa-iptal-raporu-goster", [
            'kasalar' => $kasalar,
            'bas_tarih' => $bas_tarih,
            'bit_tarih' => $bit_tarih,
            'kasa_id' => $kasa_id,
            'tip' => $tip,
            'tipler' => $tipler,
            'kasa_dokum' => $kasa_dokum,
            'paginate_ex' => [
                'bas_tarih' => $bas_tarih,
                'bit_tarih' => $bit_tarih,
                'kasa_id' => $kasa_id,
                'tip' => $tip,
            ]
        ]);
    }


    public function kasaRaporlariGoster() {

        $bas_tarih = "";
        $bit_tarih = "";
        $kasa_id = "";
        $tip = "";

        if (self::$http_request != null) {
            $bas_tarih = self::$http_request->input("bas_tarih");
            $bit_tarih = self::$http_request->input("bit_tarih");
            $kasa_id = self::$http_request->input("kasa_id");
            $tip = self::$http_request->input("tip");
        }


        $tipler = [
            '0' => 'Hepsi',
            '1' => 'Yanlızca Tahsilatlar',
            '2' => 'Yanlızca Ödemeler'
        ];


        $paginate_count = 20;

        $model = $this->model("kasa", "kasalarModel");
        $kasalar = $model->kasaListesi();
        $kasa_dokum = $model->kasaDokumuGoster($kasa_id, $tip, $bas_tarih, $bit_tarih, $paginate_count);

        return $this->view("kasa/kasa-raporu-goster", [
                    'kasalar' => $kasalar,
                    'bas_tarih' => $bas_tarih,
                    'bit_tarih' => $bit_tarih,
                    'kasa_id' => $kasa_id,
                    'tip' => $tip,
                    'tipler' => $tipler,
                    'kasa_dokum' => $kasa_dokum,
                    'paginate_ex' => [
                        'bas_tarih' => $bas_tarih,
                        'bit_tarih' => $bit_tarih,
                        'kasa_id' => $kasa_id,
                        'tip' => $tip,
                    ]
        ]);
    }

    public function kasaVirman() {

        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();


        return $this->view("kasa/kasa-virman", [
                    'kasalar' => $kasalar,
                    'bugun' => date("Y-m-d")
        ]);
    }

}
