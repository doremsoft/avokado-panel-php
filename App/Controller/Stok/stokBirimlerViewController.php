<?php

namespace App\Controller\Stok;

class stokBirimlerViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function index() {

        $birimler = [];
        
        $stokBirimlerModel = $this->model("stok", "stokBirimlerModel");
        
        $birimler = $stokBirimlerModel->stokBirimleriAl();
        
        return $this->view("stok-birimler/stok-birimler-index", ['birimler' => $birimler]);
    }

}
