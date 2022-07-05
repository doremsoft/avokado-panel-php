<?php


use \Dipa\Db\Model;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mobileModel
 *
 * @author dogus
 */
class mobileModel extends Model
{

    private $stok_varyant_select = ""
    . "stok.id as stok_id, "
    . "stok.stok_kod , "
    . "stok.stok_alis_fiyati  , "
    . "stok.stok_satis_fiyati , "
    . "stok.stok_parent_id as s_pid , "
    . "stok.stok_varyant_adi,"
    . "stok.stok_varyant_deger,"
    . "stok.stok_create_id , "
    . "stok.stok_barkod_no , "
    . "stok.stok_resim , stok.remove , "
    . "IF(stok.stok_parent_id=0,stok.stok_fiyat_vergi_durum,(select stok_fiyat_vergi_durum from stok where id = s_pid LIMIT 1)) AS stok_fiyat_vergi_durum , "
    . "IF(stok.stok_parent_id=0,stok.stok_kdv_oran,(select stok_kdv_oran from stok where id = s_pid LIMIT 1)) AS stok_kdv_oran , "
    . "IF(stok.stok_parent_id=0,stok.stok_doviz,(select stok_doviz from stok where id = s_pid LIMIT 1)) AS stok_doviz , "
    . "IF(stok.stok_parent_id=0,stok.stok_birimi,(select stok_birimi from stok where id = s_pid LIMIT 1)) AS stok_birimi , "
    . "IF(stok.stok_parent_id=0,stok.stok_adi,(select stok_adi from stok where id = s_pid LIMIT 1)) AS stok_adi ,
               stok.id as st_id ,
                (
                IF((SELECT SUM(adet) AS ta FROM stok_haraket_giris WHERE stok_id = st_id and remove = 0 ) IS NULL , 0 , (SELECT SUM(adet) AS ta FROM stok_haraket_giris WHERE stok_id = st_id  and remove = 0 )) - 
                IF((SELECT SUM(adet) AS ta FROM stok_haraket_cikis WHERE stok_id = st_id and remove = 0 ) IS NULL , 0 , (SELECT SUM(adet) AS ta FROM stok_haraket_cikis WHERE stok_id = st_id  and remove = 0) )
                ) as stok_adet ";

