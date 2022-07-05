<?php
namespace App\Controller\Download;

class downloadViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function add() {

        return $this->view("download/download-add");
    }

    public function downloadList() {

        return $this->view("download/download-list");
    }
    
 

}