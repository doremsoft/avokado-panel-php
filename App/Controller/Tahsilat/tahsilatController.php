<?php
namespace App\Controller\Tahsilat;

class tahsilatController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function tahsilatEkle() {

        return $this->view("tahsilat/tahsilat-add");
    }


}