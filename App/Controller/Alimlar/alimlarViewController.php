<?php
namespace App\Controller\Alimlar;
use \Dipa\Controller;

class alimlarViewController extends Controller
{

   public function __construct() {
        parent::__construct(true);
    }

public function index(){


       return $this->view("alimlar/index");
}

    public function ikiTarihArasiAlimRaporu() {


        $bas_tarih = date("Y-m-d");

        $bit_tarih = date("Y-m-d");
               $satislar = false;
            $satislarOzet = false;
        
        $tur = "non";

        if ($this->request != null) {

            if ($this->request->has("bas_tarih")) {
                $bas_tarih = $this->request->input("bas_tarih");
            }
            
            
                if ($this->request->has("tur")) {
                $tur = $this->request->input("tur");
            }


            if ($this->request->has("bit_tarih")) {
                $bit_tarih = $this->request->input("bit_tarih");
            }
        }


        if ($this->request != null)  {

            $model = $this->model("alimlar", "alimlarModel");
            
            
            if($tur == "hbr"){
                
                
                $satislar = $model->ikitariheGoreAlimlar($bas_tarih,$bit_tarih);

                $satislarOzet = $model->ikitariheGoreAlimlarOzet($bas_tarih,$bit_tarih);
                
            }else{
                
                $satislar = $model->ikitariheGoreAlimlarUbr($bas_tarih,$bit_tarih);

                $satislarOzet = $model->ikitariheGoreAlimlarOzetUbr($bas_tarih,$bit_tarih);
                

            }

        }


        return $this->view("alimlar/iki-tarih-arasi-alim-raporu", [
            'tur'=>$tur,
                    'ozet' => $satislarOzet,
                    'alimlar' => $satislar,
                    'bas_tarih' => $bas_tarih,
                    'bit_tarih' => $bit_tarih
        ]);
    }

}