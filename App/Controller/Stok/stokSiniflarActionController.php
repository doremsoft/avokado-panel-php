<?php
namespace App\Controller\Stok;

class stokSiniflarActionController extends \Dipa\Controller
{
 public function __construct() {
        parent::__construct(true);
    }

    public function save() {

        $stokSiniflarModel = $this->model("stok", "stokSiniflarModel");

        $result = $stokSiniflarModel->stokSinifEkle($this->request);

        if ($result) {

            $this->header->result("success", "Stok Sinif Eklendi")->to("stok-siniflar");
        } else {

            $this->header->result("fail", "Stok Sinif Eklenemedi!")->back();
        }
    }

    public function update() {

        $stokSiniflarModel = $this->model("stok", "stokSiniflarModel");

        $result = $stokSiniflarModel->stokSinifGuncelle($this->request);

        if ($result) {

            echo "ok";
        } else {

            echo "fail";
        }
    }

    public function remove() {

        $stokSiniflarModel = $this->model("stok", "stokSiniflarModel");

        $result = $stokSiniflarModel->stokSinifSil($this->request);

         if ($result) {

            $this->header->result("success", "Stok Sinif Silindi")->to("stok-siniflar");
        } else {

            $this->header->result("fail", "Stok Sinif Silinemedi!")->back();
        }
    }


}