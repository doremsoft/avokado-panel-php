<?php
namespace App\Controller\Parametreler;

class parametrelerViewController extends \Dipa\Controller
{

    
    public function __construct() {
        parent::__construct(true);
    }

    public function index()
    {
        
 
        return $this->view("parametreler/index");
    }
    
}