<?php
namespace App\Controller\Finansal;
use \Dipa\Controller;
class finansalViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function add() {

        return $this->view("finansal/finansal-add");
    }



    public function odemeIndex() {

        $finansalModel = $this->model("finansal", "finansalViewModel");

        $odemeler = $finansalModel->getSonOdemeler();


        return $this->view("odeme/index",[
            'odemeler'=>$odemeler
        ]);
    }

public function odemeListe(){


        $baslama = date("Y-m-d");
        $bitis = date("Y-m-d");

$tip = "hepsi";


        if(isset($_POST["bas_tarih"])){

            $baslama = $_POST["bas_tarih"];


        }


    if(isset($_POST["tip"])){

        $tip = $_POST["tip"];


    }

    if(isset($_POST["bit_tarih"])){

        $bitis = $_POST["bit_tarih"];


    }

        $tipler=[
            'hepsi'=> 'Hepsi',
            'kasanakit'=> 'Kasa Nakit',
            'banka'=> 'Banka'

        ];


    $finansalModel = $this->model("finansal", "finansalViewModel");

    $odemeler = $finansalModel->getOdemeler($tip,$baslama,$bitis);

        return $this->view("odeme/listeleme",[
            'tipler'=>$tipler,
            'bas_tarih'=>$baslama,
            'bit_tarih'=>$bitis,
            'tip'=>$tip,
            'odemeler'=>$odemeler

        ]);
}



    public function tahsilatIndex() {

        $finansalModel = $this->model("finansal", "finansalViewModel");

        $tahsilatlar = $finansalModel->getSonTahsilatlar();


        return $this->view("tahsilat/index",[
          'tahsilatlar'=>$tahsilatlar
        ]);
    }

    public function tahsilatListe(){


        $baslama = date("Y-m-d");
        $bitis = date("Y-m-d");

        $tip = "hepsi";


        if(isset($_POST["bas_tarih"])){

            $baslama = $_POST["bas_tarih"];


        }


        if(isset($_POST["tip"])){

            $tip = $_POST["tip"];


        }

        if(isset($_POST["bit_tarih"])){

            $bitis = $_POST["bit_tarih"];


        }

        $tipler=[

            'hepsi'=> 'Hepsi',
            'kasanakit'=> 'Kasa Nakit',
            'banka'=> 'Banka'

        ];


        $finansalModel = $this->model("finansal", "finansalViewModel");

        $tahsilatlar= $finansalModel->getTahsilatlar($tip,$baslama,$bitis);

        return $this->view("tahsilat/listeleme",[
            'tipler'=>$tipler,
            'bas_tarih'=>$baslama,
            'bit_tarih'=>$bitis,
            'tip'=>$tip,
            'tahsilatlar'=>$tahsilatlar

        ]);
    }


    public function finansalOzet() {


        $model = $this->model("page", "homeModel");

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






        return $this->view("finansal/ozet", [

            'satis_raporlari' => $satisRaporlar,

            'son7gun' => $sonyedigunSatisRaporlari,
            'gecenhaftayabugun' => $gecenebugun,
            'buhaftasatislar' => $bu_hafta_toplam_satis
        ]);
    }



    public function finansalList() {

        $finansalModel = $this->model("finansal", "finansalViewModel");

        $finansallist = $finansalModel->getFinansalList();

        return $this->view("finansal/finansal-list", ['finansaller' => $finansallist]);
    }


    
      public function show($id) {

        $finansalModel = $this->model("finansal", "finansalViewModel");

        $finansal = $finansalModel->getFinansal($id);

        return $this->view("finansal/finansal-show", ['finansal' => $finansal]);
    }
    
     
      public function edit($id) {

        $finansalModel = $this->model("finansal", "finansalViewModel");

        $finansal = $finansalModel->getFinansal($id);

        return $this->view("finansal/finansal-edit", ['finansal' => $finansal]);
    }

    public function sabitOdemeler(){
        $finansalModel = $this->model("finansal", "finansalViewModel");

        $sabit_odemeler = $finansalModel->getSabitOdemeler();

        return $this->view("finansal/sabit_odemeler", ['sabit_odemeler' => $sabit_odemeler]);

    }

    public function sabitOdemeCariSecim(){

        return $this->view("finansal/sabit_odeme_hesap_sec");

    }

    public function yeniSabitOdeme($cari_id){

        $cariModel = $this->model("cari", "cariViewModel");
        $cari = $cariModel->getCari($cari_id);

        $gunler = [
          1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30
        ];

        return $this->view("finansal/yeni_sabit_odeme", [
            'cari'=>$cari,
            'gunler' => $gunler,
            'bugun'=>date("Y-m-d"),
            'seneyebugun'=>date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " + 1 year"))

        ]);


    }



    public function sabitOdemeDuzenle($odeme_id){



        $finansalModel = $this->model("finansal", "finansalViewModel");

        $sabit_odeme= $finansalModel->getSabitOdeme($odeme_id);


        $gunler = [
            1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30
        ];

        return $this->view("finansal/sabit_odeme_duzenle", [
            'gunler' => $gunler,
            'odeme'=>$sabit_odeme

        ]);


    }

    public function aylikOdemeListesi(){

        $finansalModel = $this->model("finansal", "finansalViewModel");

        $sabit_odemeler = $finansalModel->getAylikSabitOdemeler(date("m"),date("Y"));



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
                "mevcut_yil"=>date("Y"),
                "mevcut_ay"=>date("m"),
                'sabit_odemeler' => $sabit_odemeler]);
    }

    public function gelirGiderOzet(){


        $finansalModel = $this->model("finansal", "finansalViewModel");

        $gelir_yillik = $finansalModel->yillik_gelirler();

        $gider_yillik =$finansalModel->yillik_giderler();

        $satislarModel = $this->model("satislar", "satislarModel");

        $satislar = $satislarModel->satislar();


        $alimlar = $finansalModel->getAlimlar();




        $aylar = array(

            1=>"Ocak",

            2=>"Şubat",

            3=>"Mart",

            4=>"Nisan",

            5=>"Mayıs",

            6=>"Haziran",

            7=>"Temmuz",

            8=>"Ağustos",

            9=>"Eylül",

            10=>"Ekim",

            11=>"Kasım",

            12=>"Aralık"

        );

        $yil = date("Y");
        $ay = intval(date("m"));
        $buayisim = $aylar[$ay];

        return $this->view("finansal/gelir_gider", [

            'gelir_yillik'=>json_encode($gelir_yillik),
            'gider_yillik'=>json_encode($gider_yillik),
            'satislar'=>json_encode($satislar),
            'alimlar'=>json_encode($alimlar),
            'buyil'=>$yil,
            'buay'=>$ay,
            'buayisim'=>$buayisim

        ]);

    }

}