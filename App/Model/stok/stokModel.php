<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class stokModel extends Dimodel {


    public function stokBirimleriAl() {

        return $this->reset()->table("stok_birimler", Controller::$userInfo)->getAll();
    }

    public function toplamStokSayisi(){

        $sql3 = "SELECT count(id) as toplam FROM stok WHERE  remove = 0 and owner_id = ?";

        $query3 = $this->getConnection()->prepare($sql3);

        $query3->execute([Controller::$userInfo["owner_id"]]);

        $toplam_stoklar = $query3->fetch(PDO::FETCH_ASSOC);

        if ($toplam_stoklar['toplam']) {

            $toplam_stok = $toplam_stoklar['toplam'];
        } else {

            $toplam_stok = 0;
        }

        return $toplam_stok;


    }

    public function dovizleriAl(){

         return $this->table("doviz", Controller::$userInfo)->getAll();

    }

    public function etiketleriAl(){
            return $this->table("tagler", Controller::$userInfo)->getAll();

    }


    public function stokEtiketleriAl($stok_id){
            return $this->table("stok_etiketler", Controller::$userInfo)->select("tag_id")->where(["stok_id"=>["=",$stok_id]])->getAll();

    }


    public function markalariAl(){
        return $this->table("markalar", Controller::$userInfo)->orderBy(" ORDER BY marka_adi")->getAll();
    }



    public function getStokList($filtre = "") {


        if($filtre == "son-eklenen"){


          $result =   $this->table("stok", Controller::$userInfo)
                ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)])
              ->disableDefault()
              ->orderBy(" ORDER BY stok.id  DESC LIMIT 0 , 20")
              ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
              ->where([
                  "stok.remove" => ["=", "0"],
                  "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]]
              ])
           ->getAll();

          return [
              'result'=>$result
          ];


        }else  if($filtre == "kritik"){

//onemli

            return $this->table("stok", Controller::$userInfo)
                ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)])
                ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->disableDefault()
                ->where([
                    "stok.stok_min_seviyesi" => ["!=", "0"],
                    "stok.stok_min_seviyesi " => [">", "stok.stok_adet", true],
                    "stok.remove" => ["=", "0"],
                    "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]]
                ])
                ->paginate(Controller::$http_request, 10);

        }else  if($filtre == "onemli"){



            return $this->table("stok", Controller::$userInfo)
                ->disableDefault()
                ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)])
                ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->where([
                    "stok.remove" => ["=", "0"],
                    "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]],
                    "stok.onemli" => ["=", "1"]

                ])

                ->paginate(Controller::$http_request, 10);

        }else  if($filtre == "silinen"){



            return $this->table("stok", Controller::$userInfo)

                ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)])
                ->disableDefault()
                ->where([
                    "stok.remove" => ["=", "1"],
                    "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]]

                ])
                ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->paginate(Controller::$http_request, 10);

        }else{


              return  $this->table("stok", Controller::$userInfo)
                ->disableDefault()
                ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)])
                ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                  ->where([
                      "stok.remove" => ["=", "0"],
                      "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]]

                  ])
                  ->paginate(Controller::$http_request, 10);

        }




    }

    public function getKiritikStokList() {

        return $this->table("stok", Controller::$userInfo)
            ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)])
            ->disableDefault()
            ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                        ->where([
                            "stok.stok_min_seviyesi" => ["!=", "0"],
                            "stok.stok_min_seviyesi " => [">", "stok.stok_adet", true],
                            "stok.remove" => ["=", "0"],
                            "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]]

                        ])
                        ->paginate(Controller::$http_request, 10);
    }

    public function getWebStokList($page,$ex) {


        $harf = $ex["harf"];

        $tip = $ex["tip"];

        $where = [
            "stok.remove" => ["=", "0"],
            "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]]

        ];


        if($tip == "web"){

            $where["stok.web_status"] = ["=",1];
        }


        if($harf == "" || $harf == "all"){

            return $this->table("stok", Controller::$userInfo)
                ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)." , stok.web_status "])
                ->disableDefault()
                ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->where($where)
                ->paginate(Controller::$http_request, 10);



        }else{
            return $this->table("stok", Controller::$userInfo)
                ->select([Controller::helper(null,"stokModelHelper")->getStokList(true)." , stok.web_status "])
                ->disableDefault()
                ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->where($where)
                ->like(" stok.stok_adi  LIKE '".$harf."%'")
                ->paginate(Controller::$http_request, 10);



        }


    }

    public function getId() {

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 ORDER BY last_id DESC LIMIT 1 ");

        $query->execute();

        $q_last_id = $query->fetch();

        if ($q_last_id) {
            $last_id = $q_last_id["last_id"];

            return $last_id;
        } else {
            return 0;
        }
    }

    public function getUpdated($last_id) {

        $query = $this->getConnection()->prepare(""
                . "SELECT stok.* ,('0.0') as stok_alis_fiyati,('0.0') as stok_adet,stok_birimler.stok_birim_adi "
                . "FROM stok "
                . "INNER JOIN stok_birimler ON stok.stok_birimi = stok_birimler.id "
                . "WHERE stok.remove = 0 AND stok.last_val > ? ");

        $query->execute([$last_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesUpdated($last_id) {


        $query = $this->getConnection()->prepare(""
                . "SELECT * "
                . "FROM stok_gruplar "
                . "WHERE remove = 0 AND last_val > ? ");

        $query->execute([$last_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStok($id) {


        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(Controller::helper(null,"stokModelHelper")->getStok($id,$owner_id));

        $query->execute();

        return $query->fetch();


    }

    public function getStokFromN11($id) {


        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(Controller::helper(null,"stokModelHelper")->getN11Stok($id,$owner_id));

        $query->execute();

        return $query->fetch();


    }

    public function remove($id) {

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"];

        $last_id++;

        $this->table("stok", Controller::$userInfo)->find($id)->col("last_val",$last_id)->update_();

        $remove_result = $this->table("stok", Controller::$userInfo)->find($id)->remove_();

        $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);

        return $remove_result;

    }

    public function stokGruplariAl() {

        return $this->reset()->table("stok_gruplar", Controller::$userInfo)->getAll();
    }

    public function stokSiniflariAl() {

        return $this->reset()->table("stok_siniflar", Controller::$userInfo)->getAll();
    }

    public function stokSeriIleGetir($serino) {
        $sql = "SELECT ".
                Controller::helper(null,"stokModelHelper")->getStokVaryantSelect(true). " , stok_haraket_giris.seri_no as seri_no , stok_haraket_giris.depo as depo_id , stok_haraket_giris.id as ozel_urun_id "
                . "FROM "
                . "stok,stok_haraket_giris  LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  "
                . "WHERE "
                . "stok_haraket_giris.stok_id = stok.id and "
                . "stok.remove = 0 and "
                . "stok.owner_id = :owner and "
                . "stok_haraket_giris.seri_no = :serino and stok_haraket_giris.ozel_urun = 1 and stok_haraket_giris.ozel_urun_durum = 0 ";
        $query = $this->getConnection()->prepare($sql);

        $query->BindParam(":serino", $serino, PDO::PARAM_STR);
        $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
        $query->execute();

        return $query->fetch();
    }

    public function stokKodIleGetir($stokkod) {
        $sql = "SELECT ".
                 Controller::helper(null,"stokModelHelper")->getStokVaryantSelect(true). ", stok.stok_standart_adet "
                . "FROM "
                . "stok  LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  "
                . "WHERE "
                . "stok.remove = 0 and "
                . "stok.owner_id = :owner and "
                . "(stok.stok_barkod_no = :stokkod OR stok.stok_kod = :stokkod) LIMIT 0,10";
        $query = $this->getConnection()->prepare($sql);

        $query->BindParam(":stokkod", $stokkod, PDO::PARAM_STR);
        $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function stokIsımIleGetir($stokIsım , $yanlizca_ana_stok = 0) {

        if ($stokIsım == "" || empty($stokIsım) || $stokIsım == NULL) {

            return false;
        } else {


            if($yanlizca_ana_stok > 0){



                $sql = "SELECT stok.id , stok.stok_adi FROM "
                    . "stok  "
                    . "WHERE "
                    . "stok.stok_adi LIKE :str  and stok.remove = 0 and stok.owner_id = :owner and stok_parent_id = 0 LIMIT 10";


            }else{
                $sql = "SELECT ".
                    Controller::helper(null,"stokModelHelper")->getStokVaryantSelect(true). " , stok.stok_standart_adet  "
                    . "FROM "
                    . "stok  LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  "
                    . "WHERE "
                    . "stok.stok_adi LIKE :str  and stok.remove = 0 and stok.owner_id = :owner   LIMIT 10";


            }



            $query = $this->getConnection()->prepare($sql);

            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindValue(":str", "%" . $stokIsım . "%", PDO::PARAM_STR);


            $query->execute();


            if ($query->rowCount() > 0) {

                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {

                return false;
            }
        }
    }



    public function grupStoklariAl($id){

        return $this->table("stok_grup_stoklari", Controller::$userInfo)
            ->disableDefault()
            ->select([Controller::helper(null,"stokModelHelper")->getStokVaryantSelect(true)." , stok_grup_stoklari.grup_stok_id ,  stok_grup_stoklari.miktar as gsmiktar , stok_grup_stoklari.stok_id as gsstok_id "])
            ->innerjoin(" INNER JOIN  stok ON stok_grup_stoklari.grup_stok_id  = stok.id  ")
            ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
            ->where(
                [
                    " stok_grup_stoklari.stok_id "=>["=",$id],
                    " stok_grup_stoklari.owner_id"=>["=",Controller::$userInfo["owner_id"]],
                    " stok_grup_stoklari.remove"=>["=",0],
                    " stok.remove"=>["=",0],
                    " stok.owner_id"=>["=",Controller::$userInfo["owner_id"]],


                ]


            )
            ->getAll();

    }

    public function stokIsımIleGetirDepoIcın($stokIsım) {

        if ($stokIsım == "" || empty($stokIsım) || $stokIsım == NULL) {

            return false;
        } else {
            $sql = "SELECT ".
                Controller::helper(null,"stokModelHelper")->getStokVaryantSelect(true). "  , stok.stok_standart_adet "
                    . "FROM "
                    . "stok   LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  "
                    . "WHERE "
                    . "stok.stok_adi LIKE :str and stok.remove = 0 and stok.owner_id = :owner LIMIT 10 ";


            $query = $this->getConnection()->prepare($sql);


            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindValue(":str", "%" . $stokIsım . "%", PDO::PARAM_STR);

            $query->execute();


            if ($query->rowCount() > 0) {

                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {

                return false;
            }
        }
    }

    public function varyantlariAl($stok_id){


        if($stok_id == 0){

            return false;

        }else{

            return $this->table("stok", Controller::$userInfo)
                 ->select([Controller::helper(null,"stokModelHelper")->getStokVaryantSelect(true)])
                ->disableDefault()
                ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->where([
                    "stok.stok_parent_id"=>["=",$stok_id],
                    "stok.remove" => ["=", "0"],
                    "stok.owner_id"=>["=", Controller::$userInfo["owner_id"]]

                ])->getAll();
        }


    }


    public function stokOyu($stok_id){
        $sql ="SELECT raiting FROM stok_rating WHERE stok_id = ? and user_id = ? and owner_id = ? and remove = ? ";
        $query = $this->getConnection()->prepare($sql);
        $query->execute([$stok_id, Controller::$userInfo["id"] ,  Controller::$userInfo["owner_id"] , 0 ]);

       return $query->fetch();
    }

    public function stokOyKaydet($request){


        $stok_id = $request->input("stok_id");
        $oy =  $request->input("oy");



        $sql ="SELECT id FROM stok_rating WHERE stok_id = ? and user_id = ? and owner_id = ? and remove = ? ";
        $query = $this->getConnection()->prepare($sql);
        $query->execute([$stok_id, Controller::$userInfo["id"] ,  Controller::$userInfo["owner_id"] , 0 ]);

        $result = $query->fetch();


        if($result){


            return $this->getConnection()
                ->prepare("UPDATE stok_rating SET update_date = ? , raiting = ? WHERE id = ? ")
                ->execute([date("Y-m-d H:i:s"),$oy,$result["id"]]);


        }else{

            return $this->getConnection()
                ->prepare("INSERT INTO stok_rating SET created_date = ? , stok_id = ? , user_id = ? , owner_id = ? , remove = ? , raiting = ? ")
                ->execute([date("Y-m-d H:i:s"),$stok_id, Controller::$userInfo["id"] ,  Controller::$userInfo["owner_id"] , 0 , $oy]);
        }






    }

}
