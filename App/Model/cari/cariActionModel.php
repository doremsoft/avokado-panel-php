<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class cariActionModel extends Dimodel {

    public function addCari($request) {


        if ($request->input("cari_kod") == null) {

            $request->set("cari_kod", time());
        }

        $cari_id = $this->table("cari", Controller::$userInfo)
                ->col("cari_kod", $request->input("cari_kod"))
                ->col("cari_adi", $request->input("cari_adi"))
                ->col("cari_telefon", $request->input("cari_telefon"))
                ->col("cari_gsm", $request->input("cari_gsm"))
                ->col("cari_adres", $request->input("cari_adres"))
                ->col("cari_detay", $request->input("cari_detay"))
                ->col("cari_mail", $request->input("cari_mail"))
                ->col("cari_kredi_limit", $request->input("cari_kredi_limit"))
                ->col("cari_vade_gun", $request->input("cari_vade_gun"))
                ->col("cari_turu", $request->input("tur"))
                ->col("cari_vergi_no", $request->input("cari_vergi_no"))
                ->col("cari_vergi_daire", $request->input("cari_vergi_daire"))
                ->col("cari_image",$request->input("cari_image"))
                ->save_();

        return $cari_id;
    }

    public function cariUpdate($request, $id) {
        return $this->table("cari", Controller::$userInfo)
                        ->find($id)
                        ->col("cari_kod", $request->input("cari_kod"))
                        ->col("cari_adi", $request->input("cari_adi"))
                        ->col("cari_telefon", $request->input("cari_telefon"))
                        ->col("cari_gsm", $request->input("cari_gsm"))
                        ->col("cari_adres", $request->input("cari_adres"))
                        ->col("cari_detay", $request->input("cari_detay"))
                        ->col("cari_mail", $request->input("cari_mail"))
                        ->col("cari_kredi_limit", $request->input("cari_kredi_limit"))
                        ->col("cari_vade_gun", $request->input("cari_vade_gun"))
                        ->col("cari_vergi_no", $request->input("cari_vergi_no"))
                        ->col("cari_vergi_daire", $request->input("cari_vergi_daire"))
                        ->col("cari_image",$request->input("cari_image"))
                        ->update_();
    }

    public function cariArama($srt, $tur) {
        if ($srt == "" || empty($srt) || $srt == NULL) {

            return false;
        } else {
            $sql = "SELECT "
                    . "* "
                    . "FROM "
                    . "cari "
                    . "WHERE "
                    . "remove = 0 and owner_id = :owner and cari_turu = :tur and cari_adi LIKE :str ";


            $query = $this->getConnection()->prepare($sql);


            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindValue(":str", "%" . $srt . "%", PDO::PARAM_STR);
            $query->BindValue(":tur", $tur, PDO::PARAM_STR);


            $query->execute();


            if ($query->rowCount() > 0) {

                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {

                return false;
            }
        }
    }

    public function cariHepsiArama($srt) {
        if ($srt == "" || empty($srt) || $srt == NULL) {

            return false;
        } else {
            $sql = "SELECT "
                    . "* "
                    . "FROM "
                    . "cari "
                    . "WHERE "
                    . "remove = 0 and owner_id = :owner  and cari_adi LIKE :str ";


            $query = $this->getConnection()->prepare($sql);


            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindValue(":str", "%" . $srt . "%", PDO::PARAM_STR);



            $query->execute();


            if ($query->rowCount() > 0) {

                return $query->fetchAll(PDO::FETCH_ASSOC);
            } else {

                return false;
            }
        }
    }

    /*
     *    $haraketTur = [
      1 => "Hepsi",
      2 => "Satış Faturaları",
      3 => "Alım Faturaları",
      4 => "Tahsilat",
      5 => "Ödeme"
      ];
     * 
     */

    public function getCari($id) {

        return $this->table("cari", Controller::$userInfo)->find($id, TRUE);
    }

    public function hareketGetir($request) {



        $hareket_tur = $request->input("stur");
        $baslama = $request->input("bas_tarih");
        $bitis = $request->input("bit_tarih");
        $cari_id = $request->input("cari_id");
        $bas_saat =  $request->input("bas_saat");
        $bit_saat =  $request->input("bit_saat");



        if ($hareket_tur == 1) {

            $bas_tarih = date("Y-m-d H:i:s", strtotime($baslama . " ".$bas_saat));
            $bit_tarih = date("Y-m-d H:i:s", strtotime($bitis . " ".$bit_saat));
            $owner_id = Controller::$userInfo['owner_id'];



            $sql12 = "SELECT 
alim_evraklari.evrak_no as evrakno , 
alim_evraklari.id as evrak_id , 
SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)  as islem_tutar , 
alim_evraklari.tarih as islem_tarih ,  
('alisf') as tip 
FROM alim_evraklari 
INNER JOIN stok_haraket_giris  ON  alim_evraklari.id = stok_haraket_giris.alim_evrak_id  
WHERE (alim_evraklari.created_date >= ? 
and alim_evraklari.created_date <= ?)  
and alim_evraklari.remove = 0 
and alim_evraklari.owner_id = ? 
and alim_evraklari.cari_id = ? 
and stok_haraket_giris.owner_id = ? 
and stok_haraket_giris.remove = 0  

GROUP BY alim_evraklari.id ";

            $query12 = $this->getConnection()->prepare($sql12);
            $query12->execute([$bas_tarih, $bit_tarih, $owner_id, $cari_id,$owner_id]);
            $alimlar = $query12->fetchAll(PDO::FETCH_ASSOC);



            $sql11 = "SELECT 
satis_evraklari.evrak_no as evrakno , 
satis_evraklari.id as evrak_id , 
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)  as islem_tutar , 
satis_evraklari.tarih as islem_tarih ,  
('satisf') as tip 
FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id  
WHERE (satis_evraklari.created_date >= ? and satis_evraklari.created_date <= ?)  
and satis_evraklari.remove = 0 
and satis_evraklari.owner_id = ? 
and satis_evraklari.cari_id = ? 
and stok_haraket_cikis.owner_id = ? 
and stok_haraket_cikis.remove = 0  

GROUP BY satis_evraklari.id ";



           // $sql11 = "SELECT satis_evraklari.evrak_no as evrakno , satis_evraklari.id as evrak_id , satis_evraklari.genel_toplam as islem_tutar , satis_evraklari.tarih as islem_tarih ,  ('satisf') as tip FROM satis_evraklari  WHERE (created_date >= ? and created_date <= ?)  and remove = 0 and owner_id = ? and cari_id = ? ORDER BY id DESC";





            $query11 = $this->getConnection()->prepare($sql11);
            $query11->execute([$bas_tarih, $bit_tarih, $owner_id, $cari_id,$owner_id]);
            $satislar = $query11->fetchAll(PDO::FETCH_ASSOC);


            $sql = "SELECT islem_tarih ,islem_tutar  , ('tahsil') as tip , ('') as evrakno FROM tahsilatlar WHERE tahsilatlar.remove = 0 and tahsilatlar.owner_id = :owner and tahsilatlar.islem_tarih >= :baslama and tahsilatlar.created_date <= :bitis and tahsilatlar.cari_id = :cari_id ";
            $query = $this->getConnection()->prepare($sql);
            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);
            $query->execute();
            $tahsilatlar = $query->fetchAll(PDO::FETCH_ASSOC);



            $sql2 = "SELECT odeme_tarih as islem_tarih , odeme_tutar as islem_tutar , ('odeme') as tip  , ('') as evrakno FROM odemeler WHERE remove = 0 and owner_id = :owner and created_date >= :baslama and created_date <= :bitis and cari_id = :cari_id ";
            $query1 = $this->getConnection()->prepare($sql2);
            $query1->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query1->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query1->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query1->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);
            $query1->execute();


            $odemeler = $query1->fetchAll(PDO::FETCH_ASSOC);

            $result = [];



            $faturalar = array_merge($alimlar, $satislar);


            $finansal = array_merge($tahsilatlar, $odemeler);


            $genel = array_merge($faturalar, $finansal);


            $tmpArray = array();

            foreach ($genel as $key => $value) {

                $tmpArray[$key] = $value['islem_tarih'];
            }

            array_multisort($tmpArray, SORT_DESC, $genel);



            return $genel;
        } else if ($hareket_tur == 2) {



            //$sql = "SELECT * FROM satis_evraklari  WHERE (created_date >= ? and created_date <= ?)  and remove = 0 and owner_id = ? and cari_id = ? ORDER BY id DESC";



            $sql = "SELECT 
satis_evraklari.*, 
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)  as genel_toplam , 
satis_evraklari.tarih as islem_tarih 
FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id  
WHERE (satis_evraklari.created_date >= ? and satis_evraklari.created_date <= ?)  
and satis_evraklari.remove = 0 
and satis_evraklari.owner_id = ? 
and satis_evraklari.cari_id = ? 
and stok_haraket_cikis.owner_id = ? 
and stok_haraket_cikis.remove = 0  

