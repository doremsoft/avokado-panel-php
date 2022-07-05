<?php
namespace App\Controller\Iptal;

class iptalViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function satisEvrakIptalOnay($evrak_id) {
        
        
       $model =  $this->model("fatura","faturaViewModel");
       
       $result = $model->getFatura($evrak_id,"satis");

        return $this->view("iptal/satis-iptal",[
            'evrak'=>$result,
            'evrak_id'=>$evrak_id
        ]);
    }

    public function alimEvrakIptalOnay($evrak_id) {


        $model =  $this->model("fatura","faturaViewModel");

        $result = $model->getFatura($evrak_id,"alim");

        return $this->view("iptal/alim-iptal",[
            'evrak'=>$result,
            'evrak_id'=>$evrak_id
        ]);
    }


}