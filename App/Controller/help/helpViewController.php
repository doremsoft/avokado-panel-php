<?php
namespace App\Controller\Help;

class helpViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function message() {

        return $this->view("help/message");
    }

   

}