<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class bildirimModel extends Dimodel
{


    public function ekle($request){


    return  $this->bildirimEkle(
        new Controller(),
        "Hatırlatma!",
        $request->input("mesaj"), 0,
        $request->input("tarih"),  $request->input("saat"),
        Controller::$userInfo["id"],  0,"alarm");

}


    public function bildirimSil($id){


        return $this->table("bildirimler", Controller::$userInfo)->find($id)->remove_();

    }


    public function getBildirimler($tur = null){
        $bugun = date("Y-m-d");

        if($tur == null){


            return $this->table("bildirimler", Controller::$userInfo)
                ->orderBy(" ORDER BY zaman ASC ")
                ->where([
                    "tarih"=>["=",$bugun],
                    "user_id"=>["=",Controller::$userInfo["id"]]
                ])
                ->paginate(Controller::$http_request, 20);

        }else if($tur == 1){

            return $this->table("bildirimler", Controller::$userInfo)
                ->orderBy(" ORDER BY zaman ASC ")
                ->where([
                    "user_id"=>["=",Controller::$userInfo["id"]]
                ])
                ->paginate(Controller::$http_request, 20);



        }else  if($tur == 2){



            return $this->table("bildirimler", Controller::$userInfo)
                ->orderBy(" ORDER BY zaman ASC ")
                ->where([
                    "tarih"=>["<",$bugun],
                    "user_id"=>["=",Controller::$userInfo["id"]]
                ])
                ->paginate(Controller::$http_request, 20);

        }else  if($tur == 3){



            return $this->table("bildirimler", Controller::$userInfo)
                ->orderBy(" ORDER BY zaman ASC ")
                ->where([
                    "tarih"=>[">",$bugun],
                    "user_id"=>["=",Controller::$userInfo["id"]]
                ])
                ->paginate(Controller::$http_request, 20);

        }else  if($tur == 4){



            return $this->table("bildirimler", Controller::$userInfo)
                ->orderBy(" ORDER BY zaman ASC ")
                ->where([
                    "goruntuleme"=>["=",1],
                    "user_id"=>["=",Controller::$userInfo["id"]]
                ])
                ->paginate(Controller::$http_request, 20);

        }else  if($tur == 5){

            return $this->table("bildirimler", Controller::$userInfo)
                ->orderBy(" ORDER BY zaman ASC ")
                ->where([
                    "goruntuleme"=>["=",0],
                    "user_id"=>["=",Controller::$userInfo["id"]]
                ])
                ->paginate(Controller::$http_request, 20);

        }else  if($tur == 6){

            return $this->table("bildirimler", Controller::$userInfo)
                ->disableDefault()
                ->orderBy(" ORDER BY zaman ASC ")
                ->where([
                    "remove"=>["=",1],
                    "user_id"=>["=",Controller::$userInfo["id"]]
                ])
                ->paginate(Controller::$http_request, 20);

        }



    }



    public function kontrol($notIn = false)
    {
        if($notIn == false){

            $sql = "SELECT id FROM bildirimler WHERE owner_id = ? and user_id = ? and goruntuleme = ? and zaman <= ? and remove = 0 ";
            $query = $this->getConnection()->prepare($sql);
            $bugun = date("Y-m-d H:i:s");
            $query->execute([Controller::$userInfo["owner_id"], Controller::$userInfo["id"], 0, $bugun]);
            return $query->fetch();

        }else{

            $sql = "SELECT id FROM bildirimler WHERE owner_id = ? and user_id = ? and goruntuleme = ? and zaman <= ?  and remove = 0 and  id NOT IN ({$notIn}) ";

            $query = $this->getConnection()->prepare($sql);
            $bugun = date("Y-m-d H:i:s");
            $query->execute([Controller::$userInfo["owner_id"], Controller::$userInfo["id"], 0, $bugun]);
            return $query->fetch();
        }

    }


    public function yeniBildirimler($adet = 5 , $notIn = false)
    {


        if($notIn == false){

            $sql = "SELECT bildirimler.* ,    UNIX_TIMESTAMP(created_date) AS epoch_time  FROM bildirimler WHERE owner_id = ? and user_id = ? and goruntuleme = ?  and remove = 0 and tarih <= ?  ";
            $query = $this->getConnection()->prepare($sql);
            $bugun = date("Y-m-d");
            $query->execute([Controller::$userInfo["owner_id"], Controller::$userInfo["id"], 0, $bugun]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;



        }else{

            $sql = "SELECT bildirimler.* ,    UNIX_TIMESTAMP(created_date) AS epoch_time  FROM bildirimler WHERE id NOT IN ({$notIn}) and owner_id = ? and user_id = ? and  goruntuleme = ?  and remove = 0 and tarih <= ? ";
            $query = $this->getConnection()->prepare($sql);
            $bugun = date("Y-m-d");
            $query->execute([Controller::$userInfo["owner_id"], Controller::$userInfo["id"], 0, $bugun]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;



        }
    }

    public function listeOkunduYap($idlistestring)
    {
        $idliste = explode(",", $idlistestring);

        $update_sql = "";

        foreach ($idliste as $id) {

            $update_sql .= "UPDATE bildirimler SET goruntuleme = 1 WHERE id = {$id};";
        }

        $updatequery = $this->getConnection()->prepare($update_sql);

        return $updatequery->execute();
    }



    public function butunBildirimler(){

        $sql = "SELECT * FROM bildirimler WHERE owner_id = ? and user_id = ?  and remove = 0 ORDER BY id DESC LIMIT 20 ";
        $query = $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"], Controller::$userInfo["id"]]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    public function sonOkunanBildirimler()
    {

            $sql = "SELECT * FROM bildirimler WHERE owner_id = ? and user_id = ? and  goruntuleme = ?  and remove = 0 ORDER BY id DESC LIMIT 20 ";
            $query = $this->getConnection()->prepare($sql);
            $bugun = date("Y-m-d");
            $query->execute([Controller::$userInfo["owner_id"], Controller::$userInfo["id"], 1]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;


    }


}
