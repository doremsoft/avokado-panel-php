<?php
namespace App\Controller\Bildirim;

class bildirimController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function add() {

        return $this->view("bildirim/add",[
            "bugun"=> date("Y-m-d"),
            "saat"=> date("H:i:s")
        ]);
    }

    public function send() {

        $bildirimModel = $this->model("bildirim", "bildirimModel");

        $bildirimlist = $bildirimModel->topluBildirimGonder($this->request);


    }
    


}