<?php
namespace App\Controller\Bildirim;

class bildirimController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }
     public function index($tur = null) {

        $bildirimModel = $this->model("bildirim", "bildirimModel");

        $bildirimler = $bildirimModel->getBildirimler($tur);

        $url="bildirim/liste";

        if($tur != NULL){
            $url=$url."/".$tur;
        }




        return $this->view("bildirim/index",[
            'bildirimler'=>$bildirimler,
            'tur'=>$tur,
            'urladres'=>$url,
            'bugun'=>date("Y-m-d"),
            'saat'=>date("H:i", strtotime("+1 hour"))
        ]);

    }


    public function remove($id) {

        $bildirimModel = $this->model("bildirim", "bildirimModel");

        $sil = $bildirimModel->bildirimSil($id);


        if ($sil) {

            \Dipa\Io\Log::write("Bildirim Silindi id: {$id} ", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Bildirim İptal Edildi")->back();

        } else {

            \Dipa\Io\Log::write("Bildirim Silinemedi  id: {$id}", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Bildirim İptal Edilemedi!")->back();
        }

    }




    public function kontrol() {

        $bildirimModel = $this->model("bildirim", "bildirimModel");

        $ignore = $this->request->input("ignorelist");

        if($ignore == ""){

            $ignore = false;
        }

        $sonuc = $bildirimModel->kontrol($ignore);

        if($sonuc){
            echo 1;
        }else{
            echo 0;
        }


    }


    public function okunduyap(){


        $bildirimModel = $this->model("bildirim", "bildirimModel");


        $liste= $this->request->input("liste");

        $okundu = $bildirimModel->listeOkunduYap($liste);

        if($okundu){
            echo json_encode([
                "status"=>1
            ]);
        }else{
            echo json_encode(["status"=>0]);
        }



    }



    public function ekle(){


        $bildirimModel = $this->model("bildirim", "bildirimModel");


        $ekle = $bildirimModel->ekle(self::$http_request);


        if(self::$http_request == 2){



            if ($ekle) {

                \Dipa\Io\Log::write("Bildirim Eklendi id: {$ekle} ", self::$account_no, self::$userInfo["id"]);

             echo 1;

            } else {

                \Dipa\Io\Log::write("Bildirim Eklenemedi", self::$account_no, self::$userInfo["id"]);
                echo 2;
            }



        }else{


            if ($ekle) {

                \Dipa\Io\Log::write("Bildirim Eklendi id: {$ekle} ", self::$account_no, self::$userInfo["id"]);

                $this->header->result("success", "Bildirim Eklendi")->back();

            } else {

                \Dipa\Io\Log::write("Bildirim Eklenemedi", self::$account_no, self::$userInfo["id"]);

                $this->header->result("fail", "Bildirim Eklenemedi!")->back();
            }


        }




    }



    public function yenibildirimler() {

        $bildirimModel = $this->model("bildirim", "bildirimModel");

        $adet = $this->request->input("adet");

        $ignore = $this->request->input("ignorelist");

        if($ignore == ""){

            $ignore = false;
        }

        $bildirimler = $bildirimModel->yeniBildirimler($adet,$ignore);

        if($bildirimler){



            foreach ($bildirimler as $key => $val){

                $bildirimler[$key]["bildirim_mesaj"] = html_entity_decode($val["bildirim_mesaj"],ENT_QUOTES);

            }


            echo json_encode([
                "status"=>1,
                "bildirimler" => $bildirimler


            ]);
        }else{
           echo json_encode(["status"=>0]);
        }
    }



}