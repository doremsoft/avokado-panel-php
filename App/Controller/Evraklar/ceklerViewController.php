<?php
namespace App\Controller\Evraklar;

class ceklerViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    
    public function cekEkle(){

        return $this->view("evraklar/yeni-cek");
    }
    

}