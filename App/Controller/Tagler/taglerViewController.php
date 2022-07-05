<?php
namespace App\Controller\Tagler;

class taglerViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function tagList() {
        
        
       $model =  $this->model("tagler","taglerModel");
       
       $tagler = $model->getTagler();

        return $this->view("tagler/tag-list",[
            'etiketler'=>$tagler
        ]);
    }

}