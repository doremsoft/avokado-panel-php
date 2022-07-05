<?php
namespace App\Controller\Fatura;

class faturaViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    
      public function show($id,$type) {

        $faturaModel = $this->model("fatura", "faturaViewModel");

        $faturaOzet = $faturaModel->getFatura($id,$type);
                
        $faturaKalemler = $faturaModel->getFaturaKalemleri($id,$type);
        
        $sdurum = 0;
        
        $sonsilgun = date("Y-m-d h:i:s", strtotime("+7 days"));
        
        if($faturaOzet["created_date"] < $sonsilgun){
            
            $sdurum = 1;
            
        }

        return $this->view("fatura/fatura-show", [
            'faturaOzet' => $faturaOzet,
            'kalemler'=>$faturaKalemler,
            'type'=>$type,
            'sil_durum'=>$sdurum
                ]);
    }


    public function index() {



        $year = date("Y");

        $faturaModel = $this->model("fatura", "faturaViewModel");

        $satislar = $faturaModel->getSatisFaturalari();
        $alimlar = $faturaModel->getAlimFaturalari();








        return $this->view("fatura/fatura-index",[

            'satislar' => json_encode($satislar),
            'alimlar' => json_encode($alimlar),
            'year'=>$year
        ]);
    }




    public function yeniFatura() {



        return $this->view("fatura/fatura-yeni");
    }


    public function faturalar(){

        $bas_tarih = date("Y-m-d");

        $bit_tarih = date("Y-m-d");
        $satislar = false;
        $satislarOzet = false;

        $type = 0;
        $faturalar = [];


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

            $model = $this->model("fatura", "faturaViewModel");

                $faturalar = $model->ikitariheGoreFaturalar($bas_tarih,$bit_tarih,$type);

        }


        return $this->view("fatura/fatura-list", [

            'type'=>$type,
            'ozet' => $satislarOzet,
            'faturalar' => $faturalar,
            'bas_tarih' => $bas_tarih,
            'bit_tarih' => $bit_tarih
        ]);


    }

}