<?php

use \Dipa\Db\Model;

/**
 *
 * @author Doğuş DİCLE
 */
class parakendeModel extends Model {

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
            return false;
        }
    }

    public function getUpdated($last_id) {

        $query = $this->getConnection()->prepare(""
                . "SELECT stok.* , stok_birimler.stok_birim_adi "
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

    public function cariArama($srt, $owner) {
        if ($srt == "" || empty($srt) || $srt == NULL) {

            return false;
        } else {
            $sql = "SELECT "
                    . "* "
                    . "FROM "
                    . "cari "
                    . "WHERE "
                    . "remove = 0 and owner_id = :owner "
                    . "and (cari_adi LIKE :str or id = :cari_id or rfid_id = :cari_id) ";


            $query = $this->getConnection()->prepare($sql);

            $query->BindParam(":owner", $owner, PDO::PARAM_STR);
            $query->BindValue(":str", "%" . $srt . "%", PDO::PARAM_STR);
            $query->BindParam(":cari_id", $srt, PDO::PARAM_STR);

            $query->execute();


            if ($query->rowCount() > 0) {

                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {

                return false;
            }
        }
    }

    public function cariEkle($request) {
        $cari_adi_input = $request->input("cari_adi_input");
        $cari_yekili_input = $request->input("cari_yekili_input");
        $cari_telefon_input = $request->input("cari_telefon_input");
        $cari_mail_input = $request->input("cari_mail_input");
        $cari_unvan_input = $request->input("cari_unvan_input");
        $cari_vergino_input = $request->input("cari_vergino_input");
        $cari_vergi_daire_input = $request->input("cari_vergi_daire_input");
        $cari_fatura_adres_input = $request->input("cari_fatura_adres_input");

        $owner_id = $request->input("owner_id");

        $kod = time();

        $date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO cari ("
                . "cari_kod,"
                . "cari_adi,"
                . "cari_telefon,"
                . "cari_adres,"
                . "cari_mail,"
                . "owner_id,"
                . "created_date"
                . ") VALUES (?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);
        $result = $query->execute([
            $kod,
            $cari_adi_input,
            $cari_telefon_input,
            $cari_fatura_adres_input,
            $cari_mail_input,
            $owner_id,
            $date
        ]);

        if ($result) {

            $cari_id = $this->getConnection()->lastInsertId();

            if ($cari_id) {

                $faturaSql = "INSERT INTO cari_fatura_bilgileri ("
                        . "cari_id,"
                        . "cari_unvan,"
                        . "cari_vergi_no,"
                        . "cari_vergi_daire,"
                        . "cari_fatura_adres,"
                        . "birincil_fatura,"
                        . "owner_id,"
                        . "created_date"
                        . ") VALUES (?,?,?,?,?,?,?,?)";


                $faturaQuery = $this->getConnection()->prepare($faturaSql);
                $faturaResult = $faturaQuery->execute([
                    $cari_id,
                    $cari_adi_input,
                    $cari_vergino_input,
                    $cari_vergi_daire_input,
                    $cari_fatura_adres_input,
                    1,
                    $owner_id,
                    $date
                ]);

                if ($faturaResult) {

                    return $cari_id;
                } else {

                    return "faturadontsave";
                }
            } else {
                return "caridontsave";
            }
        }
    }

    public function parakendeSatisCikis($request) {

        $postdata = $request->getAll();

        $serial_no = $request->input("device_serial");



        $cihaz_sql = "SELECT * FROM parakende_yazilim_ayarlari WHERE serial_code = ? and remove = 0";

        $cihaz_query = $this->getConnection()->prepare($cihaz_sql);

        $cihaz_query->execute([$serial_no]);

        $yazilim_ayari = $cihaz_query->fetch();






        if ($yazilim_ayari) {


            $products_data = json_decode($request->input("selected_products"), true);

            if ($request->input("cikis_evrak_no") != NULL) {

                $evrak_no = $request->input("cikis_evrak_no");
            } else {

                $evrak_no = "prk-" . +time();
            }


            $cari_id = $request->input("cari_id");


            if ($request->input("vade_gun") != NULL) {

                $vade_gun = $request->input("vade_gun");
            } else {

                $vade_gun = 0;
            }


            //Satış parakende ise standart cari hesabı seçicez
            if ($cari_id == null) {

                $cari_id = $yazilim_ayari["master_parakende_cari_hesap_id"];
            }



            $time = date("H:i:s");

            $cikis_tarih = date("Y-m-d H:i:s");

            $user_id = $request->input("user_id");

            $owner = $request->input("owner_id");

            $evrak = $request->input("evrak");



            if ($evrak == "none") {

                $evrak_tur = 0;
            }


            $sql = "INSERT INTO satis_evraklari ("
                    . "evrak_no,"
                    . "tarih,"
                    . "cari_id,"
                    . "evrak_tur,"
                    . "owner_id,"
                    . "created_date,"
                    . "created_user,vade_gun,parakende_satis) VALUES (?,?,?,?,?,?,?,?,?)";

            $query = $this->getConnection()->prepare($sql);
            $result = $query->execute([
                $evrak_no, $cikis_tarih, $cari_id, $evrak_tur, $owner, $cikis_tarih, $user_id, $vade_gun, 1
            ]);

            $satis_evrak_id = $this->getConnection()->lastInsertId();


            $ara_toplam = 0;
            $toplam_vergi = 0;
            $toplam_indirim = 0;
            $genel_toplam = 0;


            foreach ($products_data as $key => $value) {

                $stok_id = $value["product_id"];
                $miktar = $value["amount"];
                $satis_fiyat = $value["price"];
                $kdv_oran = $value["kdv_oran"];
                $depo = $yazilim_ayari["cikis_yapacagi_depo_id"];
                $seri_no = "";
                $ozel_urun_id = 0;
                $ozel_urun = 0;


                $faturaSql = "INSERT INTO stok_haraket_cikis ("
                        . "seri_no,"
                        . "cikis_tarih,"
                        . "cikis_evrak_no,"
                        . "stok_id,"
                        . "adet,"
                        . "satis_fiyati,"
                        . "kdv_oran,"
                        . "cari_id,"
                        . "ozel_urun_id,"
                        . "depo,"
                        . "owner_id,"
                        . "parakende_cikis,"
                        . "created_user,"
                        . "parakende_yazilim_serial,"
                        . "created_date,"
                        . "satis_evrak_id"
                        . ") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";


                $faturaQuery = $this->getConnection()->prepare($faturaSql);
                $faturaResult = $faturaQuery->execute([
                    $seri_no,
                    $cikis_tarih,
                    $evrak_no,
                    $stok_id,
                    $miktar,
                    $satis_fiyat,
                    $kdv_oran,
                    $cari_id,
                    $ozel_urun_id,
                    $depo,
                    $owner,
                    1,
                    $user_id,
                    $serial_no,
                    $cikis_tarih,
                    $satis_evrak_id
                ]);

                $query = $this->getConnection()->prepare("UPDATE stok SET stok_adet = stok_adet-:adet  WHERE id = :id");
                $update = $query->execute(array("adet" => $miktar, "id" => $stok_id));


                $ara_toplam = $ara_toplam + ($satis_fiyat * $miktar);


                if ($kdv_oran > 0) {

                    if ($kdv_oran > 9) {

                        $kdv_duzelt = "1." . $kdv_oran;
                    } else {

                        $kdv_duzelt = "1.0" . $kdv_oran;
                    }

                    $kdvli_toplam_tutar = $ara_toplam * $kdv_duzelt;

                    $toplam_vergi = $toplam_vergi + ($kdvli_toplam_tutar - $ara_toplam);

                    $genel_toplam = $genel_toplam + $kdvli_toplam_tutar;
                }else{
                     $genel_toplam = $genel_toplam +$ara_toplam;
                }
                
               
            }




            $query2 = $this->getConnection()->prepare("UPDATE "
                    . "satis_evraklari "
                    . "SET "
                    . "ara_toplam = :ara_top , "
                    . "vergi_toplam = :vergi_top , "
                    . "indirim_toplam = :indirim_top , "
                    . "genel_toplam = :genel_top "
                    . "WHERE id = :id"
            );

            $update2 = $query2->execute(array(
                "id" => $satis_evrak_id,
                "ara_top" => $ara_toplam,
                "vergi_top" => $toplam_vergi,
                "indirim_top" => $toplam_indirim,
                "genel_top" => $genel_toplam));



            //Tahsilatlar

            $payments = json_decode($request->input("payments"), true);

            $paymet_sql = "";

            $toplam_nakit_tutar = 0;

            $toplam_kk_tutar = 0;

            $tarih = date("Y-m-d");


            foreach ($payments as $p_key => $p_value) {

                $tutar = $p_value[0];

                $tip = $p_value[1];

                if ($tip == "nakit") {

                    $toplam_nakit_tutar = $toplam_nakit_tutar + $tutar;

                    $paymet_sql .= "INSERT INTO kasa_haraket "
                            . "("
                            . "kasa_id,"
                            . "kasa_haraket_tip,"
                            . "kasa_haraket_cari_id,"
                            . "kasa_haraket_tutar,"
                            . "kasa_haraket_tarih,"
                            . "owner_id,"
                            . "created_user,"
                            . "created_date,"
                            . "satis_evrak_id"
                            . ") "
                            . "VALUES ({$yazilim_ayari["kasa_id"]},1,$cari_id,$tutar,\"$tarih\",$owner,$user_id,\"$cikis_tarih\",$satis_evrak_id); ";
                } else if ($tip == "kk") {

                    $toplam_kk_tutar = $toplam_nakit_tutar + $tutar;
                }
            }


            if ($paymet_sql != "") {
                $payquery = $this->getConnection()->prepare($paymet_sql);
                $payquery->execute();

                $kasa = $yazilim_ayari["kasa_id"];
                $payquery = $this->getConnection()->prepare("UPDATE kasalar SET kasa_toplam_tutar = kasa_toplam_tutar+? , update_date = ? WHERE id = ?");
                $payquery->execute([$toplam_nakit_tutar,$cikis_tarih,$kasa]);
            }

            return True;
        } else {

            return false;
        }
    }

    public function getCari($http_request) {

        $id = $http_request->input("selected_account_id");
        $owner_id = $http_request->input("owner_id");

        $query = $this
                ->getConnection()
                ->prepare("SELECT * FROM cari WHERE  id = ? AND remove = 0 AND owner_id = ? ");

        $query->execute(array($id, $owner_id));
        $cari_data =  $query->fetch(PDO::FETCH_ASSOC);
        
        $cari_id = $id;
        


        $toplamSql = "SELECT sum(genel_toplam) as genel_toplam FROM satis_evraklari WHERE cari_id = ? AND remove = 0 and owner_id = ?";

        $query = $this->getConnection()->prepare($toplamSql);

        $query->execute([$cari_id, $owner_id]);

        $toplam_satis = $query->fetch();

        if ($toplam_satis) {
            $cari_data["cari_toplam_satis"] = $toplam_satis["genel_toplam"];
        }


        $toplamTahsilatSql = "SELECT  sum(kasa_haraket_tutar) as toplam_tahsilat_tutari "
                . "FROM kasa_haraket "
                . "WHERE "
                . "kasa_haraket_cari_id = ? and kasa_haraket_tip = 1 and remove = 0 and owner_id = ?  ";

        $tahsilatQuery = $this->getConnection()->prepare($toplamTahsilatSql);
        $tahsilatQuery->execute([$cari_id, $owner_id]);
        $toplam_tahsilat = $tahsilatQuery->fetch();


        if ($toplam_tahsilat['toplam_tahsilat_tutari']) {
            $cari_data['cari_toplam_tahsilat'] = $toplam_tahsilat['toplam_tahsilat_tutari'];
        }

         $cari_data['cari_kul_limit'] = 0;
         
         

        $cari_data['cari_bakiye'] = $cari_data["cari_toplam_satis"] - $cari_data['cari_toplam_tahsilat']; 
        
        
        if($cari_data['cari_kredi_limit'] > 0){
            
              $cari_data['cari_kul_limit'] = $cari_data['cari_bakiye'] - $cari_data['cari_kredi_limit'];
            
            
        }else if($cari_data['cari_kredi_limit'] >  $cari_data['cari_bakiye']){
            
              $cari_data['cari_kul_limit'] = $cari_data['cari_bakiye'] - $cari_data['cari_kredi_limit'];
            
            
        }else{
            $cari_data['cari_kul_limit'] = 0;
            
        }
      
        
        return $cari_data;
        
    }

    public function getCariFaturaBilgileri($http_request) {

        $id = $http_request->input("selected_account_id");
        $owner_id = $http_request->input("owner_id");

        $query = $this
                ->getConnection()
                ->prepare("SELECT * FROM cari_fatura_bilgileri WHERE birincil_fatura = 1 AND cari_id = ? AND remove = 0 AND owner_id = ? ");

        $query->execute(array($id, $owner_id));
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function faturaBilgileriniGuncelle($http_request) {

        $fatura_id = $http_request->input("fatura_id");
        $cari_id = $http_request->input("cari_id");
        $owner_id = $http_request->input("owner_id");
        $user = $http_request->input("user_id");
        $unvan = $http_request->input("unvan");
        $vergino = $http_request->input("vergino");
        $vergidaire = $http_request->input("vergidaire");
        $vergiadres = $http_request->input("vergiadres");
        $update_date = date("Y-m-d H:i:s");

        $update_sql = "UPDATE cari_fatura_bilgileri SET "
                . "cari_unvan = ? ,"
                . "cari_vergi_no = ?,"
                . "cari_vergi_daire = ? ,"
                . "cari_fatura_adres = ? ,"
                . "updated_date = ? ,"
                . "updated_user = ? WHERE id = ? AND owner_id = ? AND cari_id = ? AND remove = 0 ";

        $query = $this->getConnection()->prepare($update_sql);
        return $query->execute(array($unvan, $vergino, $vergidaire, $vergiadres, $update_date, $user, $fatura_id, $owner_id, $cari_id));
    }

}
