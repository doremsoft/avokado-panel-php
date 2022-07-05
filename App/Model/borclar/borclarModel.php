<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class borclarModel extends Dimodel {
 

    public function getBorclarList(){


        $sql = "SELECT 
(SELECT sum(odeme_tutar) as tutar FROM odemeler WHERE cari_id = cari.id and remove = 0 and owner_id = ? )  as odeme_tutar   ,

(SELECT sum(islem_tutar) as tutar FROM tahsilatlar WHERE cari_id = cari.id and remove = 0 and owner_id = ?)  as tahsilat_tutar  , 
  
(SELECT SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)  as total  FROM stok_haraket_giris WHERE cari_id = cari.id and remove = 0 and owner_id = ? ) as toplam_borc_bakiyesi ,
  
(SELECT   SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) +
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)   as total   FROM stok_haraket_cikis WHERE cari_id = cari.id and remove = 0 and owner_id = ? ) as toplam_alacak_bakiyesi ,
cari.id as cari_id ,cari.cari_adi 
  
FROM cari 

WHERE  cari.owner_id = ? and cari.remove = 0 

 ";




        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([
            Controller::$userInfo['owner_id'],
            Controller::$userInfo['owner_id'],
            Controller::$userInfo['owner_id'],
            Controller::$userInfo['owner_id'],
            Controller::$userInfo['owner_id']]);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);


        return $result;
        
        
    }
    
        public function getGuncelBorclarList(){
        

   
        $sql="SELECT 
alim_evraklari.* ,
 SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)  as toplam_borc_bakiyesi , 
cari.id as cari_id ,cari.cari_adi,(
SELECT sum(odeme_tutar) as tutar FROM odemeler WHERE cari_id = cari.id and remove = 0 and owner_id = ? )  as odeme_tutar  
FROM alim_evraklari 
LEFT JOIN stok_haraket_giris ON alim_evraklari.id = stok_haraket_giris.alim_evrak_id 
INNER JOIN cari ON alim_evraklari.cari_id = cari.id WHERE alim_evraklari.vade_tarih <= ? and alim_evraklari.owner_id = ? GROUP BY alim_evraklari.cari_id  ";

        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([Controller::$userInfo['owner_id'],date("Y-m-d"),Controller::$userInfo['owner_id']]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
        
        
    }
}
