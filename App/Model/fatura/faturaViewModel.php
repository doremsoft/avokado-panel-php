<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class faturaViewModel extends Dimodel {
/*
 * Controller::$userInfo
 */
    
 


    public function getFatura($fatura_id,$type)
{
    
    if($type == "satis"){
        

        $sql = "SELECT  satis_evraklari.* , stok_haraket_cikis.owner_id , 

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
satis_evraklari.id = ?  and 
satis_evraklari.owner_id = ?";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo["owner_id"],$fatura_id, Controller::$userInfo["owner_id"]]);

        return $query->fetch();


        
   
    }else if($type == "alim"){

        /*
         * ara_toplam
         * indirim_toplam
         * vergi_toplam
         * genel_toplam
         */

        $sql = "SELECT  alim_evraklari.* , stok_haraket_giris.owner_id , 
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
alim_evraklari.id = ?  and 
alim_evraklari.owner_id = ?";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo["owner_id"],$fatura_id, Controller::$userInfo["owner_id"]]);

        return $query->fetch();
        
    }
  
}



public function getFaturaKalemleri($fatura_id,$type)
{

    
    
    if($type == "satis"){
        
        return $this->reset()
                ->disableDefault()
                ->table("stok_haraket_cikis",Controller::$userInfo)
                ->select("stok_haraket_cikis.* , ".Controller::helper(null,"stokModelHelper")->getStokVaryantSelectName()." ")
                ->innerjoin(" INNER JOIN stok ON stok_haraket_cikis.stok_id = stok.id ")
            ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->where([
                    'stok_haraket_cikis.satis_evrak_id'=>['=',$fatura_id],
                    'stok_haraket_cikis.remove'=>['=',0],
                    'stok_haraket_cikis.owner_id'=>['=',Controller::$userInfo["owner_id"]]
                        
                        ])->getAll();
        
   
    }else if($type == "alim"){
        //getStokVaryantSelectName()
      
        return $this->reset()
                ->disableDefault()
                ->table("stok_haraket_giris",Controller::$userInfo)
                ->select("stok_haraket_giris.* , ". Controller::helper(null,"stokModelHelper")->getStokVaryantSelectName()." ")
                ->innerjoin(" INNER JOIN stok ON stok_haraket_giris.stok_id = stok.id  ")
            ->leftjoin(["stok as lst" => "stok.stok_parent_id = lst.id"])
                ->where([
                    'stok_haraket_giris.alim_evrak_id'=>['=',$fatura_id],
                    'stok_haraket_giris.remove'=>['=',0],
                     'stok_haraket_giris.owner_id'=>['=',Controller::$userInfo["owner_id"]]
                        ])->getAll();
        
        
        
    }
  
}


public function ikitariheGoreFaturalar($bas_tarih,$bit_tarih, $type){

if($type == 1){
    $sql = "SELECT 
alim_evraklari.* , cari.cari_adi ,
(
SELECT  
(

SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)   

) as genel_toplam FROM  stok_haraket_giris WHERE   alim_evrak_id  = alim_evraklari.id and remove = 0 and owner_id = alim_evraklari.owner_id 
 )  as genel_toplam
 
 
 
FROM alim_evraklari 
INNER JOIN cari ON alim_evraklari.cari_id = cari.id 
WHERE 
(alim_evraklari.tarih >= ? and alim_evraklari.tarih <= ?) 
and alim_evraklari.remove = 0  and alim_evraklari.owner_id = ? and alim_evraklari.evrak_tur = 2  
ORDER BY alim_evraklari.id DESC";

    $query= $this->getConnection()->prepare($sql);
    $query->execute([$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"]]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}else if($type == 2){

    $sql = "SELECT satis_evraklari.* , 
cari.cari_adi , cari.id as cari_id ,  

(
SELECT  
(

SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)   

) as genel_toplam FROM  stok_haraket_cikis WHERE  satis_evrak_id  = satis_evraklari.id and remove = 0 and owner_id = satis_evraklari.owner_id 
 )  as genel_toplam 
 


FROM satis_evraklari 
LEFT JOIN cari ON satis_evraklari.cari_id = cari.id 
WHERE 
(satis_evraklari.tarih >= ? and satis_evraklari.tarih <= ?)  and satis_evraklari.evrak_tur = 2   
and satis_evraklari.remove = 0  and satis_evraklari.owner_id = ? ORDER BY satis_evraklari.id DESC";

    $query= $this->getConnection()->prepare($sql);
    $query->execute([$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"]]);
    return $query->fetchAll(PDO::FETCH_ASSOC);


}




}


    public function getSatisFaturalari(){
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

) as genel_toplam  , DATE_FORMAT(stok_haraket_cikis.cikis_tarih , '%m') as satistarih    ,stok_haraket_cikis.satis_evrak_id 
 
FROM stok_haraket_cikis 

INNER JOIN satis_evraklari ON stok_haraket_cikis.satis_evrak_id = satis_evraklari.id 

WHERE  
stok_haraket_cikis.owner_id = ?  and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.evrak_tur = 2 and   
stok_haraket_cikis.cikis_tarih >= ? and 
stok_haraket_cikis.cikis_tarih <= ?  GROUP BY 
satistarih


";



        $query= $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);


        if($result){
            foreach ($result as $key => $val){


                $ay = intval($val["satistarih"]);

                $takvim[$ay] = number_format($val["genel_toplam"], 2, '.', '');

            }

        }


        return $takvim;


    }


    public function getAlimFaturalari(){
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
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
  )

) as genel_toplam  , DATE_FORMAT(stok_haraket_giris.giris_tarih , '%m') as alimtarih    , stok_haraket_giris.alim_evrak_id 
 
FROM stok_haraket_giris 

INNER JOIN alim_evraklari ON stok_haraket_giris.alim_evrak_id = alim_evraklari.id 

WHERE  
stok_haraket_giris.owner_id = ?  and 
stok_haraket_giris.remove = 0 and 
alim_evraklari.evrak_tur = 2 and   
stok_haraket_giris.giris_tarih >= ? and 
stok_haraket_giris.giris_tarih <= ?  GROUP BY 
alimtarih


";



        $query= $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);


        if($result){
            foreach ($result as $key => $val){


                $ay = intval($val["alimtarih"]);

                $takvim[$ay] = number_format($val["genel_toplam"], 2, '.', '');

            }

        }


        return $takvim;


    }

    
}
