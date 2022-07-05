<?php
namespace App\Controller\Program;

class programViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function barkod() {

        $model = $this->model("yazilim","yazilimModel");

        $yazilimlar = $model->getYazilimList("barkod");

        return $this->view("program/barkod",['yazilimlar'=>$yazilimlar]);
    }



}