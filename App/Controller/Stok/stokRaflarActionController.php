<?php
namespace App\Controller\Stok;

class stokRaflarActionController extends \Dipa\Controller
{

  public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $stokRaflarModel = $this->model("stok", "stokRaflarModel");

        $result = $stokRaflarModel->stokRafEkle($this->request);

        if ($result) {

            $this->header->result("success", "Stok Raf Eklendi")->to("stok-raflar");
        } else {

            $this->header->result("fail", "Stok Raf Eklenemedi!")->back();
        }
    }

    public function update() {

        $stokRaflarModel = $this->model("stok", "stokRaflarModel");

        $result = $stokRaflarModel->stokRafGuncelle($this->request);

        if ($result) {

            echo "ok";
        } else {

            echo "fail";
        }
    }

    public function remove() {

        $stokRaflarModel = $this->model("stok", "stokRaflarModel");

        $result = $stokRaflarModel->stokRafSil($this->request);

         if ($result) {

            $this->header->result("success", "Stok Raf Silindi")->to("stok-raflar");
        } else {

            $this->header->result("fail", "Stok Raf Silinemedi!")->back();
        }
    }
    
    public function getir(){
        
        
             if($this->request->input("depo")){
            
            $stokModel = $this->model("stok", "stokRaflarModel");
            
            
            $stok = $stokModel->deponunRaflari($this->request->input("depo"));
            
            if ($stok){
                $durum = "ok";
            }else{
                
                $durum = "non";
            }
            
            
            $sonuc = [
                
                'durum'=>$durum,
                'raflar'=>$stok
                
            ];
            
            echo json_encode($sonuc);
            
        }else{
            echo "non";
        }
           
    }

}