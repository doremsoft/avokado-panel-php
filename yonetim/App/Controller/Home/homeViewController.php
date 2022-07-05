<?php
namespace App\Controller\Home;
use \Dipa\Controller;

class homeViewController extends Controller
{
  
    
    public function __construct() {
        parent::__construct(true);
    }

    public function indexAction()
    {



        $model = $this->model("hesaplar", "hesaplarModel");


        $hesaplar = $model->getList();


        return $this->view("hesaplar/list", ['hesaplar' => $hesaplar]);
    }
    
    public function getInfo()
    {
        phpinfo();
    }
}