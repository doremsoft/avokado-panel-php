<?php

use Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class homeModel extends Dimodel {
    
    private $owner;
    
    public function __construct() {
     parent::__construct();
              $this->owner =  Controller::$userInfo['owner_id'];
    }

    public function duyulariAl() {

        $sql = "SELECT * FROM duyurular WHERE remove = 0 ORDER BY id DESC LIMIT 5";
        $query = $this->getConnection()->prepare($sql);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sonyedigunSatisRaporlari() {

        $gun = date('Y-m-d', strtotime(date("Y/m/d") . "-7 days"));
        $gun1 = date('Y-m-d', strtotime(date("Y/m/d") . "-6 days"));
        $gun2 = date('Y-m-d', strtotime(date("Y/m/d") . "-5 days"));
        $gun3 = date('Y-m-d', strtotime(date("Y/m/d") . "-4 days"));
        $gun4 = date('Y-m-d', strtotime(date("Y/m/d") . "-3 days"));
        $gun5 = date('Y-m-d', strtotime(date("Y/m/d") . "-2 days"));
        $gun6 = date('Y-m-d', strtotime(date("Y/m/d") . "-1 days"));
        $gun7 = date('Y-m-d');


        $gunler = [
            $gun => 0,
            $gun1 => 0,
            $gun2 => 0,
            $gun3 => 0,
            $gun4 => 0,
            $gun5 => 0,
            $gun6 => 0,
            $gun7 => 0,
        ];



        $sql = "SELECT  
  DATE(tarih) as date , 
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
) as totalCount   
 FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id 
WHERE  
stok_haraket_cikis.owner_id = ? and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.remove = 0 and 
satis_evraklari.owner_id = ? and satis_evraklari.tarih >= ? AND satis_evraklari.tarih <= ?  GROUP BY DATE(satis_evraklari.created_date)";

        $query1 = $this->getConnection()->prepare($sql);
        $query1->execute([$this->owner,$this->owner,$gun, $gun7]);
        $result = $query1->fetchAll(PDO::FETCH_ASSOC);


        if ($result) {

            foreach ($result as $key => $value) {

                $gun = $value["date"];
                $satis = $value["totalCount"];



                $gunler[$gun] =  number_format($satis, 2, '.', '');
            }
        }


        return $gunler;
    }

    public function satisRaporlari() {




        $sql = "SELECT  
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
) as toplam   
 FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id 
WHERE  
stok_haraket_cikis.owner_id = ? and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.remove = 0 and 
satis_evraklari.owner_id = ?";




        $query = $this->getConnection()->prepare($sql);

        $query->execute([$this->owner,$this->owner]);

        $satislar_result = $query->fetch(PDO::FETCH_ASSOC);

        if ($satislar_result['toplam']) {

            $toplam = $satislar_result['toplam'];
        } else {
            $toplam = 0;
        }




        $sql2 = "SELECT  
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
) as toplam   
 FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id 
WHERE  
stok_haraket_cikis.owner_id = ? and 
stok_haraket_cikis.remove = 0 and 
satis_evraklari.remove = 0 and 
satis_evraklari.owner_id = ? and satis_evraklari.tarih= ? ";


        $query2 = $this->getConnection()->prepare($sql2);

        $query2->execute([$this->owner,$this->owner,date("Y-m-d")]);

        $bugun_satislar_result = $query2->fetch(PDO::FETCH_ASSOC);

        if ($bugun_satislar_result['toplam']) {

            $bugun = $bugun_satislar_result['toplam'];
        } else {

            $bugun = 0;
        }


        $sql3 = "SELECT count(id) as toplam FROM stok WHERE  remove = 0 and owner_id = ?";

        $query3 = $this->getConnection()->prepare($sql3);

        $query3->execute([$this->owner]);

        $toplam_stoklar = $query3->fetch(PDO::FETCH_ASSOC);

        if ($toplam_stoklar['toplam']) {

            $toplam_stok = $toplam_stoklar['toplam'];
        } else {

            $toplam_stok = 0;
        }



        $sql4 = "SELECT count(id) as toplam FROM cari WHERE  remove = 0 and owner_id = ?";

        $query4 = $this->getConnection()->prepare($sql4);

        $query4->execute([$this->owner]);

        $toplam_cari_hesap = $query4->fetch(PDO::FETCH_ASSOC);

        if ($toplam_cari_hesap['toplam']) {

            $toplam_cari = $toplam_cari_hesap['toplam'];
        } else {

            $toplam_cari = 0;
        }




        return ['bugun' => $bugun, 'toplam' => $toplam, 'toplam_stok' => $toplam_stok, 'toplam_cari' => $toplam_cari];
    }

    
        public function test() {
            
       
        $sql = "SELECT stok.* , id as stok_id ,stok_parent_id as s_pid , IF(stok_parent_id=0,stok_adi,(select stok_adi from stok where id = s_pid LIMIT 1)) AS stok_adi ,stok_kod , stok_varyant_adi FROM stok  WHERE remove = 0 and owner_id = ?";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([$this->owner]);

        $sonuc = $query->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        var_dump($sonuc);
        
             echo "</pre>";
        }
}
