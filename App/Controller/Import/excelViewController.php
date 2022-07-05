<?php

namespace App\Controller\Import;

class excelViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function stokListesi() {

        $stokModel = $this->model("stok", "stokModel");
        $birimler = $stokModel->stokBirimleriAl();
         $dovizler = $stokModel->dovizleriAl();

        return $this->view("excel/stok-aktarma", [
                    'birimler' => $birimler,
                    'dovizler' => $dovizler
        ]);
    }


    public function stokAdetGuncelleme(){

        return $this->view("excel/stok-adet-guncelleme-index");
    }

    public function stokGuncellemeListesi() {


        return $this->view("excel/stok-guncelleme-index");
    }


}
