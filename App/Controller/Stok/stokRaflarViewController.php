<?php
namespace App\Controller\Stok;

class stokRaflarViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function index() {

        $depolar = [];
        $raflar = [];
        
        $stokDepolarModel = $this->model("stok", "stokDepolarModel");
        $depolar = $stokDepolarModel->stokDepolariAl();
        
        $stokRaflarModel = $this->model("stok", "stokRaflarModel");
        $raflar = $stokRaflarModel->stokRaflariAl();
        
        return $this->view("stok-raflar/stok-raflar-index", [
            'depolar' => $depolar,
            'raflar'=>$raflar
                ]);
    
    }

}