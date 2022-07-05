<?php

use Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class satislarModel extends Dimodel {
    

    
    
        
    public function ikitariheGoreSatislarOzet($bas_tarih,$bit_tarih, $bas_saat , $bit_saat){
        
              $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." ".$bas_saat));
        
        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." ".$bit_saat));


        $sql = "SELECT  
satis_evraklari.* , stok_haraket_cikis.owner_id , 
SUM(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet))) as ara_toplam ,
SUM(stok_haraket_cikis.indirim_tutari  * stok_haraket_cikis.adet) as indirim_toplam , 
SUM(
(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100
) as vergi_toplam  ,
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
) as genel_toplam   
 FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id 
WHERE  
stok_haraket_cikis.owner_id = ? and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.remove = 0 and 
(satis_evraklari.tarih >= ? and satis_evraklari.tarih <= ?) and satis_evraklari.remove = 0 and
satis_evraklari.owner_id = ?";


        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"]]);

        return $query->fetch(PDO::FETCH_ASSOC);
        
        
    }

    public function ikitariheGoreSatislar($bas_tarih,$bit_tarih, $bas_saat , $bit_saat) {
        
              $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." ".$bas_saat));
        
        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." ".$bit_saat));
        
        $sql = "SELECT satis_evraklari.* , 
cari.cari_adi , cari.id as cari_id ,  

(
SELECT  
(

SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)   

) as genel_toplam FROM  stok_haraket_cikis WHERE satis_evrak_id  = satis_evraklari.id and remove = 0 and owner_id = satis_evraklari.owner_id 
 )  as genel_toplam 
 


FROM satis_evraklari LEFT JOIN cari ON satis_evraklari.cari_id = cari.id WHERE (satis_evraklari.evrak_zamani >= ? and satis_evraklari.evrak_zamani <= ?)  and satis_evraklari.remove = 0 ORDER BY satis_evraklari.id DESC";

        
        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([$bas_tarih,$bit_tarih]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }



    public function ikitariheGoreSatislarAnaliz($bas_tarih,$bit_tarih, $bas_saat , $bit_saat) {

        $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." ".$bas_saat));

        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." ".$bit_saat));


        $sql = "SELECT  
satis_evraklari.tarih , 
SUM(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet))) as ara_toplam ,
SUM(stok_haraket_cikis.indirim_tutari  * stok_haraket_cikis.adet) as indirim_toplam , 
SUM(
(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100
) as vergi_toplam  ,
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
) as genel_toplam   
 FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id 
WHERE  
stok_haraket_cikis.owner_id = ? and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.remove = 0 and 
(satis_evraklari.evrak_zamani >= ? and satis_evraklari.evrak_zamani <= ?) and satis_evraklari.remove = 0 and
satis_evraklari.owner_id = ? ";


        $query= $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"]]);


        return $query->fetchAll(PDO::FETCH_ASSOC);

    }



    public function ikitariheGoreSatislarOzetUbr($bas_tarih,$bit_tarih, $bas_saat , $bit_saat){
        
        
          $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." ".$bas_saat));
        
        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." ".$bit_saat));



        $sql = "SELECT  
satis_evraklari.* , stok_haraket_cikis.owner_id , 
SUM(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet))) as ara_toplam ,
SUM(stok_haraket_cikis.indirim_tutari  * stok_haraket_cikis.adet) as indirim_toplam , 
SUM(
(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100
) as vergi_toplam  ,
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
) as genel_toplam   
 FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id 
