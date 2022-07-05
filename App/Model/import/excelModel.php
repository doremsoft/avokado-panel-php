<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 *
 * @author Doğuş DİCLE
 */
class excelModel extends Dimodel
{
    /*
     * Controller::$userInfo
     */

    private $helper = null;


    private function trbuyut($str)
    {
        $str = str_replace(array('i', 'ı', 'ü', 'ğ', 'ş', 'ö', 'ç'), array('İ', 'I', 'Ü', 'Ğ', 'Ş', 'Ö', 'Ç'), $str);
        return mb_strtoupper($str, 'utf-8');
    }

    private function trkucult($str)
    {
        $str = str_replace(array('İ', 'I', 'Ü', 'Ğ', 'Ş', 'Ö', 'Ç'), array('i', 'ı', 'ü', 'ğ', 'ş', 'ö', 'ç'), $str);
        return mb_strtolower($str, 'utf-8');
    }

    private function tr_strtolower($metin)
    {
        return mb_strtolower($metin, 'utf-8');
    }

    private function tr_strtoupper($metin)
    {
        return mb_strtoupper($metin, 'utf-8');
    }

    private function tr_ucfirst($metin)
    {
        $ilk = mb_substr($metin, 0, 1, 'utf-8');
        $kalan = mb_substr($metin, 1, strlen($metin), 'utf-8');
        return $this->trbuyut($ilk) . $this->trkucult($kalan);
    }

