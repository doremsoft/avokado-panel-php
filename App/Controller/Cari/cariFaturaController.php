<?php
namespace App\Controller\Cari;

class cariFaturaController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    
      public function edit($fatura_id,$cari_id) {

        $cariModel = $this->model("cari", "cariFaturaModel");

        $cari = $cariModel->getCariFaturaBilgisi($fatura_id);

        return $this->view("cari/cari-fatura-edit", ['cari' => $cari]);
    }
    
    
        public function update() {

        $cari_id = $this->request->input("cari_id");
        
        $fatura_id = $this->request->input("fatura_id");

        $cariModel = $this->model("cari", "cariFaturaModel");

        $result = $cariModel->cariFaturaUpdate($this->request,$fatura_id);

        if ($result) {

            $this->header->result("success", "Cari Fatura Bilgileri Güncellendi")->to("cari-fatura/edit/$fatura_id/$cari_id");
        } else {
            $this->header->result("fail", "Cari Fatura Bilgileri Güncellenemedi!")->back();
        }
    }
}