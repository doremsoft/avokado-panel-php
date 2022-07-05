<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class finansalViewModel extends Dimodel {
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
public function getSabitOdemeler()
{
  return $this->table("sabit_odemeler",Controller::$userInfo)

      ->disableDefault()
      ->select(" sabit_odemeler.* , cari.cari_adi ")
      ->innerjoin([" cari "=>" sabit_odemeler.cari_id = cari.id "])
      ->where([
          "sabit_odemeler.remove"=>["=",0],
          "sabit_odemeler.owner_id"=>["=",Controller::$userInfo["owner_id"]]


      ])
      ->getAll();
}
    public function getAylikSabitOdemeler($ay,$yil)
    {
        $baslama = $yil."-".$ay."-01";
        $bitis = $yil."-".$ay."-31";


        return $this->table("sabit_odemeler",Controller::$userInfo)

            ->disableDefault()
            ->select(" sabit_odemeler.* , cari.cari_adi ")
            ->innerjoin([" cari "=>" sabit_odemeler.cari_id = cari.id "])
            ->where([
                "sabit_odemeler.remove"=>["=",0],
                "sabit_odemeler.owner_id"=>["=",Controller::$userInfo["owner_id"]],
                "sabit_odemeler.baslama_tarihi"=>["<=",$baslama],
                "sabit_odemeler.bitis_tarihi"=>[">=",$bitis]

            ])
            ->getAll();
    }



    public function getSabitOdeme($sodeme_id)
    {
        return $this->table("sabit_odemeler",Controller::$userInfo)
            ->disableDefault()
            ->select(" sabit_odemeler.* , cari.cari_adi ")
            ->innerjoin([" cari "=>" sabit_odemeler.cari_id = cari.id "])
            ->where([
                "sabit_odemeler.id" =>["=",$sodeme_id],
                "sabit_odemeler.remove"=>["=",0],
                "sabit_odemeler.owner_id"=>["=",Controller::$userInfo["owner_id"]]

            ])
            ->get();
    }

    public function getOdemeler($tip,$baslama,$bitis){


    if($tip == "hepsi"){

        return $this->table("odemeler", Controller::$userInfo)
            ->disableDefault()
            ->select(" odemeler.odeme_tip , odemeler.odeme_tutar , odemeler.odeme_tarih , odemeler.odeme_islem_id , cari.cari_adi , cari.id as cari_id ")
            ->where([
                "odemeler.remove"=>["=",0],
                "odemeler.owner_id"=>["=",Controller::$userInfo["owner_id"]],
                "odemeler.odeme_tarih"=>["<=",$bitis ],
                " odemeler.odeme_tarih "=>[">=",$baslama]
            ])
            ->orderBy(" ORDER BY odemeler.id DESC ")
            ->leftjoin([" cari "=>" odemeler.cari_id = cari.id "])
            ->paginate(Controller::$http_request, 20);

    }else{

        return $this->table("odemeler", Controller::$userInfo)
            ->disableDefault()
            ->select(" odemeler.odeme_tip , odemeler.odeme_tutar , odemeler.odeme_tarih , odemeler.odeme_islem_id , cari.cari_adi , cari.id as cari_id ")
            ->where([
                "odemeler.remove"=>["=",0],
                "odemeler.owner_id"=>["=",Controller::$userInfo["owner_id"]],
                "odemeler.odeme_tarih"=>["<=",$bitis ],
                " odemeler.odeme_tarih "=>[">=",$baslama],
                "odemeler.odeme_tip"=>["=",$tip]

            ])
            ->orderBy(" ORDER BY odemeler.id DESC ")
            ->leftjoin([" cari "=>" odemeler.cari_id = cari.id "])
            ->paginate(Controller::$http_request, 20);
    }



    }

    public function getSonOdemeler(){


        return $this->table("odemeler", Controller::$userInfo)
            ->disableDefault()
            ->select(" odemeler.odeme_tip , odemeler.odeme_tutar , odemeler.odeme_tarih , odemeler.odeme_islem_id , cari.cari_adi , cari.id as cari_id ")
            ->where([
                "odemeler.remove"=>["=",0],
                "odemeler.owner_id"=>["=",Controller::$userInfo["owner_id"]]

            ])
            ->leftjoin([" cari "=>" odemeler.cari_id = cari.id "])
            ->orderBy(" ORDER BY odemeler.id DESC ")
            ->limit(" LIMIT 10 ")
            ->getAll();

    }

    public function getTahsilatlar($tip,$baslama,$bitis){


        if($tip == "hepsi"){

            return $this->table("tahsilatlar", Controller::$userInfo)
                ->disableDefault()
                ->select("
                  tahsilatlar.islem_tip , 
                tahsilatlar.islem_tutar , 
                tahsilatlar.islem_tarih , 
                tahsilatlar.islem_id , 
                cari.cari_adi , cari.id as cari_id ")
                ->where([
                    "tahsilatlar.remove"=>["=",0],
                    "tahsilatlar.owner_id"=>["=",Controller::$userInfo["owner_id"]],
                    "tahsilatlar.islem_tarih"=>["<=",$bitis ],
                    " tahsilatlar.islem_tarih "=>[">=",$baslama]
                ])
                ->orderBy(" ORDER BY tahsilatlar.id DESC ")
                ->leftjoin([" cari "=>" tahsilatlar.cari_id = cari.id "])
                ->paginate(Controller::$http_request, 20);

        }else{

            return $this->table("tahsilatlar", Controller::$userInfo)
                ->disableDefault()
                ->select(" 
                tahsilatlar.islem_tip , 
                tahsilatlar.islem_tutar , 
                tahsilatlar.islem_tarih , 
                tahsilatlar.islem_id , 
                cari.cari_adi , cari.id as cari_id ")
                ->where([
                    "tahsilatlar.remove"=>["=",0],
                    "tahsilatlar.owner_id"=>["=",Controller::$userInfo["owner_id"]],
                    "tahsilatlar.islem_tarih"=>["<=",$bitis ],
                    " tahsilatlar.islem_tarih "=>[">=",$baslama],
                    "tahsilatlar.islem_tip"=>["=",$tip]

                ])
                ->orderBy(" ORDER BY tahsilatlar.id DESC ")
                ->leftjoin([" cari "=>" tahsilatlar.cari_id = cari.id "])
                ->paginate(Controller::$http_request, 20);
        }



    }


    public function getSonTahsilatlar(){


        return $this->table("tahsilatlar", Controller::$userInfo)
            ->disableDefault()
            ->select("    tahsilatlar.islem_tip , 
                tahsilatlar.islem_tutar , 
                tahsilatlar.islem_tarih , 
                tahsilatlar.islem_id , 
                cari.cari_adi , cari.id as cari_id  ")
            ->where([
                "tahsilatlar.remove"=>["=",0],
                "tahsilatlar.owner_id"=>["=",Controller::$userInfo["owner_id"]]

            ])
            ->leftjoin([" cari "=>" tahsilatlar.cari_id = cari.id "])
            ->orderBy(" ORDER BY tahsilatlar.id DESC ")
            ->limit(" LIMIT 10 ")
            ->getAll();

    }

    public function yillik_gelirler(){
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
  SUM(islem_tutar) as tutar , DATE_FORMAT(islem_tarih , '%m') as tarih    
 
FROM tahsilatlar WHERE  owner_id = ?  and remove = 0 and  islem_tarih >= ? and islem_tarih <= ?  GROUP BY tarih


";


        $query= $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);


        if($result){
            foreach ($result as $key => $val){


                $ay = intval($val["tarih"]);

                $takvim[$ay] = number_format($val["tutar"], 2, '.', '');

            }

        }

        return $takvim;

    }

    public function yillik_giderler(){
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
 SUM(odeme_tutar) as tutar, DATE_FORMAT(odeme_tarih , '%m') as tarih    
 
FROM odemeler WHERE  owner_id = ?  and remove = 0 and  odeme_tarih >= ? and odeme_tarih <= ?  GROUP BY tarih


";


        $query= $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"],$bas_tarih,$bit_tarih]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);


        if($result){
            foreach ($result as $key => $val){


                $ay = intval($val["tarih"]);

                $takvim[$ay] = number_format($val["tutar"], 2, '.', '');

            }

        }

        return $takvim;

    }

    public function getAlimlar(){
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
