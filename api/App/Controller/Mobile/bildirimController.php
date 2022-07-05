<?php

namespace App\Controller\Mobile;

use \Dipa\Api\Mobileapi;

class bildirimController extends \Dipa\Controller
{


    public function __construct()
    {

        parent::__construct(false);
    }


    public function kontrol(){

        echo json_encode(["login"=> 1 , "status" => 0, "kontrol"=>date("Y-m-d H:i:s")]);
        die();

        \Dipa\App::editConfig("db", "database",$this->request->input("dbname"));

        $model = $this->model("mobile", "mobileModel");

        if($model->bildirimKullaniciKontrol($this->request)){

            $zaman = date("Y-m-d H:i:s");

            $bildirimler = $model->bildirimKontrol($this->request,$zaman);

            if($bildirimler){

                echo json_encode(["login"=> 1 , "status" => 1 , "bildirimler" => $bildirimler , "kontrol"=>$zaman]);

            }else{

                echo json_encode(["login"=> 1 , "status" => 0, "kontrol"=>$zaman]);
            }

        }else{

            echo json_encode(["login"=> 0 , "status" => 0]);
        }

    }


    public function bildirimGoruldu(){

        \Dipa\App::editConfig("db", "database",$this->request->input("dbname"));

        $model = $this->model("mobile", "mobileModel");

        if($model->bildirimKullaniciKontrol($this->request)){

            $bildirim = $model->bildirimOkundu($this->request);

            if($bildirim){

                echo json_encode(["login"=> 1 , "status" => 1 ]);

            }else{

                echo json_encode(["login"=> 1 , "status" => 0]);
            }

        }else{

            echo json_encode(["login"=> 0 , "status" => 0]);
        }

    }

}