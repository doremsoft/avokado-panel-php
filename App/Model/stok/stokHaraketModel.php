<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class stokHaraketmodel extends Dimodel
{

    private  $helper;

    public function __construct($lib = null, $config = null)
    {
        parent::__construct($lib, $config);


        $this->helper = Controller::helper(null,"stokAdetHelper");
    }



    private $stok_varyant_select = ""
    . "stok.id , "
    . "stok.stok_kod , "
    . "stok.stok_adet  , "
    . "stok.stok_alis_fiyati  , "
    . "stok.stok_satis_fiyati , "
    . "stok.stok_max_iskontolu_satis_fiyati ,"
    . "stok_fiyat_vergi_durum , "
    . "stok.stok_parent_id as s_pid , "
    . "stok.stok_varyant_adi,"
    . "stok.stok_varyant_deger,"
    . "IF(stok.stok_parent_id=0,stok.stok_kdv_oran,(select stok_kdv_oran from stok where id = s_pid LIMIT 1)) AS stok_kdv_oran , "
    . "IF(stok.stok_parent_id=0,stok.stok_doviz,(select stok_doviz from stok where id = s_pid LIMIT 1)) AS stok_doviz , "
    . "IF(stok.stok_parent_id=0,stok.stok_birimi,(select stok_birimi from stok where id = s_pid LIMIT 1)) AS stok_birim_adi , "
    . "IF(stok.stok_parent_id=0,stok.stok_adi,(select stok_adi from stok where id = s_pid LIMIT 1)) AS stok_adi ";


    public function getAlimEvrak($evrak_id)
    {

        return $this->table("alim_evraklari", Controller::$userInfo)->find($evrak_id, TRUE);
    }

    public function getCikisEvrak($evrak_id)
    {

        return $this->table("satis_evraklari", Controller::$userInfo)->find($evrak_id, TRUE);
    }


    public function getsatisEvrakKalemler($evrak_id)
    {


        $sql = "SELECT {$this->stok_varyant_select}   , 
stok_haraket_cikis.satis_fiyati  as stok_satis_fiyati , 
stok_haraket_cikis.doviz  as stok_satis_doviz , 
stok_haraket_cikis.adet as stok_standart_adet ,
stok_haraket_cikis.iskonto as stok_alim_iskonto_oran ,
stok_haraket_cikis.depo as depo  ,
stok_haraket_cikis.vergi_durum ,
stok_haraket_cikis.adet_etkisiz ,
stok_haraket_cikis.id as shgid 
FROM stok_haraket_cikis 
INNER JOIN stok ON stok_haraket_cikis.stok_id = stok.id  
WHERE stok_haraket_cikis.satis_evrak_id = ? and stok_haraket_cikis.remove = 0 ";

        $query = $this->getConnection()->prepare($sql);
        $query->execute([$evrak_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAlimEvrakKalemler($evrak_id)
    {


        $sql = "SELECT {$this->stok_varyant_select}   , 

stok_haraket_giris.vergi_durum , 
stok_haraket_giris.alis_fiyati  as stok_alis_fiyati , 
stok_haraket_giris.adet as stok_standart_adet ,
stok_haraket_giris.iskonto as stok_alim_iskonto_oran ,
stok_haraket_giris.depo as depo  ,
stok_haraket_giris.vergi_durum  ,
stok_haraket_giris.doviz as stok_alim_doviz  
FROM stok_haraket_giris 
INNER JOIN stok ON stok_haraket_giris.stok_id = stok.id  
WHERE stok_haraket_giris.alim_evrak_id = ? and stok_haraket_giris.remove = 0 ";

        $query = $this->getConnection()->prepare($sql);
        $query->execute([$evrak_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCari($id)
    {
        return $this->table("cari", Controller::$userInfo)->find($id, TRUE);
    }

    public function depolariGetir()
    {

        return $this->reset()->table("stok_depolar", Controller::$userInfo)->getAll();
    }

    public function girisEkle($request)
    {
        $postdata = $request->getAll();

        $data = $request->input("data");

        $cari_id = $request->input("cari_id");

        $cari_data = [];
        $cari_data["cari_adi"] = $request->input("cari_unvan");
        $cari_data["cari_vergi_no"] = $request->input("cari_vergi_no");
        $cari_data["cari_vergi_daire"] = $request->input("cari_vergi_daire");
        $cari_data["cari_adres"] =$request->input("cari_vergi_adres");





        if ($request->input("giris_evrak_no") != NULL) {

            $evrak_no = $request->input("giris_evrak_no");

        } else {
            $evrak_no = "evr-" . +time();
        }


        $evrak = $request->input("evrak_tur");


        $evrak_tur = 0;

        if ($evrak == "none") {

            $evrak_tur = 0;

        } else {
            $evrak_tur = $evrak;
        }


        if ($request->input("vade_gun") != NULL) {

            $vade_gun = $request->input("vade_gun");

            $vade_tarih = date("Y-m-d", strtotime("+" . $vade_gun . " day"));
        } else {

            $vade_tarih = date("Y-m-d");

            $vade_gun = 0;
        }


        $time = date("H:i:s");

        $giris_tarih = date("Y-m-d H:i:s", strtotime($request->input("giris_haraket_tarih") . " $time"));

        $evrak_detayi = $request->input("evrak_detay");

        $sql = "INSERT INTO alim_evraklari ("
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
            . "vergiadres,evrak_zamani,evrak_detayi) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);
        $result = $query->execute([
            $evrak_no, $giris_tarih, $cari_id, $evrak_tur,
            Controller::$userInfo["owner_id"], $giris_tarih, Controller::$userInfo["id"], $vade_gun, $vade_tarih, $cari_data["cari_adi"],
            $cari_data["cari_vergi_no"],
            $cari_data["cari_vergi_daire"],
            $cari_data["cari_adres"],
            $giris_tarih,
            $evrak_detayi
        ]);

        $alim_evrak_id = $this->getConnection()->lastInsertId();

        $ae_evrak_tutari = 0;
        $ae_evrak_kdv1_tutar = 0;
        $ae_evrak_kdv8_tutar = 0;
        $ae_evrak_kdv18_tutar = 0;
        $ae_evrak_indirim_tutar = 0;

        $evrak_tutari = 0;

        if (is_array($data)) {

            $kalem_insert_sql = "INSERT INTO stok_haraket_giris (
seri_no,
giris_tarih,
giris_evrak_no,
stok_id,
adet,
alis_fiyati,
kdv_oran,
cari_id,
ozel_urun,
depo,
alim_evrak_id,
iskonto,
indirim_tutari,
vergi_tutari,
owner_id,
vergi_durum,doviz,doviz_kur)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $owner_id = Controller::$userInfo["owner_id"];

            $insert_query = $this->getConnection()->prepare($kalem_insert_sql);

            foreach ($data as $key => $value) {
                $indirim_tutari = 0;
                $stok_id = $value["stokid"];
                $seri_no = $value["serino"];
                $miktar = $value["miktar"];
                $alis_fiyat = $value["alis_fiyat"];
                $kdv_oran = $value["kdv_oran"];
                $toplam = $value["toplam"];
                $kdv_tip = $value["kdv_tip"];
                $depo = $value["depo_id"];
                $iskontooran = $value["iskontooran"];


                $doviz_adi = $value["alim_doviz"];
                $doviz_kur = $value["doviz_kur"];

                $alis_fiyat = $alis_fiyat - $indirim_tutari;

                $vergi_tutari = 0;

                if ($kdv_oran > 9) {

                    $kdv_oran_duzelt = (float)"1.$kdv_oran";

                } else {

                    $kdv_oran_duzelt = (float)"1.0$kdv_oran";
                }


                $evrak_birim_tutar = $alis_fiyat * $miktar;


                if ($kdv_oran > 0) {

                    //KDV DAHİL
                    if ($kdv_tip == 1) {

                        $vergisiz_alis_fiyat = $alis_fiyat / $kdv_oran_duzelt;

                        $alis_fiyat = $vergisiz_alis_fiyat;


                    } else {


                        $evrak_birim_tutar = $evrak_birim_tutar * $kdv_oran_duzelt;

                    }

                }


                $evrak_tutari = $evrak_tutari + $evrak_birim_tutar;


                if ($iskontooran > 0) {

                    $indirim_tutari = $alis_fiyat * $iskontooran / 100;

                }


                $serino_kontrol = str_replace(" ", "", $seri_no);

                if ($serino_kontrol == "" || $serino_kontrol == " ") {

                    $ozel_urun = 0;
                } else {

                    $ozel_urun = 1;
                }

                if ($ozel_urun == 1) {

                    $miktar = 1;
                }


                ///////////////////////

                $ae_evrak_tutari = $ae_evrak_tutari + ($alis_fiyat * $miktar);

                $ae_evrak_indirim_tutar = $ae_evrak_indirim_tutar + ($indirim_tutari * $miktar);

                $ham_tutar = ($alis_fiyat - $indirim_tutari) * $miktar;

                if ($kdv_oran == 1) {

                    $ae_evrak_kdv1_tutar = $ae_evrak_kdv1_tutar +  ($ham_tutar * 1.01 - $ham_tutar);

                } else if ($kdv_oran == 8) {

                    $ae_evrak_kdv8_tutar = $ae_evrak_kdv8_tutar +  ($ham_tutar * 1.08 - $ham_tutar);

                } else if ($kdv_oran == 18) {

                    $ae_evrak_kdv18_tutar = $ae_evrak_kdv18_tutar +  ($ham_tutar * 1.18 - $ham_tutar);
                }

                ///////////////////////

                $insert_query->execute([
                    $seri_no,
                    $giris_tarih,
                    $evrak_no,
                    $stok_id,
                    $miktar,
                    $alis_fiyat,
                    $kdv_oran,
                    $cari_id,
                    $ozel_urun,
                    $depo,
                    $alim_evrak_id,
                    $iskontooran,
                    $indirim_tutari,
                    0,
                    $owner_id,
                    $kdv_tip,
                    $doviz_adi,
                    $doviz_kur
                ]);


                $this->helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);
            }
        }



        //Evrak Ödemesi


        $evrak_odeme_yontemi = $request->input("evrak-odeme-yontemi");
        $evrak_odeme_yontemi_kanali = $request->input("evrak-odeme-yontemi-kanali");

        if ($evrak_odeme_yontemi == 1 && $evrak_odeme_yontemi_kanali > 0) {

            $result = $this->reset()->table("kasa_haraket", Controller::$userInfo)
                ->col("kasa_id", $evrak_odeme_yontemi_kanali)
                ->col("kasa_haraket_tip", 2)
                ->col("kasa_haraket_cari_id", $cari_id)
                ->col("kasa_haraket_tutar", $evrak_tutari)
                ->col("kasa_haraket_tarih", $giris_tarih)
                ->col("kasa_haraket_not", $evrak_no . " - Alım faturasına ödeme")
                ->save_();

            if ($result) {

                $islem_id = $this->getConnection()->lastInsertId();

                $result = $this->reset()->table("odemeler", Controller::$userInfo)
                    ->col("cari_id", $cari_id)
                    ->col("odeme_tip", "kasanakit")
                    ->col("odeme_islem_id", $islem_id)
                    ->col("odeme_tutar", $evrak_tutari)
                    ->col("odeme_tarih", $giris_tarih)
                    ->col("alim_evrak_id", $alim_evrak_id)
                    ->save_();
            }


        } else if ($evrak_odeme_yontemi == 2 && $evrak_odeme_yontemi_kanali > 0) {


            $banka_id = 0;

            $banka_hesabi = $this->reset()->table("banka_hesaplari", Controller::$userInfo)
                ->find($evrak_odeme_yontemi_kanali)->get();


            $banka_id = $banka_hesabi["banka_id"];

            $result = $this->reset()->table("banka_hareket", Controller::$userInfo)
                ->col("banka_id", $banka_id)
                ->col("banka_haraket_tip", 2)
                ->col("banka_haraket_cari_id", $cari_id)
                ->col("banka_haraket_tutar", $evrak_tutari)
                ->col("banka_haraket_tarih", $giris_tarih)
                ->col("alim_evrak_id", $alim_evrak_id)
                ->col("banka_haraket_baslik", $evrak_no . " - Alım faturasına ödeme")
                ->col("banka_hesap_id", $evrak_odeme_yontemi_kanali)
                ->save_();


            if ($result) {

                $islem_id = $this->getConnection()->lastInsertId();

                $result = $this->reset()->table("odemeler", Controller::$userInfo)
                    ->col("cari_id", $cari_id)
                    ->col("odeme_tip", "banka")
                    ->col("odeme_islem_id", $islem_id)
                    ->col("odeme_tutar", $evrak_tutari)
                    ->col("odeme_tarih", $giris_tarih)
                    ->col("alim_evrak_id", $alim_evrak_id)
                    ->save_();
            }


        }


        $ae_kdv_toplam = $ae_evrak_kdv1_tutar + $ae_evrak_kdv8_tutar + $ae_evrak_kdv18_tutar;
        $ae_genel_toplam = $ae_kdv_toplam + ($ae_evrak_tutari - $ae_evrak_indirim_tutar);


        $query = $this->getConnection()->prepare("UPDATE alim_evraklari SET 
evrak_tutar = ? ,
kdv_1 = ? ,
kdv_8 = ? ,
kdv_18 = ? ,
kdv_toplam = ? ,
indirim_toplam = ? ,
genel_toplam  = ?  WHERE id = ?");
        $query->execute([
            $ae_evrak_tutari,
            $ae_evrak_kdv1_tutar,
            $ae_evrak_kdv8_tutar,
            $ae_evrak_kdv18_tutar,
            $ae_kdv_toplam,
            $ae_evrak_indirim_tutar,
            $ae_genel_toplam,
            $alim_evrak_id
        ]);


        return true;
    }


    public function girisGuncelle($request)
    {


        if (isset($_POST["data"])) {
            $data = $_POST["data"];
        } else {

            $data = [];
        }


        $cari_id = $request->input("cari_id");

        $cari_data = [];
        $cari_data["cari_adi"] = $request->input("cari_unvan");
        $cari_data["cari_vergi_no"] = $request->input("cari_vergi_no");
        $cari_data["cari_vergi_daire"] = $request->input("cari_vergi_daire");
        $cari_data["cari_adres"] =$request->input("cari_vergi_adres");



        if ($request->input("giris_evrak_no") != NULL) {

            $evrak_no = $request->input("giris_evrak_no");

        } else {

            $evrak_no = "evr-" . +time();
        }


        $evrak = $request->input("evrak_tur");


        $evrak_tur = 0;

        if ($evrak == "none") {

            $evrak_tur = 0;
        } else {
            $evrak_tur = $evrak;
        }

        if ($request->input("vade_gun") != NULL) {

            $vade_gun = $request->input("vade_gun");

            $vade_tarih = date("Y-m-d", strtotime("+" . $vade_gun . " day"));
        } else {

            $vade_tarih = date("Y-m-d");

            $vade_gun = 0;
        }


        $time = date("H:i:s");
        $giris_tarih = date("Y-m-d H:i:s", strtotime($request->input("giris_haraket_tarih") . " $time"));
        $alim_evrak_id = $request->input("evrak_id");
        $evrak_detayi = $request->input("evrak_detay");


        $alimupdatesql = "UPDATE alim_evraklari SET "
            . "evrak_no = ?,"
            . "tarih= ?,"
            . "cari_id= ?,"
            . "evrak_tur= ?,"
            . "update_date= ?,"
            . "updated_user= ?,"
            . "unvan= ?,"
            . "vergino= ?,"
            . "vergidaire= ?,"
            . "vergiadres= ?,
            evrak_zamani= ? ,
             evrak_tur = ?  , 
             evrak_detayi = ? 
            WHERE id = ?";

        $query = $this->getConnection()->prepare($alimupdatesql);
        $result = $query->execute([
            $evrak_no,
            $giris_tarih,
            $cari_id,
            $evrak_tur,
            $giris_tarih,
            Controller::$userInfo["id"],
            $cari_data["cari_adi"],
            $cari_data["cari_vergi_no"],
            $cari_data["cari_vergi_daire"],
            $cari_data["cari_adres"],
            $giris_tarih,
            $evrak_tur,
            $evrak_detayi,
            $alim_evrak_id
        ]);




        $old_update_id_list = [];


        $remove_sql_get = "SELECT stok_id  FROM stok_haraket_giris  WHERE alim_evrak_id = ? and owner_id = ? and remove = 0 ";
        $remove_sql_get_query = $this->getConnection()->prepare($remove_sql_get);
        $remove_sql_get_query->execute([$alim_evrak_id, Controller::$userInfo["owner_id"]]);
        $old_update_id_list = $remove_sql_get_query->fetchAll(PDO::FETCH_ASSOC);


        $remove_sql = "UPDATE stok_haraket_giris SET remove = 1 WHERE alim_evrak_id = ? and owner_id = ? ";
        $queryd = $this->getConnection()->prepare($remove_sql);
        $queryd->execute([$alim_evrak_id, Controller::$userInfo["owner_id"]]);




        $ae_evrak_tutari = 0;
        $ae_evrak_kdv1_tutar = 0;
        $ae_evrak_kdv8_tutar = 0;
        $ae_evrak_kdv18_tutar = 0;
        $ae_evrak_indirim_tutar = 0;


        if (is_array($data)) {

            $kalem_insert_sql = "INSERT INTO stok_haraket_giris (
seri_no,
giris_tarih,
giris_evrak_no,
stok_id,
adet,
alis_fiyati,
kdv_oran,
cari_id,
ozel_urun,
depo,
alim_evrak_id,
iskonto,
indirim_tutari,
vergi_tutari,
owner_id,
vergi_durum,doviz,doviz_kur)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $owner_id = Controller::$userInfo["owner_id"];

            $insert_query = $this->getConnection()->prepare($kalem_insert_sql);


            foreach ($data as $key => $value) {
                try {


                    $indirim_tutari = 0;
                    $stok_id = $value["stokid"];
                    $seri_no = $value["serino"];
                    $miktar = $value["miktar"];
                    $alis_fiyat = $value["alis_fiyat"];
                    $kdv_oran = $value["kdv_oran"];
                    $toplam = $value["toplam"];
                    $kdv_tip = $value["kdv_tip"];
                    $depo = $value["depo_id"];
                    $iskontooran = $value["iskontooran"];
                    $alis_fiyat = $alis_fiyat - $indirim_tutari;
                    $vergi_tutari = 0;
                    $doviz_adi = $value["alim_doviz"];
                    $doviz_kur = $value["doviz_kur"];

                    if ($kdv_oran > 9) {

                        $kdv_oran_duzelt = (float)"1.$kdv_oran";

                    } else {

                        $kdv_oran_duzelt = (float)"1.0$kdv_oran";
                    }


                    if ($kdv_oran > 0) {

                        //KDV DAHİL
                        if ($kdv_tip == 1) {

                            $vergisiz_alis_fiyat = $alis_fiyat / $kdv_oran_duzelt;

                            $alis_fiyat = $vergisiz_alis_fiyat;


                        }

                    }


                    if ($iskontooran > 0) {

                        $indirim_tutari = $alis_fiyat * $iskontooran / 100;

                    }


                    $serino_kontrol = str_replace(" ", "", $seri_no);

                    if ($serino_kontrol == "" || $serino_kontrol == " ") {

                        $ozel_urun = 0;
                    } else {

                        $ozel_urun = 1;
                    }

                    if ($ozel_urun == 1) {

                        $miktar = 1;
                    }



                    ///////////////////////

                    $ae_evrak_tutari = $ae_evrak_tutari + ($alis_fiyat * $miktar);

                    $ae_evrak_indirim_tutar = $ae_evrak_indirim_tutar + ($indirim_tutari * $miktar);

                    $ham_tutar = ($alis_fiyat - $indirim_tutari) * $miktar;

                    if ($kdv_oran == 1) {

                        $ae_evrak_kdv1_tutar = $ae_evrak_kdv1_tutar +  ($ham_tutar * 1.01 - $ham_tutar);

                    } else if ($kdv_oran == 8) {

                        $ae_evrak_kdv8_tutar = $ae_evrak_kdv8_tutar +  ($ham_tutar * 1.08 - $ham_tutar);

                    } else if ($kdv_oran == 18) {

                        $ae_evrak_kdv18_tutar = $ae_evrak_kdv18_tutar +  ($ham_tutar * 1.18 - $ham_tutar);
                    }

                    ///////////////////////


                    $insert_query->execute([
                        $seri_no,
                        $giris_tarih,
                        $evrak_no,
                        $stok_id,
                        $miktar,
                        $alis_fiyat,
                        $kdv_oran,
                        $cari_id,
                        $ozel_urun,
                        $depo,
                        $alim_evrak_id,
                        $iskontooran,
                        $indirim_tutari,
                        0,
                        $owner_id,
                        $kdv_tip,
                        $doviz_adi,
                        $doviz_kur
                    ]);


                    $this->helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);


                } catch (Exception $e) {


                    var_dump($e->getMessage());

                    return false;

                }


            }

        }

            $ae_kdv_toplam = $ae_evrak_kdv1_tutar + $ae_evrak_kdv8_tutar + $ae_evrak_kdv18_tutar;
            $ae_genel_toplam = $ae_kdv_toplam + ($ae_evrak_tutari - $ae_evrak_indirim_tutar);

            $query = $this->getConnection()->prepare("UPDATE alim_evraklari SET 
evrak_tutar = ? ,
kdv_1 = ? ,
kdv_8 = ? ,
kdv_18 = ? ,
kdv_toplam = ? ,
indirim_toplam = ? ,
genel_toplam  = ?   WHERE id = ?");
            $query->execute([
                $ae_evrak_tutari,
                $ae_evrak_kdv1_tutar,
                $ae_evrak_kdv8_tutar,
                $ae_evrak_kdv18_tutar,
                $ae_kdv_toplam,
                $ae_evrak_indirim_tutar,
                $ae_genel_toplam,
                $alim_evrak_id
            ]);


            if(is_array($old_update_id_list)){

                foreach ($old_update_id_list as $key_aa => $val_aa){

                    $this->helper->set($this->getConnection() ,$val_aa["stok_id"])->count(true)->reset(true);

                }

            }



        return true;


    }

    public function cikisEkle($request , $siparisten_fatura = 0)
    {
        $postdata = $request->getAll();

        $data = $request->input("data");
        $cari_id = $request->input("cari_id");



        $cari_data = [];
        $cari_data["cari_adi"] = $request->input("cari_unvan");
        $cari_data["cari_vergi_no"] = $request->input("cari_vergi_no");
        $cari_data["cari_vergi_daire"] = $request->input("cari_vergi_daire");
        $cari_data["cari_adres"] =$request->input("cari_vergi_adres");




        $evrak_detayi = $request->input("evrak_detay");


        $table = $this->reset()->table("stok_haraket_cikis", Controller::$userInfo);

        if ($request->input("cikis_evrak_no") != NULL) {

            $evrak_no = $request->input("cikis_evrak_no");
        } else {

            $evrak_no = "ck-e-" . +time();
        }


        $time = date("H:i:s");

        $giris_tarih = date("Y-m-d H:i:s", strtotime($request->input("cikis_haraket_tarih") . " $time"));

        $cikis_tarih = date("Y-m-d H:i:s", strtotime($request->input("cikis_haraket_tarih") . " $time"));

        $evrak = $request->input("evrak");

        $evrak = $request->input("evrak_tur");

        $evrak_tur = 0;

        if ($evrak == "none") {

            $evrak_tur = 0;

        } else {
            $evrak_tur = $evrak;
        }


        $siparis_kod = "";



        if ($request->input("vade_gun") != NULL) {

            $vade_gun = $request->input("vade_gun");

            $vade_tarih = date("Y-m-d", strtotime("+" . $vade_gun . " day"));
        } else {

            $vade_tarih = date("Y-m-d");

            $vade_gun = 0;
        }


        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        $uniqdate = $d->format("dHisu");

        $siparis_stok = 0;

        if($request->input("evrak_tur") == "1"){

            $evrak_no = "spr-" . $uniqdate;
            $siparis_stok = 1;

        }

        $sql = "INSERT INTO satis_evraklari ("
            . "evrak_no,"
            . "tarih,"
            . "cari_id,"
            . "evrak_tur,"
            . "owner_id,"
            . "created_date,"
            . "created_user,"
            . "vade_gun,"
            . "parakende_satis,"
            . "vade_tarih,"
            . "unvan,"
            . "vergino,"
            . "vergidaire,"
            . "vergiadres,"
            . "uniq_id,"
            . "evrak_zamani,
            evrak_detayi
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);
        $result = $query->execute([
            $evrak_no,
            $cikis_tarih,
            $cari_id,
            $evrak_tur,
            Controller::$userInfo["owner_id"],
            $cikis_tarih,
            Controller::$userInfo["id"],
            $vade_gun, 0,
            $vade_tarih,
            $cari_data["cari_adi"],
            $cari_data["cari_vergi_no"],
            $cari_data["cari_vergi_daire"],
            $cari_data["cari_adres"],
            $uniqdate,
            $cikis_tarih,
            $evrak_detayi


        ]);

        $satis_evrak_id = $this->getConnection()->lastInsertId();


        $evrak_tutari = 0;
        $evrak_kdv1_tutar = 0;
        $evrak_kdv8_tutar = 0;
        $evrak_kdv18_tutar = 0;
        $evrak_indirim_tutar = 0;


        $mevcut_id_list = [];


        foreach ($data as $key => $value) {


     array_push($mevcut_id_list,$value["shgid"]);


            $stok_id = $value["stokid"];
            $seri_no = $value["serino"];
            $miktar = $value["miktar"];
            $satis_fiyat = $value["satis_fiyat"];
            $kdv_oran = $value["kdv_oran"];
            $toplam = $value["toplam"];
            $kdv_tip = $value["kdv_tip"];
            $depo = $value["depo_id"];
            $iskontooran = $value["iskontooran"];
            $indirim_tutari = 0;





            if (isset($value["ozel_urun_id"])) {

                $ozel_urun_id = $value["ozel_urun_id"];

            } else {
                $ozel_urun_id = 0;
            }


            if ($kdv_oran > 9) {

                $kdv_oran_duzelt = (float)"1.$kdv_oran";

            } else {

                $kdv_oran_duzelt = (float)"1.0$kdv_oran";
            }


            if ($kdv_oran > 0) {

                //KDV DAHİL
                if ($kdv_tip == 1) {

                    $vergisiz_satis_fiyat = $satis_fiyat / $kdv_oran_duzelt;

                    $satis_fiyat = $vergisiz_satis_fiyat;

                }

            }




            if ($iskontooran > 0) {

                $indirim_tutari = $satis_fiyat * $iskontooran / 100;

            }


            $serino_kontrol = str_replace(" ", "", $seri_no);

            if ($serino_kontrol == "" || $serino_kontrol == " ") {

                $ozel_urun = 0;
            } else {

                $ozel_urun = 1;
            }

            if ($ozel_urun == 1) {

                $miktar = 1;
            }


            ///////////////////////

                $evrak_tutari = $evrak_tutari + ($satis_fiyat * $miktar);

                $evrak_indirim_tutar = $evrak_indirim_tutar + ($indirim_tutari * $miktar);

                $ham_tutar = ($satis_fiyat - $indirim_tutari) * $miktar;

                if ($kdv_oran == 1) {

                    $evrak_kdv1_tutar = $evrak_kdv1_tutar +  ($ham_tutar * 1.01 - $ham_tutar);

                } else if ($kdv_oran == 8) {

                    $evrak_kdv8_tutar = $evrak_kdv8_tutar +  ($ham_tutar * 1.08 - $ham_tutar);

                } else if ($kdv_oran == 18) {

                    $evrak_kdv18_tutar = $evrak_kdv18_tutar +  ($ham_tutar * 1.18 - $ham_tutar);
                }
            $doviz_adi = $value["satis_doviz"];
            $doviz_kur = $value["doviz_kur"];


            $table->col("seri_no", $seri_no)
                ->col("cikis_tarih", $giris_tarih)
                ->col("cikis_evrak_no", $evrak_no)
                ->col("stok_id", $stok_id)
                ->col("adet", $miktar)
                ->col("satis_fiyati", $satis_fiyat)
                ->col("kdv_oran", $kdv_oran)
                ->col("cari_id", $cari_id)
                ->col("ozel_urun_id", $ozel_urun_id)
                ->col("depo", $depo)
                ->col("satis_evrak_id", $satis_evrak_id)
                ->col("iskonto", $iskontooran)
                ->col("indirim_tutari", $indirim_tutari)
                ->col("vergi_durum", $kdv_tip)
                ->col("siparis_stok",$siparis_stok)
                ->col("doviz",$doviz_adi)
                ->col("doviz_kur",$doviz_kur)
                ->save_();

            if ($ozel_urun == 1) {

                $query = $this->getConnection()->prepare("UPDATE stok_haraket_giris SET ozel_urun_durum = 1  WHERE id = :id");
                $update = $query->execute(array("id" => $ozel_urun_id));
            }


          //  $this->helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);


            unset($kdv_oran, $kdv_tip);
        }



        if($request->input("evrak_tur") == "1"){

            $siparis_kod = $satis_evrak_id.date("Hi");



        }

        $siparis_fatura_kod = "0";

        if($siparisten_fatura == 1){

            $mevcut_id_list_string = implode(",",$mevcut_id_list);

            if($mevcut_id_list_string != "0" && $mevcut_id_list_string != ""){

                $this->getConnection()->prepare("UPDATE stok_haraket_cikis SET adet_etkisiz = 1  WHERE id IN ({$mevcut_id_list_string})")->execute();

            }


            $siparis_kod = 0;
            $siparis_fatura_kod = $request->input("siparis_kod");




        }

        foreach ($data as $key => $value) {

            $this->helper->set($this->getConnection() ,$value["stokid"])->count(true)->reset(true);
        }

        $kdv_toplam = $evrak_kdv1_tutar + $evrak_kdv8_tutar + $evrak_kdv18_tutar;
        $genel_toplam = $kdv_toplam + ($evrak_tutari - $evrak_indirim_tutar);

        //Evrak Ödemesi


        $evrak_odeme_yontemi = $request->input("evrak-odeme-yontemi");

        $evrak_odeme_yontemi_kanali = $request->input("evrak-odeme-yontemi-kanali");

        if ($evrak_odeme_yontemi == 1 && $evrak_odeme_yontemi_kanali > 0) {

            $result = $this->reset()->table("kasa_haraket", Controller::$userInfo)
                ->col("kasa_id", $evrak_odeme_yontemi_kanali)
                ->col("kasa_haraket_tip", 1)
                ->col("kasa_haraket_cari_id", $cari_id)
                ->col("kasa_haraket_tutar", $genel_toplam)
                ->col("kasa_haraket_tarih", $giris_tarih)
                ->col("kasa_haraket_not", $evrak_no . " - Satış Evrak Tahsil")
                ->save_();

            if ($result) {

                $islem_id = $this->getConnection()->lastInsertId();

                $result = $this->reset()->table("tahsilatlar", Controller::$userInfo)
                    ->col("cari_id", $cari_id)
                    ->col("islem_tip", "kasanakit")
                    ->col("islem_id", $islem_id)
                    ->col("islem_tutar", $genel_toplam)
                    ->col("islem_tarih", $giris_tarih)
                    ->col("satis_evrak_id", $satis_evrak_id)
                    ->col("islem_mesaj", $evrak_no . " - Satış faturasından Tahsil")
                    ->save_();
            }


        } else if ($evrak_odeme_yontemi == 2 && $evrak_odeme_yontemi_kanali > 0) {


            $banka_id = 0;

            $banka_hesabi = $this->reset()->table("banka_hesaplari", Controller::$userInfo)->find($evrak_odeme_yontemi_kanali)->get();


            $banka_id = $banka_hesabi["banka_id"];

            $result = $this->reset()->table("banka_hareket", Controller::$userInfo)
                ->col("banka_id", $banka_id)
                ->col("banka_haraket_tip", 1)
                ->col("banka_haraket_cari_id", $cari_id)
                ->col("banka_haraket_tutar", $genel_toplam)
                ->col("banka_haraket_tarih", $giris_tarih)
                ->col("satis_evrak_id", $satis_evrak_id)
                ->col("banka_haraket_baslik", $evrak_no . " - Satış Evrak tahsil")
                ->col("banka_hesap_id", $evrak_odeme_yontemi_kanali)
                ->save_();


            if ($result) {

                $islem_id = $this->getConnection()->lastInsertId();

                $result = $this->reset()->table("tahsilatlar", Controller::$userInfo)
                    ->col("cari_id", $cari_id)
                    ->col("islem_tip", "banka")
                    ->col("islem_id", $islem_id)
                    ->col("islem_tutar", $genel_toplam)
                    ->col("islem_tarih", $giris_tarih)
                    ->col("satis_evrak_id", $satis_evrak_id)
                    ->col("islem_mesaj", $evrak_no . " - Satış Evrak Tahsil")
                    ->save_();
            }


        }




        $query = $this->getConnection()->prepare("UPDATE satis_evraklari SET 
evrak_tutar = ? ,
kdv_1 = ? ,
kdv_8 = ? ,
kdv_18 = ? ,
kdv_toplam = ? ,
indirim_toplam = ? ,
genel_toplam  = ? , 
siparis_kod = ? , 
siparis_fatura_kod = ? WHERE id = ?");
       return $query->execute([
            $evrak_tutari,
            $evrak_kdv1_tutar,
            $evrak_kdv8_tutar,
            $evrak_kdv18_tutar,
            $kdv_toplam,
            $evrak_indirim_tutar,
            $genel_toplam,
            $siparis_kod,
            $siparis_fatura_kod,
            $satis_evrak_id

        ]);

    }

    public function cikisGuncelle($request)
    {
        $postdata = $request->getAll();

        $data = $request->input("data");

        $cari_id = $request->input("cari_id");


        if($cari_id == null){
            $cari_id = 0;
        }


        $cari_data = [];
        $cari_data["cari_adi"] = $request->input("cari_unvan");
        $cari_data["cari_vergi_no"] = $request->input("cari_vergi_no");
        $cari_data["cari_vergi_daire"] = $request->input("cari_vergi_daire");
        $cari_data["cari_adres"] =$request->input("cari_vergi_adres");



        $table = $this->reset()->table("stok_haraket_cikis", Controller::$userInfo);

        if ($request->input("cikis_evrak_no") != NULL) {

            $evrak_no = $request->input("cikis_evrak_no");
        } else {

            $evrak_no = "ck-e-" . +time();
        }


        $time = date("H:i:s");

        $giris_tarih = date("Y-m-d H:i:s", strtotime($request->input("cikis_haraket_tarih") . " $time"));

        $cikis_tarih = date("Y-m-d H:i:s", strtotime($request->input("cikis_haraket_tarih") . " $time"));

        $evrak = $request->input("evrak");


        $evrak_tur = 0;

        if ($evrak == "none") {

            $evrak_tur = 0;
        }


        if ($request->input("vade_gun") != NULL) {

            $vade_gun = $request->input("vade_gun");

            $vade_tarih = date("Y-m-d", strtotime("+" . $vade_gun . " day"));
        } else {

            $vade_tarih = date("Y-m-d");

            $vade_gun = 0;
        }


        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        $uniqdate = $d->format("YmdHisu");


        $satis_evrak_id = $request->input("evrak_id");
        $remove_sql = "UPDATE stok_haraket_cikis SET remove = 1 WHERE satis_evrak_id = ? and owner_id = ? ";
        $queryd = $this->getConnection()->prepare($remove_sql);
        $queryd->execute([$satis_evrak_id, Controller::$userInfo["owner_id"]]);


        $siparis_stok = 0;

        $evrak_tur = $request->input("evrak_tur");

        if( $request->input("evrak_tur") == 1){
            $siparis_stok = 1;
        }


        $evrak_tutari = 0;
        $evrak_kdv1_tutar = 0;
        $evrak_kdv8_tutar = 0;
        $evrak_kdv18_tutar = 0;
        $evrak_indirim_tutar = 0;


        foreach ($data as $key => $value) {

            $stok_id = $value["stokid"];
            $seri_no = $value["serino"];
            $miktar = $value["miktar"];
            $satis_fiyat = $value["satis_fiyat"];
            $adetetkisiz = $value["adetetkisiz"];
            $kdv_oran = $value["kdv_oran"];
            $toplam = $value["toplam"];
            $kdv_tip = $value["kdv_tip"];
            $depo = $value["depo_id"];
            $iskontooran = $value["iskontooran"];
            $indirim_tutari = 0;


            if (isset($value["ozel_urun_id"])) {

                $ozel_urun_id = $value["ozel_urun_id"];
            } else {
                $ozel_urun_id = 0;
            }


            if ($kdv_oran > 9) {

                $kdv_oran_duzelt = (float)"1.$kdv_oran";

            } else {

                $kdv_oran_duzelt = (float)"1.0$kdv_oran";
            }


            if ($kdv_oran > 0) {

                //KDV DAHİL
                if ($kdv_tip == 1) {

                    $vergisiz_satis_fiyat = $satis_fiyat / $kdv_oran_duzelt;

                    $satis_fiyat = $vergisiz_satis_fiyat;


                }

            }


            if ($iskontooran > 0) {

                $indirim_tutari = $satis_fiyat * $iskontooran / 100;

            }


            $serino_kontrol = str_replace(" ", "", $seri_no);

            if ($serino_kontrol == "" || $serino_kontrol == " ") {

                $ozel_urun = 0;
            } else {

                $ozel_urun = 1;
            }

            if ($ozel_urun == 1) {

                $miktar = 1;
            }


            ///////////////////////
            $evrak_tutari = $evrak_tutari + ($satis_fiyat * $miktar);

            $evrak_indirim_tutar = $evrak_indirim_tutar + ($indirim_tutari * $miktar);

            $ham_tutar = ($satis_fiyat - $indirim_tutari) * $miktar;

            if ($kdv_oran == 1) {

                $evrak_kdv1_tutar = $evrak_kdv1_tutar +  ($ham_tutar * 1.01 - $ham_tutar);

            } else if ($kdv_oran == 8) {

                $evrak_kdv8_tutar = $evrak_kdv8_tutar +  ($ham_tutar * 1.08 - $ham_tutar);

            } else if ($kdv_oran == 18) {

                $evrak_kdv18_tutar = $evrak_kdv18_tutar +  ($ham_tutar * 1.18 - $ham_tutar);
            }

            ///////////////////////

            $doviz_adi = $value["satis_doviz"];
            $doviz_kur = $value["doviz_kur"];

            $table->col("seri_no", $seri_no)
                ->col("cikis_tarih", $giris_tarih)
                ->col("cikis_evrak_no", $evrak_no)
                ->col("stok_id", $stok_id)
                ->col("adet", $miktar)
                ->col("satis_fiyati", $satis_fiyat)
                ->col("kdv_oran", $kdv_oran)
                ->col("cari_id", $cari_id)
                ->col("ozel_urun_id", $ozel_urun_id)
                ->col("depo", $depo)
                ->col("satis_evrak_id", $satis_evrak_id)
                ->col("iskonto", $iskontooran)
                ->col("indirim_tutari", $indirim_tutari)
                ->col("adet_etkisiz",$adetetkisiz)
                ->col("siparis_stok",$siparis_stok)
                ->col("doviz",$doviz_adi)
                ->col("doviz_kur",$doviz_kur)
                ->save_();


            if ($ozel_urun == 1) {

                $query = $this->getConnection()->prepare("UPDATE stok_haraket_giris SET ozel_urun_durum = 1  WHERE id = :id");
                $update = $query->execute(array("id" => $ozel_urun_id));

            }

            $this->helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);
            unset($kdv_oran, $kdv_tip);
        }


        $kdv_toplam = $evrak_kdv1_tutar + $evrak_kdv8_tutar + $evrak_kdv18_tutar;
        $genel_toplam = $kdv_toplam + ($evrak_tutari - $evrak_indirim_tutar);


        $evrak_detayi = $request->input("evrak_detay");



        $query = $this->getConnection()->prepare("UPDATE satis_evraklari SET 
evrak_tutar = ? ,
kdv_1 = ? ,
kdv_8 = ? ,
kdv_18 = ? ,
kdv_toplam = ? ,
indirim_toplam = ? ,
genel_toplam  = ? , evrak_detayi = ? ,cari_id = ? ,  unvan = ? ,vergino= ? ,vergidaire= ? ,vergiadres= ? , evrak_tur = ?      WHERE id = ?");
        return $query->execute([
            $evrak_tutari,
            $evrak_kdv1_tutar,
            $evrak_kdv8_tutar,
            $evrak_kdv18_tutar,
            $kdv_toplam,
            $evrak_indirim_tutar,
            $genel_toplam,
            $evrak_detayi,
            $cari_id,
            $cari_data["cari_adi"],
            $cari_data["cari_vergi_no"],
            $cari_data["cari_vergi_daire"],
            $cari_data["cari_adres"],
            $evrak_tur,
            $satis_evrak_id
        ]);
    }

    public function girisleriCek($request)
    {


        $limit = $request->input("limit") != null ? $request->input("limit") : 0;


        if ($limit == 0) {
            $sql = "SELECT stok_haraket_giris.* , cari.cari_adi "
                . "FROM stok_haraket_giris "
                . "LEFT JOIN cari ON stok_haraket_giris.cari_id = cari.id "
                . "WHERE stok_haraket_giris.stok_id = :stok_id and stok_haraket_giris.remove = 0 and stok_haraket_giris.owner_id = :owner ";
        } else {


            $sql = "SELECT stok_haraket_giris.* , cari.cari_adi "
                . "FROM stok_haraket_giris "
                . "LEFT JOIN cari ON stok_haraket_giris.cari_id = cari.id "
                . "WHERE stok_haraket_giris.stok_id = :stok_id and stok_haraket_giris.remove = 0 and stok_haraket_giris.owner_id = :owner LIMIT $limit ";
        }


        $query = $this->getConnection()->prepare($sql);

        $query->BindValue(":stok_id", (int)$request->input("stok_id"), PDO::PARAM_INT);
        $query->BindValue(":owner", (int)Controller::$userInfo['owner_id'], PDO::PARAM_INT);


        $query->execute();


        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cikislariCek($request)
    {


        $limit = $request->input("limit") != null ? $request->input("limit") : 0;


        if ($limit == 0) {


            $sql = "SELECT stok_haraket_cikis.* , cari.cari_adi "
                . "FROM stok_haraket_cikis "
                . "LEFT JOIN cari ON stok_haraket_cikis.cari_id = cari.id "
                . "WHERE stok_haraket_cikis.stok_id = :stok_id and stok_haraket_cikis.remove = 0 and stok_haraket_cikis.owner_id = :owner ";
        } else {


            $sql = "SELECT stok_haraket_cikis.* , cari.cari_adi "
                . "FROM stok_haraket_cikis "
                . "LEFT JOIN cari ON stok_haraket_cikis.cari_id = cari.id "
                . "WHERE stok_haraket_cikis.stok_id = :stok_id and stok_haraket_cikis.remove = 0 and stok_haraket_cikis.owner_id = :owner LIMIT $limit";
        }

        $query = $this->getConnection()->prepare($sql);

        $query->BindValue(":stok_id", (int)$request->input("stok_id"), PDO::PARAM_INT);
        $query->BindValue(":owner", (int)Controller::$userInfo['owner_id'], PDO::PARAM_INT);


        $query->execute();


        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function serinolulariCek($request)
    {

        $sql = "SELECT stok_haraket_giris.* , cari.cari_adi , stok_depolar.stok_depo_adi "
            . "FROM stok_haraket_giris "
            . "LEFT JOIN cari ON stok_haraket_giris.cari_id = cari.id "
            . "INNER JOIN stok_depolar ON stok_haraket_giris.depo = stok_depolar.id "
            . "WHERE stok_haraket_giris.stok_id = :stok_id and "
            . "stok_haraket_giris.remove = 0 and "
            . "stok_haraket_giris.owner_id = :owner and stok_haraket_giris.ozel_urun = 1 and stok_haraket_giris.ozel_urun_durum = 0";


        $query = $this->getConnection()->prepare($sql);

        $query->BindValue(":stok_id", (int)$request->input("stok_id"), PDO::PARAM_INT);
        $query->BindValue(":owner", (int)Controller::$userInfo['owner_id'], PDO::PARAM_INT);


        $query->execute();


        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function icTransfer($request)
    {

        $cikacak_depo = $request->input("source");
        $girecek_depo = $request->input("target");
        $stok_id = $request->input("stok_id");
        $adet = $request->input("adet");
        $ozel_urun = $request->input("ozel");
        $seri = $request->input("seri");
        $giris_tarih = date("Y-m-d H:i:s");
        $evrak = "ictr-" . time();
        $alis_fiyat = $request->input("alis");
        $cevirim = $request->input("cevir");


        if ($cevirim == 0) {


            if ($ozel_urun == 0) {


                $cikis_table = $this->table("stok_haraket_cikis", Controller::$userInfo);

                $cikis_table->col("seri_no", $seri)
                    ->col("cikis_tarih", $giris_tarih)
                    ->col("cikis_evrak_no", $evrak)
                    ->col("stok_id", $stok_id)
                    ->col("adet", $adet)
                    ->col("satis_fiyati", 0)
                    ->col("kdv_oran", 0)
                    ->col("cari_id", 0)
                    ->col("ozel_urun_id", $ozel_urun)
                    ->col("depo", $cikacak_depo)
                    ->col("ic_transfer", 1)
                    ->save_();


                $giris_table = $this->reset()->table("stok_haraket_giris", Controller::$userInfo);

                $giris_table->col("seri_no", $seri)
                    ->col("giris_tarih", $giris_tarih)
                    ->col("giris_evrak_no", $evrak)
                    ->col("stok_id", $stok_id)
                    ->col("adet", $adet)
                    ->col("alis_fiyati", 0)
                    ->col("kdv_oran", 0)
                    ->col("iskonto", 0)
                    ->col("cari_id", 0)
                    ->col("ozel_urun", $ozel_urun)
                    ->col("depo", $girecek_depo)
                    ->col("ic_transfer", 1)
                    ->save_();
            } else {


                $cikis_table = $this->table("stok_haraket_cikis", Controller::$userInfo);

                $cikis_table->col("seri_no", $seri)
                    ->col("cikis_tarih", $giris_tarih)
                    ->col("cikis_evrak_no", $evrak)
                    ->col("stok_id", $stok_id)
                    ->col("adet", $adet)
                    ->col("satis_fiyati", 0)
                    ->col("kdv_oran", 0)
                    ->col("cari_id", 0)
                    ->col("ozel_urun_id", $ozel_urun)
                    ->col("depo", $cikacak_depo)
                    ->col("ic_transfer", 1)
                    ->save_();

                $ozel_guncelle = $this->reset()->table("stok_haraket_giris", Controller::$userInfo)->find($ozel_urun);

                $ozel_guncelle->col("depo", $girecek_depo)->update_();
            }
        } else if ($cevirim == 1) {


            $cikis_table = $this->table("stok_haraket_cikis", Controller::$userInfo);

            $cikis_table->col("seri_no", $seri)
                ->col("cikis_tarih", $giris_tarih)
                ->col("cikis_evrak_no", $evrak)
                ->col("stok_id", $stok_id)
                ->col("adet", $adet)
                ->col("satis_fiyati", 0)
                ->col("kdv_oran", 0)
                ->col("cari_id", 0)
                ->col("ozel_urun_id", $ozel_urun)
                ->col("depo", $cikacak_depo)
                ->col("ic_transfer", 1)
                ->save_();


            $giris_table = $this->reset()->table("stok_haraket_giris", Controller::$userInfo);

            $giris_table->col("seri_no", $seri)
                ->col("giris_tarih", $giris_tarih)
                ->col("giris_evrak_no", $evrak)
                ->col("stok_id", $stok_id)
                ->col("adet", $adet)
                ->col("alis_fiyati", $alis_fiyat)
                ->col("kdv_oran", 18)
                ->col("iskonto", 0)
                ->col("cari_id", 0)
                ->col("ozel_urun", 1)
                ->col("depo", $girecek_depo)
                ->col("ic_transfer", 1)
                ->save_();
        }


        $this->helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);

        return true;
    }

    public function stokPaketle($request)
    {

        $pgstoklar = $request->input("pgstoklar");

        $paketmiktar = $request->input("stokpaketmiktari");

        $paketlenecek_stok_id = $request->input("paketlenecek_stok_id");

        $paketlenecek_stok_vergi_orani = $request->input("paketlenecek_stok_vergi_orani");

        $paketlenecek_stok_depo = $request->input("paketlenecek_stok_depo");


        $cikis_tarih = date("Y-m-d H:i:s");

        $tarih = date("Y-m-d H:i:s");

        $paket_no = uniqid();

        $paket_tutari = 0;

        $aciklama = $paketlenecek_stok_id . " numaralı stok paket";

        $t = microtime(true);

        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);

        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));

        $uniqdate = $d->format("YmdHisu");

        $vade_tarih = date("Y-m-d");

        $vade_gun = 0;

        $sql = "INSERT INTO satis_evraklari ("
            . "evrak_no,"
            . "tarih,"
            . "cari_id,"
            . "evrak_tur,"
            . "owner_id,"
            . "created_date,"
            . "created_user,"
            . "vade_gun,"
            . "parakende_satis,"
            . "vade_tarih,"
            . "unvan,"
            . "vergino,"
            . "vergidaire,"
            . "vergiadres,"
            . "uniq_id,"
            . "evrak_zamani,
            evrak_detayi , paket_kod 
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);

        $result = $query->execute([
            $paket_no,
            $cikis_tarih,
            0,
            0,
            Controller::$userInfo['owner_id'],
            $cikis_tarih,
            Controller::$userInfo["id"],
            $vade_gun,
            0,
            $vade_tarih,
            "",
            "",
            "",
            "",
            $uniqdate,
            $cikis_tarih,
            $aciklama,
            $paket_no
        ]);

        $satis_evrak_id = $this->getConnection()->lastInsertId();

        foreach ($pgstoklar as $key => $value) {

            $evrak_no = uniqid();

            $stok_id = $key;

            $fiyat = $value["fiyat"];

            $miktar = $value["miktar"] * $paketmiktar;

            $depo = $value["depo"];

            $vergi_oran = $value["vergi_oran"];

            $paket_tutari = $paket_tutari + ($fiyat * $miktar);

            if ($vergi_oran > 9) {

                $kdv_oran_duzelt = (float)"1.$vergi_oran";

            } else {

                $kdv_oran_duzelt = (float)"1.0$vergi_oran";
            }

            $vergisiz_fiyat = $value["fiyat"] / $kdv_oran_duzelt;

            $cikis_insert_sql = "INSERT INTO stok_haraket_cikis SET 
                cikis_evrak_no = ? , 
                cari_id = ? ,
                cikis_tarih = ? , 
                stok_id = ? ,  
                adet = ? ,
                satis_fiyati = ? ,
                kdv_oran = ? ,
                depo = ? , 
                paket_kod = ? , 
                vergili_satis_fiyat = ? , 
                vergi_dahil_gosterim = ?  , 
                aciklama = ?  , 
                satis_evrak_id = ? , 
                owner_id = ? ";

            $insert_query = $this->getConnection()->prepare($cikis_insert_sql);
            $insert_query->execute([$evrak_no, 0, $tarih, $stok_id, $miktar, $vergisiz_fiyat, $vergi_oran, $depo, $paket_no, $fiyat, 1,
                $aciklama . " çıkışı", $satis_evrak_id, Controller::$userInfo['owner_id']]);

            $this->helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);
        }


        $alim_evrak_no = "alm-" . uniqid();


        $sql = "INSERT INTO alim_evraklari ("
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
            . "vergiadres,evrak_zamani,evrak_detayi,paket_kod) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);
        $result = $query->execute([
            $alim_evrak_no,
            $tarih,
            0,
            0,
            Controller::$userInfo["owner_id"],
            $tarih,
            Controller::$userInfo["id"],
            $vade_gun,
            $vade_tarih,
            "",
            "",
            "",
            "",
            $tarih,
            "",
            $paket_no
        ]);

        $alim_evrak_id = $this->getConnection()->lastInsertId();


        if ($paketlenecek_stok_vergi_orani > 9) {

            $paketlenecek_stok_vergi_orani_oran_duzelt = (float)"1.$paketlenecek_stok_vergi_orani";

        } else {

            $paketlenecek_stok_vergi_orani_oran_duzelt = (float)"1.0$paketlenecek_stok_vergi_orani";
        }


        $alis_fiyati = $paket_tutari / $paketlenecek_stok_vergi_orani_oran_duzelt;


        $giris_insert_sql = "INSERT INTO stok_haraket_giris SET
                giris_evrak_no = ? ,
                cari_id = ? ,
                giris_tarih = ? ,
                stok_id = ? ,
                adet = ? ,
                alis_fiyati = ? ,
                kdv_oran = ? ,
                depo = ? , alim_evrak_id = ? , owner_id = ?  , aciklama = ? , paket_kod = ?  ";
        $insert_query = $this->getConnection()->prepare($giris_insert_sql);
        $insert_query->execute([
            $paket_no, 0,
            $tarih,
            $paketlenecek_stok_id,
            $paketmiktar,
            $alis_fiyati,
            $paketlenecek_stok_vergi_orani,
            $paketlenecek_stok_depo, $alim_evrak_id, Controller::$userInfo['owner_id'], "Paketten Oluşturuldu", $paket_no]);


        return true;
    }

    public function ozeldenCikar($request)
    {

        $ozel_urun_id = $request->input("ozel_urun_id");


        $ozel = $this->table("stok_haraket_giris", Controller::$userInfo)->find($ozel_urun_id);
        $ozel_get = $ozel->get();
        $cari = $ozel_get["cari_id"];
        $alis = $ozel_get["alis_fiyati"];
        $kdv = $ozel_get["kdv_oran"];
        $depo = $ozel_get["depo"];
        $evrak_no = $ozel_get["giris_evrak_no"];
        $tarih = $ozel_get["giris_tarih"];
        $stok_id = $ozel_get["stok_id"];
        $seri = $ozel_get["seri_no"];

        $ozel_guncelle = $this->reset()->table("stok_haraket_giris", Controller::$userInfo)->find($ozel_urun_id);
        $ozel_guncelle->col("remove", 1)->update_();
        $cikis_table = $this->reset()->table("stok_haraket_cikis", Controller::$userInfo);

        $cikis_table->col("seri_no", $seri)
            ->col("cikis_tarih", $tarih)
            ->col("cikis_evrak_no", $evrak_no)
            ->col("stok_id", $stok_id)
            ->col("adet", 0)
            ->col("satis_fiyati", 0)
            ->col("kdv_oran", 0)
            ->col("cari_id", 0)
            ->col("ozel_urun_id", $ozel_urun_id)
            ->col("depo", $depo)
            ->col("ic_transfer", 1)
            ->save_();


        $giris_table = $this->reset()->table("stok_haraket_giris", Controller::$userInfo);

        $giris_table->col("seri_no", "")
            ->col("giris_tarih", $tarih)
            ->col("giris_evrak_no", $evrak_no)
            ->col("stok_id", $stok_id)
            ->col("adet", 1)
            ->col("alis_fiyati", $alis)
            ->col("kdv_oran", $kdv)
            ->col("iskonto", 0)
            ->col("cari_id", $cari)
            ->col("ozel_urun", 0)
            ->col("depo", $depo)
            ->col("ic_transfer", 1)
            ->save_();

        return true;
    }

    public function stokHareketKontrol($stok_id)
    {

        $giris = 1;
        $cikis = 1;

        $giris_table = $this->table("stok_haraket_giris", Controller::$userInfo)->where(["stok_id" => ["=", $stok_id]])->get();
        $cikis_table = $this->table("stok_haraket_cikis", Controller::$userInfo)->where(["stok_id" => ["=", $stok_id]])->get();

        if (!$giris_table) {
            $giris = 0;
        }

        if (!$cikis_table) {
            $cikis = 0;

        }

        if ($giris == 0 && $cikis == 0) {

            return 1;
        } else {

            return 0;
        }
    }

    public function haftalikRapor($stok_id)
    {

        $bugun = date("Y-m-d");
        $gecenhafta = date("Y-m-d", strtotime('last week'));


        $bas_tarih = $gecenhafta . " 00:00:00";
        $bit_tarih = $bugun . " 23:59:59";


        $cikissql = "SELECT   adet  , satis_fiyati , kdv_oran , cikis_tarih "
            . "FROM stok_haraket_cikis "
            . "WHERE stok_haraket_cikis.stok_id = :stok_id and stok_haraket_cikis.remove = 0 and stok_haraket_cikis.owner_id = :owner AND stok_haraket_cikis.cikis_tarih >= :baslama AND stok_haraket_cikis.cikis_tarih <= :bitis  
            ";


        $cikisquery = $this->getConnection()->prepare($cikissql);
        $cikisquery->BindValue(":stok_id", $stok_id, PDO::PARAM_INT);
        $cikisquery->BindValue(":owner", (int)Controller::$userInfo['owner_id'], PDO::PARAM_INT);
        $cikisquery->BindValue(":baslama", $bas_tarih, PDO::PARAM_STR);
        $cikisquery->BindValue(":bitis", $bit_tarih, PDO::PARAM_STR);


        $cikisquery->execute();
        $cikis_result = $cikisquery->fetchAll(PDO::FETCH_ASSOC);


        $satislar = [];
        $toplam = 0;


        if ($cikis_result) {

            foreach ($cikis_result as $key => $val) {

                $day = date("Y-m-d", strtotime($val["cikis_tarih"]));

                $toplam = $toplam + $val["satis_fiyati"];

                if (isset($satislar[$day])) {

                    $satislar[$day] = $satislar[$day] + $val["adet"];

                } else {

                    $satislar[$day] = $val["adet"];
                }


            }
        }

        $result = [

            'toplam_satis' => $toplam,
            'haftalik_satislar' => $satislar,
            'bitis' => $bugun,
            'baslama' => $gecenhafta

        ];


        return $result;

    }

}
