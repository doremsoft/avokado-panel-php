<?php

Class hesap
{

    private $conn;
    private $hesap_no;
    private $secure;
    private $login_durum = false;

    public function __construct($args = [])
    {


        $headers = getallheaders();



        if(isset($headers["Account-No"])){
            $this->hesap_no= $headers["Account-No"];

        }
        if(isset($headers["Account-Hash"])){

            $this->secure = $headers["Account-Hash"];
        }



        $this->conn = $args["conn"];

        $this->hesapKontrol();

    }

    private function hesapKontrol(){

        $query = $this->conn->prepare("SELECT id FROM account_data WHERE account_no = ? and secure_key = ? and remove = 0");

        $query->execute([
            $this->hesap_no,
            $this->secure
        ]);

        if($query->fetch()){

            $this->login_durum = true;

        }

    }

    public function hesap_durum(){

       return $this->login_durum;
    }

    public function krediTutari(){

        $cikis = 0;
        $giris = 0;

        $query = $this->conn->prepare(""
            . "SELECT sum(kredi) as giris_toplam "
            . "FROM hesap_kredileri "
            . "WHERE remove = 0 and hareket_turu = ? and hesap_no = ?");

        $query->execute(["giris",$this->hesap_no]);

        $giris_tutari_result = $query->fetch();

        if($giris_tutari_result){

            $giris = $giris_tutari_result["giris_toplam"];

        }

        $cquery = $this->conn->prepare(""
            . "SELECT sum(kredi) as cikis_toplam "
            . "FROM hesap_kredileri "
            . "WHERE remove = 0 and hareket_turu = ? and hesap_no = ?");

        $cquery->execute(["cikis",$this->hesap_no]);

        $cikis_tutari_result = $cquery->fetch();

        if($cikis_tutari_result){

            $cikis = $cikis_tutari_result["cikis_toplam"];

        }


        return $giris - $cikis;
    }


    public function paketler(){

        $now = date("Y-m-d H:i:s");

        $sql="SELECT 
*
FROM hesap_paketleri 
WHERE 
hesap_no = ? and paket_baslama_tarihi <= ? and paket_bitis_tarihi >= ? and aktif = 1";

        $query = $this->conn->prepare($sql);

        $query->execute([$this->hesap_no,$now,$now]);

        return $query->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);

    }


    public function paketListesi(){

        $now = date("Y-m-d H:i:s");

        $sql="SELECT * 

FROM hesap_paketleri 
WHERE 
hesap_no = ? and paket_baslama_tarihi <= ? and paket_bitis_tarihi >= ? and aktif = 1";

        $query = $this->conn->prepare($sql);

        $query->execute([$this->hesap_no,$now,$now]);

        return $query->fetchAll(PDO::FETCH_ASSOC);

    }

    public function hesapDetaylari($account_no){

        $masterquery = $this->conn->prepare("SELECT * FROM account_data WHERE account_no = ? and remove = ?");

        $masterquery->execute([$account_no, 0]);

       return $masterquery->fetch(PDO::FETCH_ASSOC);

    }


}