    public function stokUpload($ext, $file_path, $request)
    {
        set_time_limit(1500);

        $inputFileName = $file_path;

        if ($ext == "xls") {

            $inputFileType = "Xls";
        } else if ($ext == "xlsx") {

            $inputFileType = "Xlsx";
        } else {
            $inputFileType = $ext;
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

        $reader->setLoadSheetsOnly(true);

        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $last_val_query = $query->fetch();

        $last_val = $last_val_query["last_id"];

        $last_id = $last_val_query["last_id"];

        $last_val++;

        $ekliurundurum = $request->input("ekliurun");

        $ii = 0;

        $insertsql = "INSERT INTO stok (stok_barkod_no,stok_kod,stok_adi,stok_birimi,stok_grup,stok_alis_fiyati,stok_satis_fiyati,stok_max_iskontolu_satis_fiyati,stok_kdv_oran,stok_create_id,last_val,stok_kdv_dahil_satis_fiyati,stok_fiyat_vergi_durum,owner_id,stok_resim,stok_doviz) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $updatesql = "UPDATE stok SET stok_barkod_no= ? ,stok_kod = ? ,stok_adi= ? ,stok_birimi= ? ,stok_grup= ? ,stok_alis_fiyati= ? ,stok_satis_fiyati= ? ,stok_max_iskontolu_satis_fiyati= ? ,stok_kdv_oran= ? ,stok_create_id= ? ,last_val= ? ,stok_kdv_dahil_satis_fiyati= ? ,stok_fiyat_vergi_durum= ? , stok_doviz = ? WHERE id = ?";

        foreach ($rows as $key => $value) {

            if ($ii != 0 && $value[0] != NULL) {

                $barkod = $value[0];

                if ($barkod == NULL) {

                    $barkod = "";
                } else {

                    $barkod = trim($barkod);
                }


                $stok_kod = $value[1];

                if ($stok_kod == NULL) {

                    $stok_kod = "";
                }


                $stok_adi = $value[2];

                $stok_adi = trim($stok_adi);


                if (isset($value[2])) {

                    if ($value[2] != NULL) {

                        $stok_adi = $value[2];
                    } else {

                        $stok_adi = $barkod;
                    }
                } else {

                    $stok_adi = $barkod;
                }


                if (trim($stok_adi) == "") {


                    if (trim($stok_kod) != "") {
                        $stok_adi = $stok_kod . " " . $barkod;
                    } else {

                        $stok_adi = $barkod;
                    }


                }


                $onek = $request->input("name_pre");


                if ($onek != "no") {

                    $stok_adi = $onek . $stok_adi;
                }


                if (isset($value[3])) {

                    if ($value[3] != NULL) {

                        $birim_id = $value[3];
                    } else {

                        $birim_id = $request->input("birim_id");
                    }
                } else {

                    $birim_id = $request->input("birim_id");
                }


                if (isset($value[6])) {

                    if ($value[6] != NULL) {


                        $vergi_orani = $value[6];
                    } else {

                        $vergi_orani = $request->input("vergi_oran");
                    }
                } else {
                    $vergi_orani = $request->input("vergi_oran");
                }


                if (isset($value[7])) {

                    if ($value[7] != NULL) {

                        $vergi_durum = $value[7];
                    } else {

                        $vergi_durum = $request->input("vergi_oran_durum");
                    }
                } else {
                    $vergi_durum = $request->input("vergi_oran_durum");
                }


                if ($vergi_orani > 9) {

                    $kdv_duzelt = "1." . $vergi_orani;
                } else {

                    $kdv_duzelt = "1.0" . $vergi_orani;
                }


                if (isset($value[4])) {

                    if ($value[4] != NULL) {

                        $alis_fiyati = $value[4];


                        $alis_fiyati = str_replace(",", ".", $alis_fiyati);


                    } else {

                        $alis_fiyati = "0.0000";
                    }
                } else {
                    $alis_fiyati = "0.0000";
                }


                if (isset($value[5])) {

                    if ($value[5] != NULL) {

                        $satis_fiyati = $value[5];


                        $satis_fiyati = str_replace(",", ".", $satis_fiyati);

                    } else {

                        $satis_fiyati = "0.0000";
                    }
                } else {
                    $satis_fiyati = "0.0000";
                }


                if (isset($value[8])) {

                    if ($value[8] != NULL) {

                        $para_birim = $value[8];
                    } else {

                        $para_birim = $request->input("para_birim");
                    }
                } else {
                    $para_birim = $request->input("para_birim");
                }


                $min_satis_fiyati = $satis_fiyati;


                if ($vergi_durum == 1) {

                    if ($vergi_orani > 0) {

                        $stok_kdv_dahil_satis_fiyati = $satis_fiyati * $kdv_duzelt;
                    } else {

                        $stok_kdv_dahil_satis_fiyati = $satis_fiyati;
                    }
                } else if ($vergi_durum == 2) {


                    $stok_kdv_dahil_satis_fiyati = $satis_fiyati;


                    if ($vergi_orani > 0) {

                        if ($min_satis_fiyati > 0) {

                            $min_satis_fiyati = $min_satis_fiyati / $kdv_duzelt;
                        }

                        if ($alis_fiyati > 0) {

                            $alis_fiyati = $alis_fiyati / $kdv_duzelt;
                        }

                        if ($satis_fiyati > 0) {

                            $satis_fiyati = $satis_fiyati / $kdv_duzelt;
                        }
                    }
                }


                $dbresult = $this->getConnection()->prepare(""
                    . "SELECT id "
                    . "FROM stok "
                    . "WHERE remove = 0 and owner_id = ? and stok_barkod_no = ? ORDER BY id DESC LIMIT 1 ");

                $dbresult->execute([$owner_id, $barkod]);

                $barkod_result_query = $dbresult->fetch();

                $uniq = uniqid();

                $durum = false;

                if ($barkod_result_query) {

                    if ($ekliurundurum == 1) {

                        $durum = true;
                    } else {

                        $durum = false;
                    }
                } else {

                    $durum = true;
                }

                if ($durum) {

                    $insert_query = $this->getConnection()->prepare($insertsql);

                    $insert_query->execute([
                        $barkod,
                        $stok_kod,
                        $stok_adi,
                        $birim_id,
                        0,
                        $alis_fiyati,
                        $satis_fiyati,
                        0,
                        $vergi_orani,
                        $uniq,
                        $last_val,
                        $stok_kdv_dahil_satis_fiyati,
                        $vergi_durum,
                        $owner_id,
                        "noimage.jpg",
                        $para_birim
                    ]);
                } else {
                    $update_query = $this->getConnection()->prepare($updatesql);
                    $update_query->execute([
                        $barkod,
                        $stok_kod,
                        $stok_adi,
                        $birim_id,
                        0,
                        $alis_fiyati,
                        $satis_fiyati,
                        0,
                        $vergi_orani,
                        $uniq,
                        $last_val,
                        $stok_kdv_dahil_satis_fiyati,
                        $vergi_durum,
                        $para_birim,
                        $barkod_result_query["id"]
                    ]);
                }
            }

            $ii++;
        }

        unlink($file_path);

        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_val, $owner_id]);


