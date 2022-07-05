<?php
namespace App\Controller\Alacaklar;

class alacaklarViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }


    public function alacaklarList() {

        $alacaklarModel = $this->model("alacaklar", "alacaklarModel");

        $alacaklarlist = $alacaklarModel->getAlacaklarList();
        return $this->view("alacaklar/alacaklar-list", ['alacaklar' => $alacaklarlist]);
    }
    
    
    

    public function guncelAlacaklarList() {

        $alacaklarModel = $this->model("alacaklar", "alacaklarModel");

        $alacaklarlist = $alacaklarModel->getGuncelAlacaklarList();
      
       return $this->view("alacaklar/alacaklar-guncel-list", ['alacaklar' => $alacaklarlist]);
    }

}