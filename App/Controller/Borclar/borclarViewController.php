<?php
namespace App\Controller\Borclar;

class borclarViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }


    public function borclarList() {

        $borclarModel = $this->model("borclar", "borclarModel");

        $borclarlist = $borclarModel->getBorclarList();

        return $this->view("borclar/borclar-list", ['borclar' => $borclarlist]);
    }
    
    
        public function guncelBorclarList() {

               $borclarModel = $this->model("borclar", "borclarModel");

        $borclarlist = $borclarModel->getGuncelBorclarList();

        return $this->view("borclar/borclar-guncel-list", ['borclar' => $borclarlist]);
    }

     

}