    public function butunStoklariAl($request)
    {

        $owner_id = $request->input("ownerid");

        $query = $this->getConnection()->prepare("SELECT {$this->stok_varyant_select} FROM stok WHERE owner_id = ? ");

        $query->execute(array($owner_id));

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stokAl($request)
    {

        $owner_id = $request->input("ownerid");

        $barcode = $request->input("barcode");

        $query = $this->getConnection()->prepare("SELECT {$this->stok_varyant_select} FROM stok  WHERE   owner_id = ? and stok_barkod_no = ? and remove = 0 ");

        $query->execute(array($owner_id, $barcode));

        return $query->fetch(PDO::FETCH_ASSOC);
    }


    public function dovizKurlariAl($request)
    {
        $owner = $request->input("ownerid");

        $dovizler = [
            "Tl" => 1,
            "TL" => 1,
            "tl" => 1,
            "EUR" => 1,
            "USD" => 1

        ];

        $dovizlersql = "SELECT * FROM doviz WHERE remove = 0 and owner_id = :owner ";
        $doviz_query = $this->getConnection()->prepare($dovizlersql);
        $doviz_query->BindParam(":owner", $owner, PDO::PARAM_STR);
        $doviz_query->execute();

        if ($doviz_query->rowCount() > 0) {
            $doviz_result = $doviz_query->fetchAll(PDO::FETCH_ASSOC);
            if ($doviz_result) {
                foreach ($doviz_result as $key => $value) {
                    $dovizler[$value["doviz_kod"]] = $value["doviz_kur"];
                }
            }
        }

        return $dovizler;
    }

    public function stokGruplariAl($request)
    {

        $owner_id = $request->input("ownerid");

        $query = $this->getConnection()->prepare("SELECT id , stok_grup_adi FROM stok_gruplar  WHERE  owner_id = ? and remove = 0 ");

        $query->execute([$owner_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stokEkle($request)
    {


        $kdvsiz_satis_fiyat = 0;

        $owner_id = $request->input("ownerid");

        $stok_ad = $request->input("stok_ad");

        $barkod_no = $request->input("barkod_no");

        $kdv_oran = $request->input("kdv_oran");

        $satis_fiyat = $request->input("satis_fiyat");

        $stok_birim = $request->input("stok_birim");

        $stok_doviz = $request->input("stok_doviz");

        $stok_standart_adet = $request->input("stok_standart_adet");

        $stok_grup = $request->input("stok_grup");

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"];

        $last_id++;


        if ($kdv_oran > 9) {

            $kdv_duzelt = "1." . $kdv_oran;
        } else {

            $kdv_duzelt = "1.0" . $kdv_oran;
        }


        if ($kdv_oran == 0) {

            $kdvsiz_satis_fiyat = $satis_fiyat;
        } else {


            $kdvsiz_satis_fiyat = $satis_fiyat / $kdv_duzelt;
        }


        $update_sql = "INSERT INTO stok  ("
            . "last_val , "
            . "stok_kdv_dahil_satis_fiyati ,"
            . "stok_kdv_oran , "
            . "stok_satis_fiyati ,"
            . "stok_adi , "
            . "stok_birimi , "
            . "stok_grup, "
            . "stok_resim , "
            . "stok_standart_adet,"
            . "owner_id,"
            . "stok_barkod_no,"
            . "stok_fiyat_vergi_durum,"
            . "created_date,"
            . "stok_create_id,"
            . "stok_doviz) "
            . " VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";

        $date = date("Y-m-d H:i:s");
        $uniq = uniqid();

        $stok_query = $this->getConnection()
            ->prepare($update_sql)
            ->execute([
                $last_id,
                $satis_fiyat,
                $kdv_oran,
                $kdvsiz_satis_fiyat,
                $stok_ad,
                $stok_birim,
                $stok_grup,
                "noimage.jpg",
                $stok_standart_adet,
                $owner_id,
                $barkod_no,
                2,
                $date,
                $uniq,
                $stok_doviz
            ]);


        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);

        return $stok_query;
    }

    public function stokGuncelle($request)
    {


        $kdvsiz_satis_fiyat = 0;
        $owner_id = $request->input("ownerid");
        $stok_id = $request->input("stok_id");
        $kdv_oran = $request->input("kdv_oran");
        $satis_fiyat = $request->input("satis_fiyat");


        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"];

        $last_id++;


        if ($kdv_oran > 9) {

            $kdv_duzelt = "1." . $kdv_oran;
        } else {

            $kdv_duzelt = "1.0" . $kdv_oran;
        }


        if ($kdv_oran == 0) {

            $kdvsiz_satis_fiyat = $satis_fiyat;
        } else {


            $kdvsiz_satis_fiyat = $satis_fiyat / $kdv_duzelt;
        }


        $update_sql = "UPDATE stok SET "
            . "last_val = ? , "
            . "stok_kdv_dahil_satis_fiyati = ? , "
            . "stok_kdv_oran = ? , "
            . "stok_satis_fiyati = ? "
            . "WHERE id = ?";


        $stok_query = $this->getConnection()
            ->prepare($update_sql)
            ->execute([
                $last_id,
                $satis_fiyat,
                $kdv_oran,
                $kdvsiz_satis_fiyat,
                $stok_id
            ]);


        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);

        return $stok_query;
    }

    public function stokHizliFiyatGuncelle($request)
    {


        $kdvsiz_satis_fiyat = 0;
        $owner_id = $request->input("ownerid");
        $stok_id = $request->input("stok_id");
        $kdv_oran = $request->input("kdv_oran");
        $satis_fiyat = $request->input("satis_fiyat");

        $para_birim = $request->input("para_birim");


        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"];

        $last_id++;


        if ($kdv_oran > 9) {

            $kdv_duzelt = "1." . $kdv_oran;
        } else {

            $kdv_duzelt = "1.0" . $kdv_oran;
        }


        if ($kdv_oran == 0) {

            $kdvsiz_satis_fiyat = $satis_fiyat;
        } else {


            $kdvsiz_satis_fiyat = $satis_fiyat / $kdv_duzelt;
        }


        $date = date("Y-m-d H:i:s");


        $update_sql = "UPDATE stok SET "
            . "last_val = ? , "
            . "stok_kdv_dahil_satis_fiyati = ? , "
            . "stok_kdv_oran = ? , "
            . "stok_satis_fiyati = ? , "
            . "stok_doviz = ? ,"
            . "update_date = ? "
            . "WHERE id = ?";


        $stok_query = $this->getConnection()
            ->prepare($update_sql)
            ->execute([
                $last_id,
                $satis_fiyat,
                $kdv_oran,
                $kdvsiz_satis_fiyat,
                $para_birim,
                $date,
                $stok_id
            ]);


        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);

        return $stok_query;
    }


    /*
     * I/System.out:
     * {"stok_sayim_msg":null,
     * "stok_sayim_vergiler_dahil_fiyat":"1",
     * "stok_sayim_uniq_code":"933d019a-eb67-4d0b-9973-c2e1e76d0f09",
     * "stok_sayim_durum":"0",
     * "stok_sayim_start_date":"2019-09-09 18:11:38",
     * "stok_sayim_adi":"Deneme sayim",
     * "stok_sayim_complate_date":""}
     *
     *
     */


    private function addSayim($request, $sayim_data)
    {

        $insert_sql = "INSERT INTO stok_sayimlar SET sayim_uniq = ? ,
                                                    sayim_adi = ? ,
                                                    sayim_baslama_tarih = ? ,
                                                    sayim_durum  = ? ,
                                                    owner_id  = ? ,
                                                    created_date  =  ? ,
                                                    created_user  = ? ,
                                                    account_no  = ? ,
                                                    update_date = ? ,
                                                    updated_user = ?,
                                                    sayim_bitis_tarih = ?
                                                    ";


        if ($request->input("complate") == "1") {

            $sayim_data["stok_sayim_durum"] = "1";

            $sayim_data["stok_sayim_bitis"] = date("Y-m-d H:i:s");
        } else {

            $sayim_data["stok_sayim_bitis"] = "";
        }


        return $this->getConnection()
            ->prepare($insert_sql)
            ->execute([
                $sayim_data["stok_sayim_uniq_code"],
                $sayim_data["stok_sayim_adi"],
                $sayim_data["stok_sayim_start_date"],
                $sayim_data["stok_sayim_durum"],
                $request->input("ownerid"),
                date("Y-m-d H:i:s"),
                $request->input("userid"),
                $request->input("accountno"),
                date("Y-m-d H:i:s"),
                $request->input("userid"),
                $sayim_data["stok_sayim_bitis"]
            ]);


    }


    private function updateSayim($request, $sayim_data)
    {

        $insert_sql = "UPDATE stok_sayimlar SET  sayim_adi = ? ,
                                                    sayim_baslama_tarih = ? ,
                                                    sayim_durum  = ? ,
                                                    created_date  =  ? ,
                                                    created_user  = ? ,
                                                    account_no  = ? ,
                                                    update_date = ? ,
                                                    updated_user = ?,
                                                    sayim_bitis_tarih = ? 
                                                    WHERE 
                                                    sayim_uniq = ? and  owner_id  = ? ";


        if ($request->input("complate") == "1") {

            $sayim_data["stok_sayim_durum"] = "1";

            $sayim_data["stok_sayim_bitis"] = date("Y-m-d H:i:s");

        } else {

            $sayim_data["stok_sayim_bitis"] = "";
        }


        return $this->getConnection()
            ->prepare($insert_sql)
            ->execute([
                $sayim_data["stok_sayim_adi"],
                $sayim_data["stok_sayim_start_date"],
                $sayim_data["stok_sayim_durum"],
                date("Y-m-d H:i:s"),
                $request->input("userid"),
                $request->input("accountno"),
                date("Y-m-d H:i:s"),
                $request->input("userid"),
                $sayim_data["stok_sayim_bitis"],
                $sayim_data["stok_sayim_uniq_code"],
                $request->input("ownerid")
            ]);


    }


    private function sayimExits($sayim_uniq_id, $owner_id)
    {

        $sql = "SELECT * FROM stok_sayimlar WHERE remove = 0 and sayim_uniq = ? and owner_id = ?  ";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([$sayim_uniq_id, $owner_id]);

        return $query->fetch();

    }

    public function sayimlariAktar($request)
    {

        $sayim_data = json_decode(urldecode($request->input("sayiminfo")), true);

        $sayim_data_result = $this->sayimExits($sayim_data["stok_sayim_uniq_code"], $request->input("ownerid"));

        $sayim_durum = false;

        if ($sayim_data_result) {

            $sayim_durum = true;


            $this->updateSayim($request, $sayim_data);

        } else {

            $insert_result = $this->addSayim($request, $sayim_data);

            if ($insert_result) {
                $sayim_durum = true;
            }

        }


        if ($sayim_durum) {

            $kalem_islem_result = $this->sayimKalemIslem($sayim_data["stok_sayim_uniq_code"], $request->input("ownerid"), json_decode(urldecode($request->input("sayimlist")), true), $request);

            return $kalem_islem_result;

        } else {
            return false;
        }

    }


    /*
     * [
    {"sayim_kalem_stok_vergi_oran":"18",
    "sayim_kalem_stok_satis_fiyat":"211.8645",
    "sayim_kalem_sayimlar_id":"2",
    "owner_id":"1",
    "sayim_kalem_stok_mevcut":"10",
    "sayim_kalem_stok_alis_doviz":"TL",
    "sayim_kalem_stok_satis_doviz":"TL",
    "account_no":"121",
    "sayim_kalem_stok_alis_fiyat":"127.1102",
    "sayim_kalem_remove":"0",
    "sayim_kalem_stok_adi":"deneme",
    "id":"4",
    "sayim_kalem_stok_id":"1",
    "sayim_kalem_islem_tarihi":"2019-09-09 18:11:55"}
  }
     */


    private function sayimKalemIslem($sayim_uniq_id, $owner_id, $kalemler, $request)
    {


        $error = false;

        $user_id = $request->input("userid");

        foreach ($kalemler as $key => $value) {

            $stok_durum = $this->sayimKalemExits($sayim_uniq_id, $value["sayim_kalem_stok_id"], $owner_id);

            if ($stok_durum) {

                $update_status = $this->updateSayimKalem($sayim_uniq_id, $owner_id, $value, $user_id);

                if (!$update_status) {
                    $error = true;

                }

            } else {

                $insert_status = $this->addSayimKalem($sayim_uniq_id, $owner_id, $value, $user_id);

                if (!$insert_status) {
                    $error = true;

                }
            }

        }

        if (!$error) {
            return true;
        } else {
            return false;
        }

    }


    private function addSayimKalem($sayim_uniq_id, $owner_id, $val, $user_id)
    {

        $insert_sql = "INSERT INTO stok_sayim_kalemler SET 
sayim_uniq_id = ? ,
owner_id   = ? ,
stok_id = ? ,
stok_adi = ? ,
stok_alim_fiyati = ? ,
stok_satis_fiyati = ? ,
stok_doviz = ? ,
stok_vergi_oran = ? ,
stok_mevcut_adet = ? ,
account_no  = ? ,
created_date  = ? ,
update_date  = ? ,
created_user  = ? ,
updated_user  = ? ";

        return $this->getConnection()
            ->prepare($insert_sql)
            ->execute([
                $sayim_uniq_id,
                $owner_id,
                $val["sayim_kalem_stok_id"],
                $val["sayim_kalem_stok_adi"],
                $val["sayim_kalem_stok_alis_fiyat"],
                $val["sayim_kalem_stok_satis_fiyat"],
                $val["sayim_kalem_stok_satis_doviz"],
                $val["sayim_kalem_stok_vergi_oran"],
                $val["sayim_kalem_stok_mevcut"],
                $val["account_no"],
                date("Y-m-d H:i:s"),
                date("Y-m-d H:i:s"),
                $user_id,
                $user_id
            ]);


    }

    private function updateSayimKalem($sayim_uniq_id, $owner_id, $val, $user_id)
    {


        $update_sql = "UPDATE stok_sayim_kalemler SET 
stok_adi = ? ,
stok_alim_fiyati = ? ,
stok_satis_fiyati = ? ,
stok_doviz = ? ,
stok_vergi_oran = ? ,
stok_mevcut_adet = ? ,
update_date  = ? ,
updated_user  = ? WHERE sayim_uniq_id = ? and owner_id   = ? and stok_id = ? ";

        return $this->getConnection()
            ->prepare($update_sql)
            ->execute([
                $val["sayim_kalem_stok_adi"],
                $val["sayim_kalem_stok_alis_fiyat"],
                $val["sayim_kalem_stok_satis_fiyat"],
                $val["sayim_kalem_stok_satis_doviz"],
                $val["sayim_kalem_stok_vergi_oran"],
                $val["sayim_kalem_stok_mevcut"],
                date("Y-m-d H:i:s"),
                $user_id,
                $sayim_uniq_id,
                $owner_id,
                $val["sayim_kalem_stok_id"],
            ]);


    }

    private function sayimKalemExits($sayim_uniq_id, $sayim_kalem_stok_id, $owner_id)
    {


        $sql = "SELECT id FROM stok_sayim_kalemler WHERE remove = 0 and sayim_uniq_id = ? and stok_id = ? and owner_id = ? LIMIT 1 ";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([$sayim_uniq_id, $sayim_kalem_stok_id, $owner_id]);

        return $query->fetch();

    }


    public function stokAra($request)
    {


        $str = $request->input("query");
        $owner = $request->input("ownerid");

        $dovizler = [
            "Tl" => 1,
            "TL" => 1,
            "tl" => 1,
            "EUR" => 0,
            "USD" => 0

        ];

        $dovizlersql = "SELECT * FROM doviz WHERE remove = 0 and owner_id = :owner ";
        $doviz_query = $this->getConnection()->prepare($dovizlersql);
        $doviz_query->BindParam(":owner", $owner, PDO::PARAM_STR);
        $doviz_query->execute();

        if ($doviz_query->rowCount() > 0) {
            $doviz_result = $doviz_query->fetchAll(PDO::FETCH_ASSOC);
            if ($doviz_result) {
                foreach ($doviz_result as $key => $value) {
                    $dovizler[$value["doviz_kod"]] = $value["doviz_kur"];
                }
            }
        }


        $mvsql = "";

        $mevcutta = 0;
        if ($request->has("mevcutta")) {

            $mevcutta = $request->input("mevcutta");
        }


        if ($mevcutta > 0) {

            $mvsql = " and stok_adet > 0 ";
        }

        $sql = "SELECT "
            . $this->stok_varyant_select . " "
            . "FROM "
            . "stok "
            . "WHERE "
            . "stok.remove = 0 and "
            . "stok.owner_id = :owner and "
            . "(stok.stok_barkod_no = :barkod OR stok.stok_kod = :kod OR stok.stok_adi LIKE :str ) {$mvsql} LIMIT 20";

        $query = $this->getConnection()->prepare($sql);


        $query->BindParam(":owner", $owner, PDO::PARAM_STR);
        $query->BindValue(":str", "%" . $str . "%", PDO::PARAM_STR);
        $query->BindParam(":barkod", $str, PDO::PARAM_STR);
        $query->BindParam(":kod", $str, PDO::PARAM_STR);
        $query->execute();


        if ($query->rowCount() > 0) {

            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            $stok_list = [];

            foreach ($result as $k => $value) {

                $doviz = strtoupper($value["stok_doviz"]);

                $stok_satis_fiyati = $value["stok_satis_fiyati"];

                if ($doviz != "TL") {


                    $stok_satis_fiyati = $stok_satis_fiyati * $dovizler[$doviz];

                    $value["stok_satis_fiyati"] = $stok_satis_fiyati;


                    $value["stok_doviz"] = "TL";


                }

                array_push($stok_list, $value);

            }

            return $stok_list;
        } else {

            return false;
        }


    }


    public function bildirimKullaniciKontrol($request)
    {

        $owner_id = $request->input("ownerid");
        $user_id = $request->input("lastuserid");
        $bildirim_key = $request->input("bildirim_key");
        $query = $this->getConnection()->prepare("SELECT id  FROM users  WHERE  remove = 0 and owner_id = ? and bildirim_session_key = ? and id = ? ");
        $query->execute([$owner_id, $bildirim_key, $user_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function bildirimKontrol($request,$zaman)
    {
        $owner_id = $request->input("ownerid");
        $user_id = $request->input("lastuserid");

        $tarih = date("Y-m-d");
        $query = $this->getConnection()->prepare("SELECT id , bildirim_mesaj , tip  FROM bildirimler  WHERE  owner_id = ? and remove = 0  and user_id = ? and zaman < ? and tarih = ? and mobil_goruntuleme = 0 ");
        $query->execute([$owner_id, $user_id, $zaman, $tarih]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function bildirimOkundu($request)
    {

        $owner_id = $request->input("ownerid");
        $user_id = $request->input("lastuserid");
        $id_list = $request->input("idlist");
        $id_list = json_decode($id_list,true);
        $id_list = implode(",",$id_list);
        $query = $this->getConnection()->prepare("UPDATE  bildirimler  SET  mobil_goruntuleme = 1  WHERE owner_id = ? and user_id = ? and id IN ({$id_list}) ");
       return $query->execute([$owner_id, $user_id]);

    }




}
