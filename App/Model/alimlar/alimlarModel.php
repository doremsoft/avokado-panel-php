<?php

use Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class alimlarModel extends Dimodel {
  
        
    public function ikitariheGoreAlimlarOzet($bas_tarih,$bit_tarih){


        $sql = "SELECT  
alim_evraklari.* , stok_haraket_giris.owner_id , 
SUM(((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet))) as ara_toplam ,
SUM(stok_haraket_giris.indirim_tutari  * stok_haraket_giris.adet) as indirim_toplam , 
SUM(
(((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100
) as vergi_toplam  ,
SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
) as genel_toplam   
 FROM alim_evraklari 
INNER JOIN stok_haraket_giris  ON  alim_evraklari.id = stok_haraket_giris.alim_evrak_id 
WHERE  
stok_haraket_giris.owner_id = ? and 
stok_haraket_giris.remove = 0 and 
alim_evraklari.remove = 0 and 
(alim_evraklari.tarih >= ? and alim_evraklari.tarih <= ?) and alim_evraklari.remove = 0 and
alim_evraklari.owner_id = ?";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih, Controller::$userInfo["owner_id"]]);

        return $query->fetch(PDO::FETCH_ASSOC);


        
        
    }

    public function ikitariheGoreAlimlar($bas_tarih,$bit_tarih) {
        
        
        $sql = "SELECT 
alim_evraklari.* , cari.cari_adi ,
(
SELECT  
(

SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)   

) as genel_toplam FROM  stok_haraket_giris WHERE alim_evrak_id  = alim_evraklari.id and remove = 0 and owner_id = alim_evraklari.owner_id 
 )  as genel_toplam
 
 
 
FROM alim_evraklari 
INNER JOIN cari ON alim_evraklari.cari_id = cari.id 
WHERE 
(alim_evraklari.tarih >= ? and alim_evraklari.tarih <= ?) 
and alim_evraklari.remove = 0  and alim_evraklari.owner_id = ? 
ORDER BY alim_evraklari.id DESC";
        
        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"]]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }





    
            
    public function ikitariheGoreAlimlarOzetUbr($bas_tarih,$bit_tarih){


        $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." 00:01:00"));

        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." 23:59:59"));


        $sql = "SELECT 
 stok_haraket_giris.owner_id , 
SUM(((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet))) as ara_toplam ,
SUM(stok_haraket_giris.indirim_tutari  * stok_haraket_giris.adet) as indirim_toplam , 
SUM(
(((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100
) as vergi_toplam  ,
SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
) as genel_toplam   
 FROM stok_haraket_giris WHERE (stok_haraket_giris.giris_tarih >= ? and stok_haraket_giris.giris_tarih <= ?) and remove = 0";

        $query= $this->getConnection()->prepare($sql);

        $query->execute([$bas_tarih,$bit_tarih]);

        return $query->fetch(PDO::FETCH_ASSOC);




    }

    public function ikitariheGoreAlimlarUbr($bas_tarih,$bit_tarih) {
        
        
        $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." 00:01:00"));
        
        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." 23:59:59"));
        
        
        $sql = "SELECT stok_haraket_giris.id,stok_haraket_giris.alim_evrak_id,"
                . "stok_haraket_giris.giris_tarih, stok_haraket_giris.adet , 
                stok_haraket_giris.alis_fiyati , stok_haraket_giris.kdv_oran ,
                stok_haraket_giris.stok_id , stok_haraket_giris.giris_evrak_no , stok_haraket_giris.vergi_tutari , stok_haraket_giris.indirim_tutari , "
                .Controller::helper(null,"stokModelHelper")->getStokVaryantSelectName()." FROM stok_haraket_giris INNER JOIN stok ON stok_haraket_giris.stok_id = stok.id  LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  WHERE (stok_haraket_giris.giris_tarih >= ? and stok_haraket_giris.giris_tarih <= ?)  and stok_haraket_giris.remove = 0 ORDER BY stok_haraket_giris.id DESC";
        
        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([$bas_tarih,$bit_tarih]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }

    
}
