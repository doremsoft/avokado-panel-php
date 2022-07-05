<?php

use \Dipa\Db\Model;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class stokRaporModel extends Model {

    public function detayliStokHaraketGoster($stok_id, $sdepo, $bas_tarih, $bit_tarih, $stur) {

        $gdepo = "";
        $cdepo = "";
        
        
        $bas_tarih = $bas_tarih." 00:00:00";
        $bit_tarih = $bit_tarih." 23:59:59";
        
        
        
      $girislertarih = " AND stok_haraket_giris.giris_tarih >= :baslama AND stok_haraket_giris.giris_tarih <= :bitis ";

      $cikislartarih = " AND stok_haraket_cikis.cikis_tarih >= :baslama AND stok_haraket_cikis.cikis_tarih <= :bitis ";

            
           
        if ($sdepo != "all") {


            $gdepo = " AND stok_haraket_giris.depo = $sdepo  ";
            $cdepo = " AND stok_haraket_cikis.depo = $sdepo  ";
        }

        if ($stur == "1") {


            
         
            $girissql = "SELECT  stok_haraket_giris.giris_tarih as tarih ,stok_haraket_giris.aciklama  , stok_haraket_giris.giris_evrak_no as evrak,
                        stok_haraket_giris.adet as adet, stok_haraket_giris.alim_evrak_id as evrak_id ,
                         cari.id as cari_id ,  cari.cari_adi , ('giris') as tip , stok_depolar.id as depo_id , stok_depolar.stok_depo_adi as depo_adi "
                    . "FROM stok_haraket_giris "
                    . "LEFT JOIN cari ON stok_haraket_giris.cari_id = cari.id "
                    . "LEFT JOIN stok_depolar ON stok_haraket_giris.depo = stok_depolar.id "
                    . "WHERE stok_haraket_giris.stok_id = :stok_id and stok_haraket_giris.remove = 0 and stok_haraket_giris.owner_id = :owner $girislertarih $gdepo ";





            $query = $this->getConnection()->prepare($girissql);
            $query->BindValue(":stok_id", $stok_id, PDO::PARAM_INT);
            $query->BindValue(":owner", (int) Controller::$userInfo['owner_id'], PDO::PARAM_INT);
            $query->BindValue(":baslama", $bas_tarih, PDO::PARAM_STR);
            $query->BindValue(":bitis", $bit_tarih, PDO::PARAM_STR);

            $query->execute();
            $giris_result = $query->fetchAll(PDO::FETCH_ASSOC);



            $cikissql = "SELECT stok_haraket_cikis.cikis_tarih as tarih , stok_haraket_cikis.cikis_evrak_no as evrak , stok_haraket_cikis.satis_evrak_id as evrak_id ,
                    stok_haraket_cikis.adet as adet,
                     cari.cari_adi  , ('cikis') as tip  , stok_depolar.id as depo_id , stok_depolar.stok_depo_adi as depo_adi "
                . "FROM stok_haraket_cikis "
                . "LEFT JOIN cari ON stok_haraket_cikis.cari_id = cari.id "
                . "LEFT JOIN stok_depolar ON stok_haraket_cikis.depo = stok_depolar.id "
                . "WHERE stok_haraket_cikis.stok_id = :stok_id and stok_haraket_cikis.remove = 0 and stok_haraket_cikis.owner_id = :owner $cikislartarih $cdepo ";



            $cikisquery = $this->getConnection()->prepare($cikissql);
            $cikisquery->BindValue(":stok_id", $stok_id, PDO::PARAM_INT);
            $cikisquery->BindValue(":owner", (int) Controller::$userInfo['owner_id'], PDO::PARAM_INT);
            $cikisquery->BindValue(":baslama", $bas_tarih, PDO::PARAM_STR);
            $cikisquery->BindValue(":bitis", $bit_tarih, PDO::PARAM_STR);


            $cikisquery->execute();
            $cikis_result = $cikisquery->fetchAll(PDO::FETCH_ASSOC);

            $sonuc =  array_merge($giris_result, $cikis_result);





            return $sonuc;
        } else if ($stur == "2") {

            $sql = "SELECT  stok_haraket_giris.giris_tarih as tarih , stok_haraket_giris.giris_evrak_no as evrak,
                        stok_haraket_giris.adet as adet,
                         cari.cari_adi , ('giris') as tip , stok_depolar.id as depo_id , stok_depolar.stok_depo_adi as depo_adi "
                    . "FROM stok_haraket_giris "
                    . "LEFT JOIN cari ON stok_haraket_giris.cari_id = cari.id "
                    . "LEFT JOIN stok_depolar ON stok_haraket_giris.depo = stok_depolar.id "
                    . "WHERE stok_haraket_giris.stok_id = :stok_id and stok_haraket_giris.remove = 0 and stok_haraket_giris.owner_id = :owner $girislertarih $gdepo ";
        } else if ($stur == "3") {

            $sql = "SELECT stok_haraket_cikis.cikis_tarih as tarih , stok_haraket_cikis.cikis_evrak_no as evrak,
                    stok_haraket_cikis.adet as adet,
                     cari.cari_adi  , ('cikis') as tip  , stok_depolar.id as depo_id , stok_depolar.stok_depo_adi as depo_adi "
                    . "FROM stok_haraket_cikis "
                    . "LEFT JOIN cari ON stok_haraket_cikis.cari_id = cari.id "
                    . "LEFT JOIN stok_depolar ON stok_haraket_cikis.depo = stok_depolar.id "
                    . "WHERE stok_haraket_cikis.stok_id = :stok_id and stok_haraket_cikis.remove = 0 and stok_haraket_cikis.owner_id = :owner $cikislartarih $cdepo ";
        }






        if ($stur == "2" || $stur == "3") {

            $query = $this->getConnection()->prepare($sql);
            $query->BindValue(":stok_id", $stok_id, PDO::PARAM_INT);
            $query->BindValue(":owner", (int) Controller::$userInfo['owner_id'], PDO::PARAM_INT);

        
             $query->BindValue(":baslama", $bas_tarih, PDO::PARAM_STR);
            $query->BindValue(":bitis", $bit_tarih, PDO::PARAM_STR);
            




            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }

}
