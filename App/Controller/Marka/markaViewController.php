<?php
namespace App\Controller\Marka;

class markaViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }
    public function markaList() {
        
        
       $model =  $this->model("marka","markaModel");
       
       $markalar = $model->getMarkalar();

        return $this->view("marka/marka-list",[
            'markalar'=>$markalar
        ]);
    }

    public function markaLogoList() {


        $model =  $this->model("marka","markaModel");

        $markalar = $model->getMarkalar();

        return $this->view("marka/marka-logo-list",[
            'markalar'=>$markalar
        ]);
    }



}
