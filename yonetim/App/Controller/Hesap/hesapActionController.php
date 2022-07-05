<?php
namespace App\Controller\Hesap;



use Dipa\Http\Request;

class hesapActionController extends \Dipa\Controller
{

    private $conn = "";
    private $model = "";

    public function __construct()
    {

        $this->request = new Request();

        $this->model = $this->model("hesap","hesapModel");

        $this->conn = $this->model->getConnection();
    }


    public function detaylar(){

        $hesap = $this->helper(null,"hesap",
            [
                "conn" => $this->conn
            ]);

        echo json_encode($hesap->hesapDetaylari($this->request->input("account_no")));
    }

    public function kalanKredi(){


        $hesap = $this->helper(null,"hesap",
            [

                "conn" => $this->conn
            ]);

        if($hesap->hesap_durum() == true){

            echo json_encode(["login"=>1 , "kredi" => $hesap->krediTutari()]);

        }else{

            echo json_encode(["login"=>0 , "kredi" => 0]);
        }
    }

    public function siparisEkle(){


        $siparis_urun_kod = $this->request->input("urun_kod");

        $hesap = $this->helper(null,"hesap",
            [

                "conn" => $this->conn
            ]);

        if($hesap->hesap_durum() == true){



        }
    }


    public function paketler(){

        $hesap = $this->helper(null,"hesap",
            [
                "conn" => $this->conn
            ]);

        if($hesap->hesap_durum() == true){

            echo json_encode(["login"=>1 , "paketler" => $hesap->paketler()]);

        }else{
            echo json_encode(["login"=>0 , "paketler" => []]);
        }
    }

    public function paketListesi(){

        $hesap = $this->helper(null,"hesap",
            [
                "conn" => $this->conn
            ]);

        if($hesap->hesap_durum() == true){

            echo json_encode(["login"=>1 , "paketler" => $hesap->paketListesi()]);

        }else{
            echo json_encode(["login"=>0 , "paketler" => []]);
        }
    }

}