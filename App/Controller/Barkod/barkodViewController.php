<?php
namespace App\Controller\Barkod;

class barkodViewController extends \Dipa\Controller
{
public function __construct() {
        parent::__construct(true);
    }


    public function barkodlar() {

    

        return $this->view("barkod/barkod-list");
    }


}