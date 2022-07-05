<?php
namespace App\Controller\Stok;

class stokDepolarViewController extends \Dipa\Controller
{

       public function __construct() {
        parent::__construct(true);
    }

    public function index() {

        $depolar = [];
        
        $stokDepolarModel = $this->model("stok", "stokDepolarModel");
        
        $depolar = $stokDepolarModel->stokDepolariAl();
        
        return $this->view("stok-depolar/stok-depolar-index", ['depolar' => $depolar]);
    }
    
        public function depoList() {

        $depolar = [];
        
        $stokDepolarModel = $this->model("stok", "stokDepolarModel");
        
        $depolar = $stokDepolarModel->stokDepolariAl();
        
        return $this->view("stok-depolar/stok-depolar-list", ['depolar' => $depolar]);
    }
    
    
    public function show($id){
        
              
        $stokDepoModel = $this->model("stok", "stokDepolarModel");
        
        $depo = $stokDepoModel->stokDepoAl($id);
        
        $stoklar = $stokDepoModel->deponunStoklari($id);
       
        
         return $this->view("stok-depolar/stok-depolar-show", [
             'stoklar'=>$stoklar,
             'depo' => $depo
                 ]);
        
        
        
    }
    

}