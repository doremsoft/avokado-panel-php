<?php
namespace App\Controller\Masa;

class masaController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }
    
    
    
        public function masaIslem($cat_id) {

        $masaModel = $this->model("masa", "masaModel");

        $masalist = $masaModel->getMasaListWithCatIs($cat_id);

        return $this->view("masa/masalar", [
            'kat_id'=>$cat_id,
            'masalar' => $masalist]);
    }
    
    
    
    
    

    public function masaList() {

        $masaModel = $this->model("masa", "masaModel");

        $masalist = $masaModel->getMasaList();

        return $this->view("masa/masa-list", ['masaler' => $masalist]);
    }
    
    
   public function masaKategoriList(){
       
         $masaModel = $this->model("masa", "masaModel");

        $masalist = $masaModel->getGrupMasaList();

        return $this->view("masa/masa-grup-liste", ['masa_kategoriler' => $masalist]);
        
       
   }
    
    public function masaKategoriler(){

        $masaModel = $this->model("masa", "masaModel");

        $masalist = $masaModel->getGrupMasaList();

        return $this->view("masa/masa-kategoriler", ['masa_kategoriler' => $masalist]);
        
    }
    

}