GROUP BY satis_evraklari.id ";



            $query = $this->getConnection()->prepare($sql);

          
            $bas_tarih = date("Y-m-d H:i:s", strtotime($baslama . " ".$bas_saat));
            $bit_tarih = date("Y-m-d H:i:s", strtotime($bitis . " ".$bit_saat));

            $owner_id = Controller::$userInfo['owner_id'];

            $query->execute([$bas_tarih, $bit_tarih, $owner_id, $cari_id, $owner_id]);

            return $query->fetchAll(PDO::FETCH_ASSOC);
            
            
        } else if ($hareket_tur == 3) {





            $sql = "SELECT 
alim_evraklari.*, 
SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)  as genel_toplam , 
alim_evraklari.tarih as islem_tarih ,  
('alisf') as tip 
FROM alim_evraklari 
INNER JOIN stok_haraket_giris  ON  alim_evraklari.id = stok_haraket_giris.alim_evrak_id  
WHERE (alim_evraklari.created_date >= ? and alim_evraklari.created_date <= ?)  
and alim_evraklari.remove = 0 
and alim_evraklari.owner_id = ? 
and alim_evraklari.cari_id = ? 
and stok_haraket_giris.owner_id = ? 
and stok_haraket_giris.remove = 0  

GROUP BY alim_evraklari.id ";



          $query = $this->getConnection()->prepare($sql);

          
            $bas_tarih = date("Y-m-d H:i:s", strtotime($baslama . " ".$bas_saat));
            $bit_tarih = date("Y-m-d H:i:s", strtotime($bitis . " ".$bit_saat));

            $owner_id = Controller::$userInfo['owner_id'];

            $query->execute([$bas_tarih, $bit_tarih, $owner_id, $cari_id,$owner_id]);

            return $query->fetchAll(PDO::FETCH_ASSOC);


        } else if ($hareket_tur == 4) {

            $sql = "SELECT tahsilatlar.* , cari.cari_adi FROM tahsilatlar INNER JOIN cari ON tahsilatlar.cari_id = cari.id WHERE tahsilatlar.remove = 0 and tahsilatlar.owner_id = :owner and tahsilatlar.islem_tarih >= :baslama and tahsilatlar.islem_tarih <= :bitis and tahsilatlar.cari_id = :cari_id ";

            $query = $this->getConnection()->prepare($sql);

            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);


            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else if ($hareket_tur == 5) {



            $sql = "SELECT *  FROM odemeler WHERE remove = 0 and  owner_id = :owner and created_date >= :baslama and created_date <= :bitis and cari_id = :cari_id ";

            $query = $this->getConnection()->prepare($sql);

            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);


            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else if ($hareket_tur == 6) {




            $sql = "SELECT islem_tarih ,islem_tutar  , ('tahsil') as tip  FROM tahsilatlar WHERE tahsilatlar.remove = 0 and  tahsilatlar.owner_id = :owner and tahsilatlar.islem_tarih >= :baslama and tahsilatlar.islem_tarih <= :bitis and tahsilatlar.cari_id = :cari_id ";
            $query = $this->getConnection()->prepare($sql);
            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);
            $query->execute();
            $tahsilatlar = $query->fetchAll(PDO::FETCH_ASSOC);



            $sql2 = "SELECT odeme_tarih as islem_tarih , odeme_tutar as islem_tutar , ('odeme') as tip  FROM odemeler WHERE remove = 0 and owner_id = :owner and odeme_tarih >= :baslama and odeme_tarih <= :bitis and cari_id = :cari_id ";
            $query1 = $this->getConnection()->prepare($sql2);
            $query1->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query1->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query1->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query1->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);

            $query1->execute();

            $odemeler = $query1->fetchAll(PDO::FETCH_ASSOC);


            return array_merge($tahsilatlar, $odemeler);
        }else if ($hareket_tur == 7) {

            $bas_tarih = date("Y-m-d H:i:s", strtotime($baslama . " ".$bas_saat));
            $bit_tarih = date("Y-m-d H:i:s", strtotime($bitis . " ".$bit_saat));
            $owner_id = Controller::$userInfo['owner_id'];



            $sql12 = "SELECT "
                    . "stok_haraket_giris.giris_evrak_no as evrakno , "
                    . "stok.stok_adi as stok ,  "
                    . "stok_haraket_giris.alim_evrak_id as evrak_id ,  "
                     . "stok_haraket_giris.alis_fiyati as islem_tutar ,"
                    . "stok_haraket_giris.kdv_oran as vergi ,"
                     . "stok_haraket_giris.adet as adet ,"
                    . "stok_haraket_giris.giris_tarih as islem_tarih ,  ('alisf') as tip "
                    . "FROM "
                    . "stok_haraket_giris "
                    . "INNER JOIN stok ON stok_haraket_giris.stok_id = stok.id "
                    . "WHERE (stok_haraket_giris.giris_tarih >= ? and stok_haraket_giris.giris_tarih <= ?)  and "
                    . "stok_haraket_giris.remove = 0 and "
                    . "stok_haraket_giris.owner_id = ? and "
                    . "stok_haraket_giris.cari_id = ? ";
            
            
            
            
            $query12 = $this->getConnection()->prepare($sql12);
            $query12->execute([$bas_tarih, $bit_tarih, $owner_id, $cari_id]);
            $alimlar = $query12->fetchAll(PDO::FETCH_ASSOC);



            $sql11 = "SELECT "
                    . "stok_haraket_cikis.cikis_evrak_no as evrakno , "
                    . "stok.stok_adi as stok ,  "
                    . "stok_haraket_cikis.satis_evrak_id as evrak_id ,"
                    . "stok_haraket_cikis.satis_fiyati as islem_tutar ,"
                    . "stok_haraket_cikis.kdv_oran as vergi ,"
                    . "stok_haraket_cikis.adet as adet ,"
                    . "stok_haraket_cikis.cikis_tarih as islem_tarih ,  "
                    . "('satisf') as tip "
                    . "FROM stok_haraket_cikis  "
                    . "INNER JOIN stok ON  stok_haraket_cikis.stok_id = stok.id "
                    . "WHERE (stok_haraket_cikis.cikis_tarih >= ? and stok_haraket_cikis.cikis_tarih <= ?)  "
                    . "and "
                    . "stok_haraket_cikis.remove = 0 and "
                    . "stok_haraket_cikis.owner_id = ? and "
                    . "stok_haraket_cikis.cari_id = ? ";
            
            
            $query11 = $this->getConnection()->prepare($sql11);
            $query11->execute([$bas_tarih, $bit_tarih, $owner_id, $cari_id]);
            $satislar = $query11->fetchAll(PDO::FETCH_ASSOC);


            $sql = "SELECT islem_tarih ,islem_tutar  , ('tahsil') as tip , ('') as evrakno FROM tahsilatlar WHERE tahsilatlar.remove = 0 and tahsilatlar.owner_id = :owner and tahsilatlar.islem_tarih >= :baslama and tahsilatlar.created_date <= :bitis and tahsilatlar.cari_id = :cari_id ";
            $query = $this->getConnection()->prepare($sql);
            $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);
            $query->execute();
            $tahsilatlar = $query->fetchAll(PDO::FETCH_ASSOC);



            $sql2 = "SELECT odeme_tarih as islem_tarih , odeme_tutar as islem_tutar , ('odeme') as tip  , ('') as evrakno FROM odemeler WHERE remove = 0 and owner_id = :owner and created_date >= :baslama and created_date <= :bitis and cari_id = :cari_id ";
            $query1 = $this->getConnection()->prepare($sql2);
            $query1->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
            $query1->BindParam(":baslama", $baslama, PDO::PARAM_STR);
            $query1->BindParam(":bitis", $bitis, PDO::PARAM_STR);
            $query1->BindParam(":cari_id", $cari_id, PDO::PARAM_STR);
            $query1->execute();


            $odemeler = $query1->fetchAll(PDO::FETCH_ASSOC);

            $result = [];



            $faturalar = array_merge($alimlar, $satislar);


            $finansal = array_merge($tahsilatlar, $odemeler);


            $genel = array_merge($faturalar, $finansal);


            $tmpArray = array();

            foreach ($genel as $key => $value) {

                $tmpArray[$key] = $value['islem_tarih'];
            }

            array_multisort($tmpArray, SORT_ASC, $genel);



            return $genel;
        } 
    }

}
