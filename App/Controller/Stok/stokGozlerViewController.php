<?php

namespace App\Controller\Stok;

class stokGozlerViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function index() {
        $raflar = [];
        $gozler = [];
        
        $stokRaflarModel = $this->model("stok", "stokRaflarModel");
        $raflar = $stokRaflarModel->stokRaflariAl();

        $stokGozlerModel = $this->model("stok", "stokGozlerModel");
        $gozler = $stokGozlerModel->stokGozleriAl();

        return $this->view("stok-gozler/stok-gozler-index", [
                    'gozler' => $gozler,
                    'raflar' => $raflar
        ]);
    }

}
