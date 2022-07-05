<?php

use \Dipa\Db\Model;


Class stokAdetHelper
{


    private $stok_id;
    private $varyant_id;
    private $is_varyant;
    private $model;



    public function set($model, $stok_id){

        $this->model = $model;

        $get_stok_id_query = $this->model->prepare("SELECT * FROM stok WHERE id = ?  ");
        $get_stok_id_query->execute([$stok_id]);
        $stok_data = $get_stok_id_query->fetch();


        if($stok_data){

            if($stok_data["stok_parent_id"] > 0){

                $this->is_varyant = true;
                $this->varyant_id = $stok_id;
                $this->stok_id = $stok_data["stok_parent_id"];



            }else{

                $this->varyant_id = false;
                $this->varyant_id  = 0;
                $this->stok_id = $stok_id;
            }


        }


        return $this;

    }

    public function reset($model_rest = false){


        if($model_rest){

            $this->model = null;
        }

        $this->stok_id = 0;
        $this->varyant_id= 0;
        $this->is_varyant= 0;


    }


    public function count($update_stok = false)
    {

        $stok_id = 0;

        if($this->is_varyant){

            $stok_id = $this->varyant_id;

        }else{

            $stok_id = $this->stok_id;

        }


        $giris_sql = "SELECT SUM(adet) AS ta FROM stok_haraket_giris WHERE stok_haraket_giris.stok_id = ? and stok_haraket_giris.adet_etkisiz = 0  and stok_haraket_giris.remove = 0";
        $cikis_sql = "SELECT SUM(adet) AS ti FROM stok_haraket_cikis WHERE stok_haraket_cikis.stok_id = ? and stok_haraket_cikis.adet_etkisiz = 0 and stok_haraket_cikis.remove = 0";

        $giris = 0;
        $cikis = 0;


        $giris_query = $this->model->prepare($giris_sql);
        $giris_query->execute([$stok_id]);
        $giris_result = $giris_query->fetch(PDO::FETCH_ASSOC);

        if($giris_result){

            $giris = $giris_result["ta"];
        }




        $cikis_query = $this->model->prepare($cikis_sql);
        $cikis_query->execute([$stok_id]);
        $cikis_result = $cikis_query->fetch(PDO::FETCH_ASSOC);

        if($cikis_result){

            $cikis = $cikis_result["ti"];
        }

        $count = $giris - $cikis;

        if($update_stok){

            $update_query = $this->model->prepare("UPDATE stok  SET stok_adet = ? WHERE id = ? and remove = 0");

            $update_query->execute([$count,$stok_id]);

            if($this->is_varyant){


                $varyatnlar_toplami_sql = "SELECT SUM(stok_adet) AS toplam FROM stok WHERE stok_parent_id = ?  and stok.remove = 0";

                $query = $this->model->prepare($varyatnlar_toplami_sql);

                $query->execute([$this->stok_id]);

                $result = $query->fetch(PDO::FETCH_ASSOC);

                $toplam = 0;

                $toplam = $result["toplam"];

                $update_parent_query = $this->model->prepare("UPDATE stok  SET stok_adet = ? WHERE id = ? ");

                $update_parent_query->execute([$toplam,$this->stok_id]);


            }

        }



        return $this;


    }


}

/**
 *
 * @author Doğuş DİCLE
 */
class parakendeModel extends Model
{

    function __construct($lib = null, $config = null)
    {
        parent::__construct($lib, $config);
    }



    private $stok_update_select = " 
    stok.stok_barkod_no ,
    stok.stok_create_id ,
    stok.stok_satis_fiyati ,
    stok.stok_standart_adet , 
    stok.remove , 
    stok.id , 
    stok.stok_kod , 
    stok.stok_grup , 
    stok.id as stok_id ,
    stok.stok_parent_id as s_pid , 
    stok.stok_varyant_adi,
    stok.stok_varyant_deger,
    stok.stok_adet , 
    IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
    IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
    IF(lst.id IS NULL ,stok.stok_birimi,lst.stok_birimi) AS stok_birim_adi , 
    IF(lst.id IS NULL ,stok.stok_resim,lst.stok_resim) AS stok_resim , 
    IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_adi 
";


