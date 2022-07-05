<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class siparisViewModel extends Dimodel
{
    /*
     * Controller::$userInfo
     */
    /*
   function __construct()
   {
       // parent::__construct("pdo");
       // pdo - mysqli - pdox
   }
   */
    public function getSiparisList()
    {


        return $this->table("satis_evraklari", Controller::$userInfo)
            ->select("satis_evraklari.* , cari.cari_adi , cari.id as cari_id , (
SELECT  
(

SUM(
((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) + 
((((stok_haraket_cikis.satis_fiyati - stok_haraket_cikis.indirim_tutari) * (stok_haraket_cikis.adet)) * (stok_haraket_cikis.kdv_oran)) / 100)
)   

) as genel_toplam FROM  stok_haraket_cikis WHERE  satis_evrak_id  = satis_evraklari.id and remove = 0 and owner_id = satis_evraklari.owner_id 
 )  as genel_toplam  ")
            ->disableDefault()
            ->where(
                [
                    "satis_evraklari.evrak_tur " => ["=", "1"],
                    "satis_evraklari.remove " => ["=", "0"],
                    "satis_evraklari.siparis_durumu" => ["!=", "7"],
                    " satis_evraklari.siparis_durumu " => ["!=", "6"],
                    "satis_evraklari.owner_id" => ["=", Controller::$userInfo["owner_id"]],
                    "satis_evraklari.siparis_kod" => ["!=", "0"]]

            )
            ->leftjoin(["cari" => "satis_evraklari.cari_id = cari.id"])
            ->paginate(Controller::$http_request, 10);


    }

    public function durum_degistir($id, $durum)
    {

        $query = $this->getConnection()->prepare("UPDATE satis_evraklari SET siparis_durumu = ? WHERE id = ?  and owner_id = ?  ");

        return $query->execute([$durum, $id, Controller::$userInfo["owner_id"]]);
    }

    public function kargoNoguncelle($id, $kargo_no)
    {


        $query = $this->getConnection()->prepare("UPDATE siparis SET kargo_no = ? WHERE id = ?   ");

        return $query->execute([$kargo_no, $id]);

    }


    public function getWebsiparis($id)
    {

        $evrak = $this->getConnection()->prepare("SELECT 


cari.cari_adi , 
cari.cari_telefon , 
cari.cari_mail ,
cari_adresler.adres as sevk_adres , 
cari_adresler.email as email_adres , 
cari_adresler.ceptelefonu as ceptelefonu , 
cari_adresler.telefon as telefonno , 
cari_adresler.ad as musteri_ad , 
cari_adresler.soyad as musteri_soyad , 
cities.cityname as sevk_il ,
counties.countyname as sevk_ilce ,
siparis.kargo_no , 
siparis.id as siparis_web_id  , 
siparis.siparisno 

FROM siparis 
LEFT JOIN cari ON siparis.cari_id = cari.id 
LEFT JOIN cari_adresler ON siparis.fatura_adresi = cari_adresler.id 
LEFT JOIN cities ON cities.cityid = cari_adresler.sehir 
LEFT JOIN counties ON counties.countyid = cari_adresler.ilce   
WHERE  siparis.id = ?");

        $evrak->execute([$id]);

        return $evrak->fetch(PDO::FETCH_ASSOC);


    }

    public function getsiparis($id)
    {

        $evrak = $this->getConnection()->prepare("SELECT 
satis_evraklari.* , 
cari.cari_adi , 
cari.cari_telefon , 
cari.cari_mail ,
cari_adresler.adres as sevk_adres , 
cari_adresler.email as email_adres , 
cari_adresler.ceptelefonu as ceptelefonu , 
cari_adresler.telefon as telefonno , 
cari_adresler.ad as musteri_ad , 
cari_adresler.soyad as musteri_soyad , 
cities.cityname as sevk_il ,
counties.countyname as sevk_ilce ,
siparis.kargo_no , 
siparis.id as siparis_web_id  

FROM satis_evraklari 
LEFT JOIN cari ON satis_evraklari.cari_id = cari.id 
LEFT JOIN cari_adresler ON satis_evraklari.teslimat_adresi = cari_adresler.id 
LEFT JOIN cities ON cities.cityid = cari_adresler.sehir 
LEFT JOIN counties ON counties.countyid = cari_adresler.ilce  
LEFT JOIN siparis ON satis_evraklari.siparis_kod = siparis.siparisno  
WHERE  satis_evraklari.owner_id = ? and satis_evraklari.evrak_tur = 1 and satis_evraklari.id = ?");

        $evrak->execute([Controller::$userInfo["owner_id"], $id]);

        return $evrak->fetch(PDO::FETCH_ASSOC);

    }



    public function getIptalSiparisList()
    {


        return $this->table("satis_evraklari", Controller::$userInfo)
            ->select("satis_evraklari.* , cari.cari_adi , cari.id as cari_id  ")
            ->disableDefault()
            ->where(
                [
                    "satis_evraklari.evrak_tur " => ["=", "1"],
                    " satis_evraklari.remove " => ["=", 1],
                    "satis_evraklari.owner_id" => ["=", Controller::$userInfo["owner_id"]],
                    "satis_evraklari.siparis_kod" => ["!=", "0"]
                ]

            )
            ->leftjoin(["cari" => "satis_evraklari.cari_id = cari.id"])
            ->paginate(Controller::$http_request, 10);

    }

    public function getKargodakiSiparisList()
    {


        return $this->table("satis_evraklari", Controller::$userInfo)
            ->select("satis_evraklari.* , cari.cari_adi , cari.id as cari_id  ")
            ->disableDefault()
            ->where(
                [
                    "satis_evraklari.evrak_tur " => ["=", "1"],
                    "satis_evraklari.siparis_durumu" => ["=", "6"],
                    " satis_evraklari.remove " => ["=", 0],
                    "satis_evraklari.owner_id" => ["=", Controller::$userInfo["owner_id"]],
                    "satis_evraklari.siparis_kod" => ["!=", "0"]
                ]

            )
            ->leftjoin(["cari" => "satis_evraklari.cari_id = cari.id"])
            ->paginate(Controller::$http_request, 10);

    }



    public function getGecmisSiparisList()
    {


        return $this->table("satis_evraklari", Controller::$userInfo)
            ->select("satis_evraklari.* , cari.cari_adi , cari.id as cari_id  ")
            ->disableDefault()
            ->where(
                [
                    "satis_evraklari.evrak_tur " => ["=", "1"],
                    "satis_evraklari.siparis_durumu" => ["=", "7"],
                    " satis_evraklari.remove " => ["=", 0],
                    "satis_evraklari.owner_id" => ["=", Controller::$userInfo["owner_id"]],
                    "satis_evraklari.siparis_kod" => ["!=", "0"]
                ]

            )
            ->leftjoin(["cari" => "satis_evraklari.cari_id = cari.id"])
            ->paginate(Controller::$http_request, 10);

    }


}