        return true;
    }

    public function stokUpdateUpload($ext, $file_path, $request)
    {

        $inputFileName = $file_path;

        if ($ext == "xls") {

            $inputFileType = "Xls";
        } else if ($ext == "xlsx") {

            $inputFileType = "Xlsx";
        } else {
            $inputFileType = $ext;
        }


        $this->getConnection()->beginTransaction();

        $bTsonuc = true;


        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

        $reader->setLoadSheetsOnly(true);

        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $last_val_query = $query->fetch();

        $last_val = $last_val_query["last_id"];

        $last_id = $last_val_query["last_id"];

        $last_val++;

        $ii = 0;


        $updatesql = "
UPDATE stok SET 
stok_adi= ? ,
stok_alis_fiyati= ? ,
stok_satis_fiyati= ? , 
stok_kdv_oran= ? ,
last_val= ? ,
stok_kdv_dahil_satis_fiyati= ? ,
stok_fiyat_vergi_durum= ? ,
stok_parent_id = ? ,
stok_doviz = ? ,
stok_varyant_adi = ? ,
stok_varyant_deger = ? 
WHERE id = ? and owner_id = ?";

        $vergi_durum = $request->input("vergi_oran_durum");
        $isim_duzeltme = $request->input("isim_duzeltme");

        foreach ($rows as $key => $value) {

            if ($ii != 0 && $value[0] != NULL) {

                $stok_id = $value[0];

                $stok_adi = $value[1];


                $stok_adi = trim($stok_adi);


                if ($isim_duzeltme == 1) {

                    $stok_adi = $this->tr_ucfirst($stok_adi);
                } else if ($isim_duzeltme == 2) {

                    //Bütün harfler büyük

                    $stok_adi = $this->trbuyut($stok_adi);
                } else if ($isim_duzeltme == 3) {
                    //Bütün harfler küçük
                    $stok_adi = $this->trkucult($stok_adi);
                }


                if (isset($value[4])) {

                    if ($value[4] != NULL) {


                        $vergi_orani = $value[4];
                    } else {

                        $vergi_orani = 0;
                    }
                } else {

                    $vergi_orani = 0;
                }


                if ($vergi_orani > 9) {

                    $kdv_duzelt = "1." . $vergi_orani;
                } else {

                    $kdv_duzelt = "1.0" . $vergi_orani;
                }


                if (isset($value[2])) {

                    if ($value[2] != NULL) {

                        $alis_fiyati = $value[2];
                    } else {

                        $alis_fiyati = "0.0000";
                    }
                } else {
                    $alis_fiyati = "0.0000";
                }


                if (isset($value[3])) {

                    if ($value[3] != NULL) {

                        $satis_fiyati = $value[3];
                    } else {

                        $satis_fiyati = "0.0000";
                    }
                } else {
                    $satis_fiyati = "0.0000";
                }


                if ($vergi_durum == 1) {

                    if ($vergi_orani > 0) {

                        $stok_kdv_dahil_satis_fiyati = $satis_fiyati * $kdv_duzelt;
                    } else {

                        $stok_kdv_dahil_satis_fiyati = $satis_fiyati;
                    }
                } else if ($vergi_durum == 2) {


                    $stok_kdv_dahil_satis_fiyati = $satis_fiyati;


                    if ($vergi_orani > 0) {


                        if ($alis_fiyati > 0) {

                            $alis_fiyati = $alis_fiyati / $kdv_duzelt;
                        }

                        if ($satis_fiyati > 0) {

                            $satis_fiyati = $satis_fiyati / $kdv_duzelt;
                        }
                    }
                }


                $doviz = $value[5];
                $pid = $value[6];
                $stok_varyant_adi = $value[7];
                $stok_varyant_deger = $value[8];


                $dbresult = $this->getConnection()->prepare(""
                    . "SELECT id "
                    . "FROM stok "
                    . "WHERE remove = 0 and owner_id = ? and id = ? ");

                $dbresult->execute([$owner_id, $stok_id]);

                $id_kontrol = $dbresult->fetch();

                $uniq = uniqid();


                if ($id_kontrol) {

                    /*
                     * stok_parent_id = ? ,
stok_doviz = ? ,
stok_varyant_adi = ? ,
stok_varyant_deger = ?
WHERE id = ? and owner_id = ?";
                     */

                    $update_query = $this->getConnection()->prepare($updatesql);
                    $update_result = $update_query->execute([
                        $stok_adi,
                        $alis_fiyati,
                        $satis_fiyati,
                        $vergi_orani,
                        $last_val,
                        $stok_kdv_dahil_satis_fiyati,
                        $vergi_durum,
                        $pid,
                        $doviz,
                        $stok_varyant_adi,
                        $stok_varyant_deger,
                        $stok_id,
                        $owner_id

                    ]);


                    if (!$update_result) {
                        $bTsonuc = false;
                    }
                }
            }

            $ii++;
        }

        unlink($file_path);

        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_val, $owner_id]);

        if (!$updateQuery) {
            $bTsonuc = false;
        }

        if ($bTsonuc) {

            $this->getConnection()->commit();
            return true;
        } else {
            $this->getConnection()->rollBack();
            return false;
        }
    }


    public function stokAdetGuncelle($ext, $file_path, $request)
    {
        set_time_limit(1500);

        $this->helper = Controller::helper(null, "stokAdetHelper");

        $inputFileName = $file_path;

        if ($ext == "xls") {

            $inputFileType = "Xls";
        } else if ($ext == "xlsx") {

            $inputFileType = "Xlsx";
        } else {
            $inputFileType = $ext;
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

        $reader->setLoadSheetsOnly(true);

        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();


        $ii = 0;

        $fazla_urunler = [];

        $eksik_urunler = [];


        foreach ($rows as $key => $value) {

            if ($ii != 0 && $value[0] != NULL) {

                $id = $value[0];

                $sistemdeki_adet = $value[1];

                $eldeki_adet = $value[2];

                $adet_fark = $sistemdeki_adet - $eldeki_adet;


                echo " id : ".$id." sistremdeki : ".$sistemdeki_adet." eldeki : ".$eldeki_adet." Fark:".$adet_fark." <br>";


                if ($adet_fark > 0) {

                    $eksik_urunler[] = [
                        "id" => $id,
                        "adet" => $adet_fark
                    ];

                } else if ($adet_fark < 0) {


                    $fazla_urunler[] = [
                        "id" => $id,
                        "adet" => $adet_fark
                    ];




                }

            }
            $ii++;
        }


        if (!empty($fazla_urunler)) {

            $now = date("Y-m-d H:i:s");

            $evrak_id = $this->satisEvrakOlustur($now, "sf-" . $now);

            $this->evrakCikisKalemleriEkle($now, 1, "sf-" . $now, $evrak_id, $eksik_urunler);
        }


        if (!empty($eksik_urunler)) {
            $now = date("Y-m-d H:i:s");

            $evrak_id = $this->alimEvrakOlustur($now, "al-" . $now);

            $this->evrakGirisKalemleriEkle($now, 1, "al-" . $now, $evrak_id, $fazla_urunler);
        }

        return true;

    }


    public function satisEvrakOlustur($now, $evrak_no)
    {


        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        $uniqdate = $d->format("YmdHisu");


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
            evrak_detayi,
            siparis_durumu ,
            teslimat_adresi ,
            siparis_kod,
            
            evrak_tutar,
            kdv_toplam ,
            indirim_toplam  ,
            genel_toplam  , 
            siparis_fatura_kod  ,kargo_bedeli
            
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);

        $result = $query->execute([
            $evrak_no,
            $now,
            0,
            1,
            1,
            $now,
            1,
            0,
            0,
            0,
            "",
            "",
            "",
            "",
            $uniqdate,
            $now,
            "",
            8,
            "",
            "",
            0,
            0,
            0,
            0,
            "",
            0
        ]);

        if (!$result) {

            return false;

        } else {

            return $this->getConnection()->lastInsertId();
        }

    }

    public function evrakCikisKalemleriEkle($now, $depo_id, $satis_evrak_no, $satis_evrak_id, $urunler)
    {


        foreach ($urunler as $key => $val) {


            $insert_sql = "INSERT INTO stok_haraket_cikis 
(
seri_no,
cikis_tarih,
cikis_evrak_no,
stok_id,
adet,
satis_fiyati,
kdv_oran,
cari_id,
ozel_urun_id,
depo,
satis_evrak_id,
iskonto,
indirim_tutari,
adet_etkisiz,
doviz,
doviz_kur,owner_id,aciklama)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $query = $this->getConnection()->prepare($insert_sql);

            $update = $query->execute([
                "", $now, $satis_evrak_no, $val["id"], abs($val["adet"]), 0, 0, 0, 0, $depo_id, $satis_evrak_id, 0, 0, 0, "TL", 1, 1, "sayimfark"

            ]);


            $this->helper->set($this->getConnection(), $val["id"])->count(true)->reset(true);

        }


        return true;
    }

    public function alimEvrakOlustur($now, $evrak_no)
    {


        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        $uniqdate = $d->format("YmdHisu");


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
            . "vergiadres,"
            . "evrak_zamani,
            evrak_tutar,
            kdv_toplam ,
            indirim_toplam  ,
            genel_toplam  
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($sql);

        $result = $query->execute([
            $evrak_no,
            $now,
            0,
            1,
            1,
            $now,
            1,
            0,
            0,
            "",
            "",
            "",
            "",
            $now,
            0,
            0,
            0,
            0
        ]);

        if (!$result) {

            return false;

        } else {

            return $this->getConnection()->lastInsertId();
        }

    }

    public function evrakGirisKalemleriEkle($now, $depo_id, $alim_evrak_no, $alim_evrak_id, $urunler)
    {


        foreach ($urunler as $key => $val) {


            $insert_sql = "INSERT INTO stok_haraket_giris 
(
seri_no,
giris_tarih,
giris_evrak_no,
stok_id,
adet,
alis_fiyati,
kdv_oran,
cari_id,
depo,
alim_evrak_id,
iskonto,
indirim_tutari,
adet_etkisiz,
doviz,
doviz_kur,
owner_id,
aciklama)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $query = $this->getConnection()->prepare($insert_sql);

            $update = $query->execute([
                "", $now, $alim_evrak_no, $val["id"], abs($val["adet"]), 0, 0, 0, $depo_id, $alim_evrak_id, 0, 0, 0, "TL", 1, 1, "sayimfark"

            ]);


            $this->helper->set($this->getConnection(), $val["id"])->count(true)->reset(true);

        }


        return true;
    }

}
