<?php
namespace App\Controller\Stok;

class stokGruplarViewController extends \Dipa\Controller
{

       public function __construct() {
        parent::__construct(true);
    }

    public function index() {

        $birimler = [];
        
        $stokGruplarModel = $this->model("stok", "stokGruplarModel");
        
        $gruplar = $stokGruplarModel->stokGruplariAl();
        
        return $this->view("stok-gruplar/stok-gruplar-index", ['gruplar' => $gruplar]);
    }

}