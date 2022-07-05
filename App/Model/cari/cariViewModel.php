<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class cariViewModel extends Dimodel {

    public function getCariList($tip) {


        if($tip == NULL){
                   return $this->table("cari", Controller::$userInfo)
                        ->select("id,cari_adi,cari_kod,cari_telefon,cari_turu")
                        ->paginate(Controller::$http_request, 20);
            
        }else{
            
                   return $this->table("cari", Controller::$userInfo)
                        ->select("id,cari_adi,cari_kod,cari_telefon,cari_turu")
                           ->where(["cari_turu"=>["=",$tip]])
                        ->paginate(Controller::$http_request, 20);
        }
 
    }

    public function getCari($id) {

        return $this->table("cari", Controller::$userInfo)->find($id, TRUE);
    }


    public function toplam_cari_sayisi(){

        $sql4 = "SELECT count(id) as toplam FROM cari WHERE  remove = 0 and owner_id = ?";

        $query4 = $this->getConnection()->prepare($sql4);

        $query4->execute([Controller::$userInfo["owner_id"]]);

        $toplam_cari_hesap = $query4->fetch(PDO::FETCH_ASSOC);

        if ($toplam_cari_hesap['toplam']) {

            $toplam_cari = $toplam_cari_hesap['toplam'];
        } else {

            $toplam_cari = 0;
        }


return $toplam_cari;

    }

    public function cariHesapOzeti($cari_id) {

        $result = [
            'genel_toplam' => 0,
            'bakiye' => 0,
            'toplam_tahsilat' => 0,
            'alim_genel_toplam' =>0,
            'toplam_odeme'=> 0 ,
            'toplam_borc'=>0,
            'toplam_alacak'=> 0 ,
            'durum' => 'non',
            'durum_s' => 0
        ];
        
        
          $alimtoplamSql = "SELECT SUM(
((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) + 
((((stok_haraket_giris.alis_fiyati - stok_haraket_giris.indirim_tutari) * (stok_haraket_giris.adet)) * (stok_haraket_giris.kdv_oran)) / 100)
)   as genel_toplam FROM stok_haraket_giris WHERE cari_id = ? AND remove = 0 and owner_id = ?";

        $aquery = $this->getConnection()->prepare($alimtoplamSql);

        $aquery->execute([$cari_id, Controller::$userInfo['owner_id']]);

        $toplam_alim = $aquery->fetch();

        if ($toplam_alim) {
            $result["alim_genel_toplam"] = $toplam_alim["genel_toplam"];
        }
        
        
        /*
         * Satışlar
         */


              $toplamSql = "SELECT SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) +
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)   as genel_toplam FROM stok_haraket_cikis WHERE cari_id = ? AND remove = 0 and owner_id = ?";

       // $toplamSql = "SELECT sum(genel_toplam) as genel_toplam FROM satis_evraklari WHERE cari_id = ? AND remove = 0 and owner_id = ?";

        $query = $this->getConnection()->prepare($toplamSql);

        $query->execute([$cari_id, Controller::$userInfo['owner_id']]);

        $toplam_satis = $query->fetch();

        if ($toplam_satis) {
            $result["genel_toplam"] = $toplam_satis["genel_toplam"];
        }

        
        /*
 * ÖDemeler
 */    

        $toplamOdemeSql = "SELECT  sum(odeme_tutar) as toplam_odeme_tutari "
                . "FROM odemeler "
                . "WHERE "
                . "cari_id = ? and  remove = 0 and owner_id = ? and etkisiz = 0  ";

        $odemeQuery = $this->getConnection()->prepare($toplamOdemeSql);
        $odemeQuery->execute([$cari_id, Controller::$userInfo['owner_id']]);
        $toplam_odeme = $odemeQuery->fetch();


        if ($toplam_odeme['toplam_odeme_tutari']) {
            $result['toplam_odeme'] = $toplam_odeme['toplam_odeme_tutari'];
        }

    /*
     * Tahsilatlar
     */

        $toplamTahsilatSql = "SELECT  sum(islem_tutar) as toplam_tahsilat_tutari "
                . "FROM tahsilatlar "
                . "WHERE "
                . "cari_id = ? and  remove = 0 and owner_id = ? and etkisiz = 0  ";

        $tahsilatQuery = $this->getConnection()->prepare($toplamTahsilatSql);
        $tahsilatQuery->execute([$cari_id, Controller::$userInfo['owner_id']]);
        $toplam_tahsilat = $tahsilatQuery->fetch();


        if ($toplam_tahsilat['toplam_tahsilat_tutari']) {
            $result['toplam_tahsilat'] = $toplam_tahsilat['toplam_tahsilat_tutari'];
        }

        
        $result['toplam_borc'] = $result["alim_genel_toplam"] - $result['toplam_odeme'];

        $result['toplam_alacak'] = $result["genel_toplam"] - $result['toplam_tahsilat'];
        
        $result['bakiye'] =$result['toplam_alacak']  - $result["toplam_borc"];
        
        $result['bakiye'] = number_format($result['bakiye'], 2, '.', '');

        if($result['bakiye'] == 0){
            
             $result['durum'] = "-";

        }else if($result['bakiye'] > 0){
            
             $result['durum'] = "Alacaklısınız";
             
              $result['durum_s'] = 1;
        
        }else if($result['bakiye'] < 0){
            
             $result['durum'] = "Borçlusunuz";
        
             $result['durum_s'] = 2;
        }
        
        return $result;
    }

    public function cari_hesap_ozet_yenile() {


        $result = [
            'genel_toplam' => 0,
            'bakiye' => 0,
            'toplam_alim' => 0,
            'toplam_satis' => 0,
            'toplam_tahsilat' => 0,
            'toplam_odeme' => 0
        ];

        $toplamSql = "SELECT sum(genel_toplam) as genel_toplam FROM satis_evraklari WHERE cari_id = ? AND remove = 0 and owner_id = ?";

        $query = $this->getConnection()->prepare($toplamSql);

        $query->execute([$cari_id, Controller::$userInfo['owner_id']]);

        $toplam_satis = $query->fetch();

        if ($toplam_satis) {
            $result["genel_toplam"] = $toplam_satis["genel_toplam"];
        }


         $toplamTahsilatSql = "SELECT  sum(islem_tutar) as toplam_tahsilat_tutari "
                . "FROM tahsilatlar "
                . "WHERE "
                . "cari_id = ? and  remove = 0 and owner_id = ?  ";

        $tahsilatQuery = $this->getConnection()->prepare($toplamTahsilatSql);
        $tahsilatQuery->execute([$cari_id, Controller::$userInfo['owner_id']]);
        $toplam_tahsilat = $tahsilatQuery->fetch();


        if ($toplam_tahsilat['toplam_tahsilat_tutari']) {
            $result['toplam_tahsilat'] = $toplam_tahsilat['toplam_tahsilat_tutari'];
        }


        $result['bakiye'] = $result["genel_toplam"] - $result['toplam_tahsilat'];
    }

    public function cariSatislar($cari_id){


        $year = date("Y");

        $bas_tarih = $year."-01-01";
        $bit_tarih = $year."-12-31";



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




        $sql = "SELECT 
satis_evraklari.*, 
SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)  as tutar , 
satis_evraklari.tarih as islem_tarih , DATE_FORMAT(satis_evraklari.tarih   , '%m') as ay  
FROM satis_evraklari 
INNER JOIN stok_haraket_cikis  ON  satis_evraklari.id = stok_haraket_cikis.satis_evrak_id  
WHERE (satis_evraklari.created_date >= ? and satis_evraklari.created_date <= ?)  
and satis_evraklari.remove = 0 
and satis_evraklari.owner_id = ? 
and satis_evraklari.cari_id = ? 
and stok_haraket_cikis.owner_id = ? 
and stok_haraket_cikis.remove = 0  

GROUP BY ay ";



        $query= $this->getConnection()->prepare($sql);
        $query->execute([$bas_tarih,$bit_tarih,Controller::$userInfo["owner_id"],$cari_id,Controller::$userInfo["owner_id"]]);


        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if($result){

            foreach ($result as $key => $val){

                $ay = intval($val["ay"]);

                $takvim[$ay] = number_format($val["tutar"], 2, '.', '');

            }

        }


        return $takvim;






    }

}
