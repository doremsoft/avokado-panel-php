<?php
namespace App\Controller\Test;

class testViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function deploy() {

        return $this->view("test/test-deploy");
    }
    
    public function deployOk(){
        
           $db_json = self::$http_request->input("dbjson");
           
          $model = $this->model("test","testModel");
          
          
          $model->deployDb($db_json);
          
          
        
    }


}