    private $stok_select = ""
    . "stok.id as stok_id ,"
    . "stok.stok_parent_id as s_pid , "
    . "stok.stok_kod , "
    . "stok.stok_varyant_adi,"
    . "stok.stok_varyant_deger,    
    IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
    IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
    IF(lst.id IS NULL ,stok.stok_birimi,lst.stok_birimi) AS stok_birim_adi , 
    IF(lst.id IS NULL ,stok.stok_resim,lst.stok_resim) AS stok_resim , 
       IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_adi  ";


    public function getId($owner)
    {

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner]);

        $q_last_id = $query->fetch();

        if ($q_last_id) {

            $last_id = $q_last_id["last_id"];

            return $last_id;

        } else {

            $updateQuery = $this->getConnection()->prepare("INSERT INTO stok_change_listener SET last_id = 1 ,  owner_id = ? ")->execute([$owner]);

            if ($updateQuery) {


                return 1;


            } else {
                return 0;
            }


        }
    }

    public function getUpdated($last_id, $owner)
    {

        $ek = "";

        if(isset($_POST["account_name"])){

            if($_POST["account_name"] == "avok_focali_av"){

                $ek = "  and stok.stok_adet > 0  ";

            }

        }

        $query = $this->getConnection()->prepare(""
            . "SELECT {$this->stok_update_select} "
            . "FROM stok   LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id   "
            . "WHERE stok.last_val > ? and stok.owner_id = ? ".$ek);

        $query->execute([$last_id, $owner]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTags($owner)
    {

        $query = $this->getConnection()->prepare("SELECT * FROM tagler WHERE remove = 0 and  owner_id = ? ");

        $query->execute([$owner]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBarcode($barcode, $owner)
    {

        $query = $this->getConnection()->prepare("SELECT {$this->stok_select} FROM stok  LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  WHERE stok.remove = 0 and stok.owner_id = ? and stok.stok_barkod_no = ? ");

        $query->execute([$owner, $barcode]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getArchiveBarcode($barcode)
    {


        try {
            $database_connection = new PDO(
                'mysql:host=' . $this->dbConfig["host"] . ';dbname=' .
                $this->dbConfig["masterDbName"] . '', $this->dbConfig["username"], $this->dbConfig["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

            $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = $database_connection->prepare("SELECT * FROM stok_arsiv WHERE remove = 0 and barcode = ? ");

            $query->execute([$barcode]);

            $result = $query->fetch(PDO::FETCH_ASSOC);

            $database_connection = NULL;

            return $result;

        } catch (PDOException $e) {

            echo "Veritabanı Bağantı Hatası!:" . $e->getMessage();

            return false;
        }
    }

    public function getCategoriesUpdated($last_id, $owner)
    {

        $query = $this->getConnection()->prepare(""
            . "SELECT * "
            . "FROM stok_gruplar "
            . "WHERE remove = 0 AND last_val > ?  and owner_id = ?  ");

        $query->execute([$last_id, $owner]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function cariAramaTelefonile($srt, $owner)
    {

        if ($srt == "" || empty($srt) || $srt == NULL) {

            return false;
        } else {
            $sql = "SELECT "
                . "* "
                . "FROM "
                . "cari "
                . "WHERE "
                . "remove = 0 and owner_id = :owner "
                . "and (cari_gsm = :str or cari_telefon = :str) LIMIT 1 ";


            $query = $this->getConnection()->prepare($sql);
            $query->BindParam(":owner", $owner, PDO::PARAM_STR);
            $query->BindValue(":str", $srt, PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                return $query->fetch();
            } else {

                return false;
            }
        }
    }

    public function cariArama($srt, $owner)
    {
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

    public function cariEkle($request)
    {
        $cari_adi_input = $request->input("cari_adi_input");
        $cari_yekili_input = $request->input("cari_yekili_input");
        $cari_telefon_input = $request->input("cari_telefon_input");
        $cari_mail_input = $request->input("cari_mail_input");

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
            . "created_date,"
            . "cari_vergi_no,"
            . "cari_vergi_daire"
            . ") VALUES (?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);
        $result = $query->execute([
            $kod,
            $cari_adi_input,
            $cari_telefon_input,
            $cari_fatura_adres_input,
            $cari_mail_input,
            $owner_id,
            $date,
            $cari_vergino_input,
            $cari_vergi_daire_input
        ]);

        if ($result) {

            return $this->getConnection()->lastInsertId();
        } else {
            return "caridontsave";
        }
    }

    public function parakendeSatisCikis($request , $api)
    {


        $helper = new stokAdetHelper();

        $user_info = $api->getUserData();


        $postdata = $request->getAll();

        $serial_no = $request->input("device_serial");

        $sale_uniq = $request->input("sale_uniq_code");

        $cihaz_sql = "SELECT * FROM yazilimlar WHERE serial_code = ? and remove = 0";

        $cihaz_query = $this->getConnection()->prepare($cihaz_sql);

        $cihaz_query->execute([$serial_no]);

        $yazilim_ayari = $cihaz_query->fetch();

        if ($yazilim_ayari) {

            $kayit_kontrol = $this->getConnection()->prepare("SELECT id FROM satis_evraklari WHERE uniq_id = ? and yazilim_serial = ? ");
            $kayit_kontrol->execute([$sale_uniq, $serial_no]);
            $kayit_kontrol_result = $kayit_kontrol->fetch(PDO::FETCH_ASSOC);

            if ($kayit_kontrol_result) {

                return [1];

            } else {


                $products_data = json_decode($request->input("selected_products"), true);

                if ($request->input("cikis_evrak_no") != NULL) {

                    $evrak_no = $request->input("cikis_evrak_no");
                } else {

                    $evrak_no = "prk-" . uniqid();
                }

                $cari_id = $request->input("cari_id");

                //Satış parakende ise standart cari hesabı seçicez
                if ($cari_id == null) {

                    $cari_id = $yazilim_ayari["master_parakende_cari_hesap_id"];
                }

                $cikis_tarih = date("Y-m-d H:i:s", strtotime($request->input("create_date")));

                $user_id = $request->input("user_id");

                $owner = $request->input("owner_id");

                $evrak = $request->input("evrak");

                $evrak_tur = 3;

                if ($evrak == "none") {

                    $evrak_tur = 3;

                } else if ($evrak == "0") {

                    $evrak_tur = 3;

                } else {

                    if ($request->input("evrak") == NULL) {

                        $evrak_tur = 3;

                    } else {

                        $evrak_tur = $request->input("evrak");
                    }

                }

                $cari_select_query = $this->getConnection()->prepare("SELECT * FROM cari WHERE id = ? ");
                $cari_select_query->execute([$cari_id]);
                $cari_result = $cari_select_query->fetch(PDO::FETCH_ASSOC);

                $tahsilat_ok = 0;

                if ($request->input("pay_ok") == 1) {

                    $vade_gun = 0;

                    $tahsilat_ok = 1;

                } else {


                    if ($request->input("vade_gun") != NULL) {

                        $vade_gun = $request->input("vade_gun");
                    } else {


                        $vade_gun = 0;


                        if (isset($cari_result["cari_vade_gun"])) {

                            $vade_gun = $cari_result["cari_vade_gun"];
                        }
                    }
                }


                if ($vade_gun > 0) {


                    $vade_tarih = date('Y-m-d', strtotime("+" . $vade_gun . " day"));
                } else if ($vade_gun == 0) {

                    $vade_tarih = date("Y-m-d");
                }


                $sql = "INSERT INTO satis_evraklari ("
                    . "evrak_no,"
                    . "tarih,"
                    . "cari_id,"
                    . "evrak_tur,"
                    . "owner_id,"
                    . "created_date,"
                    . "created_user,vade_gun,parakende_satis,"
                    . "vade_tarih,unvan,vergino,vergidaire,vergiadres,
                        tahsil_durum,uniq_id,yazilim_serial,evrak_zamani,update_date,updated_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $query = $this->getConnection()->prepare($sql);
                $result = $query->execute([
                    $evrak_no,
                    $cikis_tarih,
                    $cari_id,
                    $evrak_tur,
                    $owner,
                    $cikis_tarih,
                    $user_id,
                    $vade_gun,
                    1,
                    $vade_tarih,
                    $cari_result["cari_adi"],
                    $cari_result["cari_vergi_no"],
                    $cari_result["cari_vergi_daire"],
                    $cari_result["cari_adres"],
                    $tahsilat_ok,
                    $sale_uniq,
                    $serial_no,
                    $cikis_tarih,
                    date("Y-m-d"),
                    $user_id
                ]);

                $satis_evrak_id = $this->getConnection()->lastInsertId();


                $evrak_tutari = 0;
                $evrak_kdv1_tutar = 0;
                $evrak_kdv8_tutar = 0;
                $evrak_kdv18_tutar = 0;
                $evrak_indirim_tutar = 0;


                $mevcut_id_list = [];


                foreach ($products_data as $key => $value) {


                    $stok_id = $value["product_id"];
                    $miktar = $value["amount"];
                    $satis_fiyat = $value["price"];
                    $kdv_oran = $value["kdv_oran"];
                    $depo = $yazilim_ayari["cikis_yapacagi_depo_id"];
                    $seri_no = "";
                    $ozel_urun_id = 0;
                    $ozel_urun = 0;
                    $doviz = 0.0000;
                    $kur = 0.0000;
                    $anapara = 0.0000;

                    if (isset($value["doviz"])) {
                        $doviz = $value["doviz"];
                    }

                    if (isset($value["kur"])) {
                        $kur = $value["kur"];
                    }

                    if (isset($value["anapara"])) {

                        $anapara = $value["anapara"];
                    }

                    $iskonto_orani = 0;


                    if (isset($value["isk_oran"])) {
                        $iskonto_orani = $value["isk_oran"];
                    }


                    $indirim_tutari = 0;

                    if ($iskonto_orani > 0) {
                        $indirim_tutari = $satis_fiyat * $iskonto_orani / 100;
                    }


////////////////////////////////////

                    $evrak_tutari = $evrak_tutari + ($satis_fiyat * $miktar);

                    $evrak_indirim_tutar = $evrak_indirim_tutar + ($indirim_tutari * $miktar);

                    $ham_tutar = ($satis_fiyat - $indirim_tutari) * $miktar;

                    if ($kdv_oran == 1) {

                        $evrak_kdv1_tutar = $evrak_kdv1_tutar + ($ham_tutar * 1.01 - $ham_tutar);

                    } else if ($kdv_oran == 8) {

                        $evrak_kdv8_tutar = $evrak_kdv8_tutar + ($ham_tutar * 1.08 - $ham_tutar);

                    } else if ($kdv_oran == 18) {

                        $evrak_kdv18_tutar = $evrak_kdv18_tutar + ($ham_tutar * 1.18 - $ham_tutar);
                    }

                    ////////////////////////////////////

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
                        . "satis_evrak_id,"
                        . "doviz,"
                        . "doviz_kur,"
                        . "anapara,
                            iskonto,
                            indirim_tutari,raf,goz"
                        . ") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

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
                        $satis_evrak_id,
                        $doviz,
                        $kur,
                        $anapara,
                        $iskonto_orani,
                        $indirim_tutari,
                        0,
                        0
                    ]);


                    $helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);
                }


                $kdv_toplam = $evrak_kdv1_tutar + $evrak_kdv8_tutar + $evrak_kdv18_tutar;
                $genel_toplam = $kdv_toplam + ($evrak_tutari - $evrak_indirim_tutar);


                $satis_evraklari_query = $this->getConnection()->prepare("UPDATE satis_evraklari SET 
evrak_tutar = ? ,
kdv_1 = ? ,
kdv_8 = ? ,
kdv_18 = ? ,
kdv_toplam = ? ,
indirim_toplam = ? ,
genel_toplam  = ?  WHERE id = ?");
                $satis_evraklari_query->execute([
                    $evrak_tutari,
                    $evrak_kdv1_tutar,
                    $evrak_kdv8_tutar,
                    $evrak_kdv18_tutar,
                    $kdv_toplam,
                    $evrak_indirim_tutar,
                    $genel_toplam,
                    $satis_evrak_id

                ]);


                //Tahsilatlar

                $payments = json_decode($request->input("payments"), true);

                $kasa_paymet_sql = "";

                $toplam_nakit_tutar = 0;

                $toplam_kk_tutar = 0;

                $tarih = date("Y-m-d");

                $kasa_id = $user_info["kasa_id"];

                $standart_pos_hesap_id = $yazilim_ayari["pos_hesap_id"];

                $standart_pos_hesap_banka_id = $yazilim_ayari["pos_hesap_banka_id"];


                foreach ($payments as $p_key => $p_value) {

                    $tutar = $p_value[0];

                    $tip = $p_value[1];

                    $kk_hesap_id = $standart_pos_hesap_id;

                    $kk_banka_id = $standart_pos_hesap_banka_id;

                    $islem_id = 0;


                    if ($tip == "nakit") {

                        $kasa_paymet_sql = "INSERT INTO kasa_haraket "
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
                            . "VALUES ({$kasa_id},1,$cari_id,$tutar,\"$tarih\",$owner,$user_id,\"$cikis_tarih\",$satis_evrak_id)";

                        $payquery = $this->getConnection()->prepare($kasa_paymet_sql);

                        $payquery->execute();

                        $islem_id = $this->getConnection()->lastInsertId();


                        $tahsilat_query_sql = "INSERT INTO tahsilatlar (cari_id,islem_tip,islem_id,islem_tarih,islem_tutar,owner_id,satis_evrak_id) VALUES (?,?,?,?,?,?,?)";

                        $tahsilat_query = $this->getConnection()->prepare($tahsilat_query_sql);

                        $tahsilat_query->execute([$cari_id, "kasanakit", $islem_id, $tarih, $tutar, $owner, $satis_evrak_id]);


                    } else if ($tip == "kk") {

                        $hesap_mesaj = "perakende kk pos tahsilat";
                        $p_hatali_id = 0;


                        if (isset($p_value[2])) {

                            if ($p_value[2] > 0) {

                                $kk_hesap_id = $p_value[2];

                                $get_banka_id_sql = "SELECT banka_id FROM banka_hesaplari WHERE id = ?";

                                $banka_id_query = $this->getConnection()->prepare($get_banka_id_sql);

                                $banka_id_query->execute([$kk_hesap_id]);

                                $banka_id_query_result = $banka_id_query->fetch(PDO::FETCH_ASSOC);

                                if ($banka_id_query_result) {

                                    $kk_banka_id = $banka_id_query_result["banka_id"];
                                } else {


                                    $p_hatali_id = 1;

                                    $kk_hesap_id = $standart_pos_hesap_id;
                                    $kk_banka_id = $standart_pos_hesap_banka_id;

                                    $hesap_mesaj = "perakende kk pos tahsilat (hesap id hatalı standart id ye kaydedildi!)";
                                }
                            }
                        }


                        $banka_paymet_sql = "INSERT INTO banka_hareket "
                            . "("
                            . "banka_id,"
                            . "banka_haraket_tip,"
                            . "banka_haraket_cari_id,"
                            . "banka_haraket_tutar,"
                            . "banka_haraket_tarih,"
                            . "owner_id,"
                            . "created_user,"
                            . "created_date,"
                            . "satis_evrak_id,"
                            . "banka_hesap_id,"
                            . "banka_haraket_baslik,p_hatali_id"
                            . ") "
                            . "VALUES ({$kk_banka_id},1,$cari_id,$tutar,\"$tarih\",$owner,$user_id,\"$cikis_tarih\",$satis_evrak_id,{$kk_hesap_id},\"$hesap_mesaj\",$p_hatali_id)";

                        $bankapayquery = $this->getConnection()->prepare($banka_paymet_sql);

                        $bankapayquery->execute();

                        $islem_id = $this->getConnection()->lastInsertId();


                        $tahsilat_query_sql = "INSERT INTO tahsilatlar (cari_id,islem_tip,islem_id,islem_tarih,islem_tutar,owner_id,satis_evrak_id) VALUES (?,?,?,?,?,?,?)";

                        $tahsilat_query = $this->getConnection()->prepare($tahsilat_query_sql);

                        $tahsilat_query->execute([$cari_id, "banka", $islem_id, $tarih, $tutar, $owner, $satis_evrak_id]);
                    }
                }


                return true;
            }
        } else {

            return false;
        }
    }

    public function getCari($http_request)
    {

        $cari_id = $http_request->input("selected_account_id");
        $owner_id = $http_request->input("owner_id");

        $query = $this
            ->getConnection()
            ->prepare("SELECT * FROM cari WHERE  id = ? AND remove = 0 AND owner_id = ? ");

        $query->execute(array($cari_id, $owner_id));

        $cari_info = $query->fetch(PDO::FETCH_ASSOC);


        if ($cari_info) {


            $result = [
                'genel_toplam' => 0,
                'bakiye' => 0,
                'toplam_tahsilat' => 0,
                'alim_genel_toplam' => 0,
                'toplam_odeme' => 0,
                'toplam_borc' => 0,
                'toplam_alacak' => 0,
                'durum' => 'non',
                'durum_s' => 0
            ];


            foreach ($cari_info as $key => $value) {

                $result[$key] = $value;
            }


            if ($result["cari_adres"] == NULL) {
                $result["cari_adres"] = "";
            }


            /*
             * Alımlar
             */

            $alimtoplamSql = "SELECT SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)   as genel_toplam FROM stok_haraket_giris WHERE cari_id = ? AND remove = 0 and owner_id = ?";

            $aquery = $this->getConnection()->prepare($alimtoplamSql);

            $aquery->execute([$cari_id, $owner_id]);

            $toplam_alim = $aquery->fetch();

            if ($toplam_alim["genel_toplam"]) {
                $result["alim_genel_toplam"] = $toplam_alim["genel_toplam"];
            }


            /*
             * Satışlar
             */


            $toplamSql = "SELECT SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)   as genel_toplam FROM stok_haraket_cikis WHERE cari_id = ? AND remove = 0 and owner_id = ?";


            $query = $this->getConnection()->prepare($toplamSql);

            $query->execute([$cari_id, $owner_id]);

            $toplam_satis = $query->fetch();

            if ($toplam_satis) {
                $result["genel_toplam"] = $toplam_satis["genel_toplam"];
            }


            /*
             * ÖDemeler
             */

            $toplamOdemeSql = "SELECT  sum(odeme_tutar) as toplam_odeme_tutari "
                . "FROM odemeler "
                . "WHERE "
                . "cari_id = ? and  remove = 0 and owner_id = ?  ";

            $odemeQuery = $this->getConnection()->prepare($toplamOdemeSql);
            $odemeQuery->execute([$cari_id, $owner_id]);
            $toplam_odeme = $odemeQuery->fetch();


            if ($toplam_odeme['toplam_odeme_tutari']) {
                $result['toplam_odeme'] = $toplam_odeme['toplam_odeme_tutari'];
            }

            /*
             * Tahsilatlar
             */

            $toplamTahsilatSql = "SELECT  sum(islem_tutar) as toplam_tahsilat_tutari "
                . "FROM tahsilatlar "
                . "WHERE "
                . "cari_id = ? and  remove = 0 and owner_id = ?  ";

            $tahsilatQuery = $this->getConnection()->prepare($toplamTahsilatSql);
            $tahsilatQuery->execute([$cari_id, $owner_id]);
            $toplam_tahsilat = $tahsilatQuery->fetch();


            if ($toplam_tahsilat['toplam_tahsilat_tutari']) {
                $result['toplam_tahsilat'] = $toplam_tahsilat['toplam_tahsilat_tutari'];
            }

            $result['toplam_borc'] = $result["alim_genel_toplam"] - $result['toplam_odeme'];


            $result['toplam_alacak'] = $result["genel_toplam"] - $result['toplam_tahsilat'];


            $result['bakiye'] = $result['toplam_alacak'] - $result["toplam_borc"];


            if ($result['bakiye'] == 0) {

                $result['durum'] = "-";
            } else if ($result['bakiye'] > 0) {

                $result['durum'] = "alackli";

                $result['durum_s'] = 1;
            } else if ($result['bakiye'] < 0) {

                $result['durum'] = "brcl";

                $result['durum_s'] = 2;
            }


            return $result;
        } else {
            return false;
        }
    }

    public function faturaBilgileriniGuncelle($http_request)
    {

        $fatura_id = $http_request->input("fatura_id");
        $cari_id = $http_request->input("cari_id");
        $owner_id = $http_request->input("owner_id");
        $user = $http_request->input("user_id");
        $unvan = $http_request->input("unvan");
        $vergino = $http_request->input("vergino");
        $vergidaire = $http_request->input("vergidaire");
        $vergiadres = $http_request->input("vergiadres");
        $update_date = date("Y-m-d H:i:s");

        $update_sql = "UPDATE cari SET "
            . "cari_adi = ? ,"
            . "cari_vergi_no = ?,"
            . "cari_vergi_daire = ? ,"
            . "cari_adres = ? ,"
            . "update_date = ? WHERE id = ? AND owner_id = ? AND remove = 0 ";

        $query = $this->getConnection()->prepare($update_sql);
        return $query->execute(array($unvan, $vergino, $vergidaire, $vergiadres, $update_date, $cari_id, $owner_id));
    }

    public function offlineKullaniciAl($request)
    {


        $owner_id = $request->input("owner_id");
        $offline_code = $request->input("offline_code");

        $query = $this
            ->getConnection()
            ->prepare("SELECT * FROM users WHERE  remove = 0 AND owner_id = ? AND offline_code = ? AND offline_activate = 1");

        $query->execute(array($owner_id, $offline_code));
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function masaKategorileriAl($request)
    {

        $owner_id = $request->input("owner_id");
        $query = $this
            ->getConnection()
            ->prepare("SELECT * FROM masa_kategorileri WHERE   owner_id = ? ");

        $query->execute(array($owner_id));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function masalariAl($request)
    {


        $owner_id = $request->input("owner_id");


        $query = $this
            ->getConnection()
            ->prepare("SELECT * FROM masalar WHERE   owner_id = ? ");

        $query->execute(array($owner_id));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dovizKurlariAl($request)
    {

        $owner_id = $request->input("owner_id");
        $query = $this
            ->getConnection()
            ->prepare("SELECT * FROM doviz WHERE   owner_id = ? ");

        $query->execute(array($owner_id));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deployDb($db2_json, $device_type)
    {


        if ($device_type == "barkod") {

            $db_name = "avok_barkod";

        } else if ($device_type == "adisyon") {

            $db_name = "avok_adisyon";
        }


        $db1 = [
            'host' => $this->dbConfig["host"],
            'dbname' => $db_name,
            'username' => $this->dbConfig["username"],
            'password' => $this->dbConfig["password"]
        ];

        $sync = new \Dipa\Db\Dbsync();

        $sync->apiInit($db1, $db2_json);

        $control_result = $sync->compare();

        if ($control_result) {

            return $sync->getSql();
        } else {


            $sync_result = false;
        }
    }

    /*
     * Stok Ekleme İşlemleri
     */

    private function varyantParentKontrol($stok_kod, $owner_id)
    {

        $query = $this->getConnection()->prepare("SELECT id FROM stok WHERE remove = 0 and owner_id = ? and stok_kod = ? ");

        $query->execute([$owner_id, $stok_kod]);

        $stok = $query->fetch(PDO::FETCH_ASSOC);

        if ($stok) {

            return $stok["id"];
        } else {

            return 0;
        }
    }

    public function addStok($request)
    {


        $create_id = uniqid();
        $parabirimi = trim($request->input("ysk_parabirimi"));
        $barkod = trim($request->input("ysk_barkod"));
        $stokkod = trim($request->input("ysk_stokkod"));
        $stokadi = trim($request->input("ysk_stokadi"));
        $alisfiyati = trim($request->input("ysk_alisfiyati"));
        $satisfiyati = trim($request->input("ysk_satisfiyati"));
        $stokbirim = trim($request->input("ysk_stokbirim"));
        $vergiorani = trim($request->input("ysk_vergiorani"));
        $stok_mevcut = trim($request->input("ysk_stok_mevcut"));
        $stokgrubu = trim($request->input("ysk_stokgrubu"));
        $vergilerdahil = trim($request->input("ysk_vergilerdahil"));
        $varyant_adi = trim($request->input("ysk_varyant_adi"));
        $varyant_deger = trim($request->input("ysk_varyant_deger"));
        $varyant_ust_stok_kod = trim($request->input("ysk_varyant_ust_stok_kod"));
        $varyant_parent_id = 0;

        $seo_url = "";

        $owner_id = $request->input("owner_id");
        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");
        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"] + 1;


        if ($vergiorani > 9) {

            $kdv_duzelt = "1." . $vergiorani;
        } else {

            $kdv_duzelt = "1.0" . $vergiorani;
        }

        $max_iskontolu_satis_fiyat = $satisfiyati;


        if ($vergilerdahil == 1) {


            if ($vergiorani > 0) {

                $stok_kdv_dahil_satis_fiyati = $satisfiyati * $kdv_duzelt;
            } else {

                $stok_kdv_dahil_satis_fiyati = $satisfiyati;
            }
        } else if ($vergilerdahil == 2) {


            $stok_kdv_dahil_satis_fiyati = $satisfiyati;


            if ($vergiorani > 0) {


                if ($max_iskontolu_satis_fiyat > 0) {

                    $max_iskontolu_satis_fiyat = $max_iskontolu_satis_fiyat / $kdv_duzelt;
                }

                if ($alisfiyati > 0) {

                    $alisfiyati = $alisfiyati / $kdv_duzelt;
                }

                if ($satisfiyati > 0) {

                    $satisfiyati = $satisfiyati / $kdv_duzelt;
                }
            } else {

                $max_iskontolu_satis_fiyat = $satisfiyati;
                $stok_satis_fiyat = $satisfiyati;
                $stok_alis_fiyat = $alisfiyati;
            }
        }

        if ($varyant_ust_stok_kod != "") {

            $varyant_parent_id = $this->varyantParentKontrol($varyant_ust_stok_kod, $owner_id);
        }

        $insert_sql = "INSERT INTO stok SET  
               stok_barkod_no = ? , 
               stok_kod = ? ,
               stok_adi = ? ,
               stok_birimi = ? ,
               stok_grup = ? , 
               stok_alis_fiyati = ? ,
               stok_satis_fiyati = ? ,
               stok_max_iskontolu_satis_fiyati = ? , 
               stok_kdv_dahil_satis_fiyati = ? , 
               stok_kdv_oran = ? ,
               stok_resim = ? , 
               last_val = ? , 
               stok_create_id = ? ,
               stok_fiyat_vergi_durum = ? ,
               stok_doviz = ? ,
               owner_id = ? , 
               stok_seo_url = ? , 
               stok_parent_id = ? , 
               stok_varyant_adi = ? , 
               stok_varyant_deger = ? , 
               stok_parent_stok_kod = ? ";

        $insert_query = $this->getConnection()->prepare($insert_sql);
        $insert_query->execute([
            $barkod, $stokkod, $stokadi, $stokbirim, $stokgrubu,
            $alisfiyati, $satisfiyati, $max_iskontolu_satis_fiyat,
            $stok_kdv_dahil_satis_fiyati, $vergiorani, "noimage.jpg", $last_id,
            $create_id, $vergilerdahil, $parabirimi, $owner_id, $seo_url, $varyant_parent_id, $varyant_adi, $varyant_deger, $varyant_ust_stok_kod
        ]);

        $eklenen_stok_id = $this->getConnection()->lastInsertId();

        if ($stok_mevcut > 0) {

            $serial_no = $request->input("device_serial");

            $cihaz_sql = "SELECT * FROM yazilimlar WHERE serial_code = ? and remove = 0";

            $cihaz_query = $this->getConnection()->prepare($cihaz_sql);

            $cihaz_query->execute([$serial_no]);

            $yazilim_ayari = $cihaz_query->fetch();


            if ($yazilim_ayari) {

                $cari_id = $yazilim_ayari["master_parakende_cari_hesap_id"];
                $depo_id = $yazilim_ayari["cikis_yapacagi_depo_id"];
                $giris_tarih = date("Y-m-d H:i:s");
                $vade_tarih = date("Y-m-d");
                $evrak_no = "evr-" . +time();
                $vade_gun = 0;
                $evrak_tur = 0;

                $cari_select_query = $this->getConnection()->prepare("SELECT * FROM cari WHERE id = ? ");
                $cari_select_query->execute([$cari_id]);
                $cari_data = $cari_select_query->fetch(PDO::FETCH_ASSOC);


                $alim_evrak_sql = "INSERT INTO alim_evraklari ("
                    . "evrak_no,"
                    . "tarih,"
                    . "cari_id,"
                    . "evrak_tur,"
                    . "owner_id,"
                    . "created_date,"
                    . "created_user,"
                    . "vade_gun,"
                    . "vade_tarih,"
                    . "unvan,"
                    . "vergino,"
                    . "vergidaire,"
                    . "vergiadres,"
                    . "evrak_zamani) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $alim_evrak_query = $this->getConnection()->prepare($alim_evrak_sql);
                $alim_evrak_result = $alim_evrak_query->execute([
                    $evrak_no,
                    $giris_tarih,
                    $cari_id,
                    $evrak_tur,
                    $owner_id,
                    $giris_tarih,
                    0, //USer id
                    $vade_gun,
                    $vade_tarih,
                    $cari_data["cari_adi"],
                    $cari_data["cari_vergi_no"],
                    $cari_data["cari_vergi_daire"],
                    $cari_data["cari_adres"],
                    $giris_tarih
                ]);
                $alim_evrak_id = $this->getConnection()->lastInsertId();


                $alim_evrak_aklem_sql = "INSERT INTO stok_haraket_giris SET "
                    . "giris_tarih = ? , "
                    . "giris_evrak_no = ? , "
                    . "stok_id = ? , "
                    . "adet = ? ,  "
                    . "cari_id = ? , "
                    . "ozel_urun = ? ,"
                    . "depo = ? , "
                    . "alim_evrak_id = ? ";
                $alim_evrak_kalem_query = $this->getConnection()->prepare($alim_evrak_aklem_sql);
                $alim_evrak_kalem_query->execute([
                    $giris_tarih, $evrak_no, $eklenen_stok_id, $stok_mevcut, $cari_id, 0, $depo_id, $alim_evrak_id
                ]);


            }
        }

        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);

        return true;
    }

}
