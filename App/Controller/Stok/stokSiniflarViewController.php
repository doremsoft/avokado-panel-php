<?php
namespace App\Controller\Stok;

class stokSiniflarViewController extends \Dipa\Controller
{

  public function __construct() {
        parent::__construct(true);
    }

    public function index() {

        $siniflar = [];
        
        $stokSiniflarModel = $this->model("stok", "stokSiniflarModel");
        
        $siniflar = $stokSiniflarModel->stokSiniflariAl();
        
        return $this->view("stok-siniflar/stok-siniflar-index", ['siniflar' => $siniflar]);
    }

}