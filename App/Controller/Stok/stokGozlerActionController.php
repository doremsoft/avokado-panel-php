<?php
namespace App\Controller\Stok;

class stokGozlerActionController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function add() {

        return $this->view("stok/stok-add");
    }

    public function stokList() {

        $stokModel = $this->model("stok", "stokViewModel");

        $stoklist = $stokModel->getStokList();

        return $this->view("stok/stok-list", ['stokler' => $stoklist]);
    }
    
      public function show($id) {

        $stokModel = $this->model("stok", "stokViewModel");

        $stok = $stokModel->getStok($id);

        return $this->view("stok/stok-show", ['stok' => $stok]);
    }
    
     
      public function edit($id) {

        $stokModel = $this->model("stok", "stokViewModel");

        $stok = $stokModel->getStok($id);

        return $this->view("stok/stok-edit", ['stok' => $stok]);
    }

}