WHERE  
stok_haraket_cikis.owner_id = ? and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.remove = 0 and 
(satis_evraklari.evrak_zamani >= ? and satis_evraklari.evrak_zamani <= ?) and satis_evraklari.remove = 0 and
satis_evraklari.owner_id = ?";



        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"]]);

        return $query->fetch(PDO::FETCH_ASSOC);
        
        
    }

    public function ikitariheGoreSatislarUbr($bas_tarih,$bit_tarih, $bas_saat , $bit_saat) {
        
        
        $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." ".$bas_saat));
        
        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." ".$bit_saat));
        
        
        $sql = "SELECT stok_haraket_cikis.id,stok_haraket_cikis.satis_evrak_id,"
                . "stok_haraket_cikis.cikis_tarih, stok_haraket_cikis.adet , stok_haraket_cikis.satis_fiyati , stok_haraket_cikis.kdv_oran ,stok_haraket_cikis.stok_id , stok_haraket_cikis.cikis_evrak_no , "
                .Controller::helper(null,"stokModelHelper")->getStokVaryantSelectName()." FROM stok_haraket_cikis INNER JOIN stok ON stok_haraket_cikis.stok_id = stok.id  LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  WHERE (stok_haraket_cikis.cikis_tarih >= ? and stok_haraket_cikis.cikis_tarih <= ?)  and stok_haraket_cikis.remove = 0 and stok_haraket_cikis.ic_transfer = 0  ORDER BY stok_haraket_cikis.id DESC";
        
        $query= $this->getConnection()->prepare($sql);
       
        $query->execute([$bas_tarih,$bit_tarih]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }


    public function ikitariheGoreEnCokSatanlar($bas_tarih,$bit_tarih, $bas_saat , $bit_saat) {

        $bas_tarih = date("Y-m-d H:i:s",strtotime($bas_tarih." ".$bas_saat));

        $bit_tarih = date("Y-m-d H:i:s",strtotime($bit_tarih." ".$bit_saat));


        $sql = "SELECT  
satis_evraklari.tarih , 
SUM(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet))) as ara_toplam ,
SUM(stok_haraket_cikis.indirim_tutari  * stok_haraket_cikis.adet) as indirim_toplam , 
SUM(
(((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100
) as vergi_toplam  ,
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
) as genel_toplam   
 FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id 
WHERE  
stok_haraket_cikis.owner_id = ? and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.remove = 0 and 
(satis_evraklari.evrak_zamani >= ? and satis_evraklari.evrak_zamani <= ?) and satis_evraklari.remove = 0 and
satis_evraklari.owner_id = ? GROUP BY satis_evraklari.tarih";


        $query= $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"]]);


        return $query->fetchAll(PDO::FETCH_ASSOC);

    }

    public function satislar(){
        $year = date("Y");

        $bas_tarih = $year."-01-01 00:00:01";
        $bit_tarih = $year."-12-31 23:59:59";

        $takvim = [
            '1'=>"0",
            '2'=>"0",
            '3'=>"0",
            '4'=>"0",
            '5'=>"0",
            '6'=>"0",
            '7'=>"0",
            '8'=>"0",
            '9'=>"0",
            '10'=>"0",
            '11'=>"0",
            '12'=>"0"
        ];




        $sql="SELECT  
(
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
  )

) as genel_toplam  , DATE_FORMAT(cikis_tarih , '%m') as tarih    ,satis_evrak_id 
 
FROM stok_haraket_cikis WHERE  owner_id = ?  and remove = 0 and  cikis_tarih >= ? and cikis_tarih <= ?  GROUP BY tarih


";



        $query= $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);


        if($result){
            foreach ($result as $key => $val){


                $ay = intval($val["tarih"]);

                $takvim[$ay] = number_format($val["genel_toplam"], 2, '.', '');

            }

        }



        return $takvim;


    }

    public function sonSatislar(){

        $sql = "SELECT satis_evraklari.* , 
cari.cari_adi , cari.id as cari_id ,  

(
SELECT  
(

SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)   

) as genel_toplam FROM  stok_haraket_cikis WHERE satis_evrak_id  = satis_evraklari.id and remove = 0 and owner_id = satis_evraklari.owner_id 
 )  as genel_toplam 
 


FROM satis_evraklari LEFT JOIN cari ON satis_evraklari.cari_id = cari.id WHERE satis_evraklari.remove = 0  and satis_evraklari.cari_id != 0 ORDER BY satis_evraklari.id DESC LIMIT 5";


        $query= $this->getConnection()->prepare($sql);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);


    }
}
