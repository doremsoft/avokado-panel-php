<?php
namespace App\Controller\Fis;

class fisViewController extends \Dipa\Controller
{
    public function __construct() {
        parent::__construct(true);
    }


    public function show($id,$type) {

        $fisModel = $this->model("fis", "fisViewModel");

        $fisOzet = $fisModel->getfis($id,$type);

        $fisKalemler = $fisModel->getfisKalemleri($id,$type);

        $sdurum = 0;

        $sonsilgun = date("Y-m-d h:i:s", strtotime("+7 days"));

        if($fisOzet["created_date"] < $sonsilgun){

            $sdurum = 1;

        }

        return $this->view("fis/fis-show", [
            'fisOzet' => $fisOzet,
            'kalemler'=>$fisKalemler,
            'type'=>$type,
            'sil_durum'=>$sdurum
        ]);
    }


    public function index() {



        $year = date("Y");

        $fisModel = $this->model("fis", "fisViewModel");

        $satislar = $fisModel->getSatisfislari();
        $alimlar = $fisModel->getAlimfislari();








        return $this->view("fis/fis-index",[

            'satislar' => json_encode($satislar),
            'alimlar' => json_encode($alimlar),
            'year'=>$year
        ]);
    }




    public function yenifis() {

        return $this->view("fis/fis-yeni");
    }


    public function giris() {

        $cariModel = $this->model("cari", "cariViewModel");

        $carilist = $cariModel->getCariList(2);



        return $this->view("fis/yeni-giris", ['cariler' => $carilist,'tur'=>2 , 'uri'=>"fis/giris/"]);

    }

    public function cikis() {

        $cariModel = $this->model("cari", "cariViewModel");

        $carilist = $cariModel->getCariList(1);


        return $this->view("fis/yeni-cikis", ['cariler' => $carilist,'tur'=>1 , 'uri'=>"fis/cikis/"]);

    }

    public function fisler(){

        $bas_tarih = date("Y-m-d");

        $bit_tarih = date("Y-m-d");
        $satislar = false;
        $satislarOzet = false;

        $type = 0;
        $fislar = [];


        if ($this->request != null) {

            if ($this->request->has("bas_tarih")) {
                $bas_tarih = $this->request->input("bas_tarih");
            }


            if ($this->request->has("type")) {
                $type = $this->request->input("type");
            }



            if ($this->request->has("bit_tarih")) {
                $bit_tarih = $this->request->input("bit_tarih");
            }
        }


        if ($this->request != null)  {

            $model = $this->model("fis", "fisViewModel");

            $fislar = $model->ikitariheGorefislar($bas_tarih,$bit_tarih,$type);

        }


        return $this->view("fis/fis-list", [

            'type'=>$type,
            'ozet' => $satislarOzet,
            'fislar' => $fislar,
            'bas_tarih' => $bas_tarih,
            'bit_tarih' => $bit_tarih
        ]);


    }
}