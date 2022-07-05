<?php
namespace App\Controller\Log;

class logViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function add() {

        return $this->view("log/log-add");
    }



}