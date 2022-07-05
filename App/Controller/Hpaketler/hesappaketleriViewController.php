<?php
namespace App\Controller\Hpaketler;

class hesappaketleriViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }


    public function goster($paket) {


        return $this->view("hpaketler/paket-goster");
    }
    


}