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

  

        $sql = " SELECT DATE(tarih) as date , SUM(genel_toplam) as totalCount FROM satis_evraklari WHERE tarih >= ? AND tarih <= ? AND owner_id = ? GROUP BY DATE(created_date)";


        $query1 = $this->getConnection()->prepare($sql);
        $query1->execute([$gun, $gun7, $this->owner]);
        $result = $query1->fetchAll(PDO::FETCH_ASSOC);


        if ($result) {

            foreach ($result as $key => $value) {

                $gun = $value["date"];
                $satis = $value["totalCount"];
                $gunler[$gun] = $satis;
            }
        }


        return $gunler;
    }

    public function satisRaporlari() {



        $sql = "SELECT sum(genel_toplam) as toplam FROM satis_evraklari WHERE remove = 0 and owner_id = ?";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([$this->owner]);

        $satislar_result = $query->fetch(PDO::FETCH_ASSOC);

        if ($satislar_result['toplam']) {

            $toplam = $satislar_result['toplam'];
        } else {
            $toplam = 0;
        }


        $sql2 = "SELECT sum(genel_toplam) as toplam FROM satis_evraklari WHERE tarih= ? AND remove = 0  and owner_id = ?";

        $query2 = $this->getConnection()->prepare($sql2);

        $query2->execute([date("Y-m-d"),$this->owner]);

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

}
