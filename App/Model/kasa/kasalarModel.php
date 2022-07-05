<?php

use Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class kasalarModel extends Dimodel {
    /*
      function __construct()
      {
      // parent::__construct("pdo");
      // pdo - mysqli - pdox
      }
     */

    public function kasaListesi() {
        $tablo = $this->table("kasalar", Controller::$userInfo);

        return $this->where()->getAll();
    }

    public function kasaEkle($request) {

        if ($request->input("kasa-adi") == NULL) {

            return false;
        } else {
            return $this->table("kasalar", Controller::$userInfo)->col("kasa_adi", $request->input("kasa-adi"))->save_();
        }
    }

    public function kasaGuncelle($request) {


        return $this->table("kasalar", Controller::$userInfo)->find($request->input("id"))
                        ->col("kasa_adi", $request->input("value"))
                        ->update_();
    }

    public function kasaSil($request) {

        return $this->table("kasalar", Controller::$userInfo)->find($request->input("id"))->remove_();
    }

    public function kasaHaraketKaydet($request) {

        $not = "";
        $evrakli_islem_id = 0;


        $cikis_tarih = date("Y-m-d H:i:s");

        if ($request->input("kasa_haraket_tarih") == null) {

            $tarih = $cikis_tarih;
        } else {

            $tarih = $request->input("kasa_haraket_tarih");
        }



        $not = $request->input("kasa_haraket_not");

        if($request->input("hareket_kanal")  == "evrakislem"){

            $evrak_tur = $request->input("evrak_tur");

            $evrak_id = $request->input("evrak_id");


            $haraket_turu = "";

            if($request->input("kasa_haraket_tip") == 1){

                $haraket_turu = " tahsil ";


                $resultt = $this->reset()->table("tahsilatlar", Controller::$userInfo)->where([
                    "islem_tip" => ["=","senet"],
                    "islem_id"=>["=",$evrak_id]
                ])->get();

                $evrakli_islem_id =  $resultt["id"];



            }else  if($request->input("kasa_haraket_tip") == 2){

                $haraket_turu = " odeme ";

                $resultt = $this->reset()->table("odemeler", Controller::$userInfo)->where([
                    "odeme_tip" => ["=","senet"],
                    "odeme_islem_id"=>["=",$evrak_id]
                ])->get();

                $evrakli_islem_id =  $resultt["id"];

            }


            $this->table("kiymetli_evraklar", Controller::$userInfo)
                ->find($evrak_id)
                ->col("odeme_durum",1)
                ->update_();



            $not = $not." ".$evrak_id." ".$evrak_tur." ".$haraket_turu." Kaydı";

        }

        $result = $this->table("kasa_haraket", Controller::$userInfo)
                ->col("kasa_id", $request->input("kasa_id"))
                ->col("kasa_haraket_tip", $request->input("kasa_haraket_tip"))
                ->col("kasa_haraket_cari_id", $request->input("kasa_haraket_cari_id"))
                ->col("kasa_haraket_tutar", $request->input("kasa_haraket_tutar"))
                ->col("kasa_haraket_tarih", $tarih)
                ->col("kasa_haraket_not", $not)
                ->save_();

        if ($result) {

            $islem_id = $this->getConnection()->lastInsertId();

            $toplam_nakit_tutar = $request->input("kasa_haraket_tutar");


            if ($request->input("kasa_haraket_tip") == 1) {

                $result = $this->reset()->table("tahsilatlar", Controller::$userInfo)
                        ->col("cari_id", $request->input("kasa_haraket_cari_id"))
                        ->col("islem_tip", "kasanakit")
                        ->col("islem_id", $islem_id)
                        ->col("islem_tarih", $tarih)
                        ->col("islem_tutar", $toplam_nakit_tutar)
                         ->col("islem_mesaj", $not)
                        ->save_();


                if($request->input("hareket_kanal")  == "evrakislem") {

                    $result = $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($evrakli_islem_id)
                        ->col("etkisiz", 1)
                        ->update_();
                }


                } else {


                $result = $this->reset()->table("odemeler", Controller::$userInfo)
                        ->col("cari_id", $request->input("kasa_haraket_cari_id"))
                        ->col("odeme_tip", "kasanakit")
                        ->col("odeme_islem_id", $islem_id)
                        ->col("odeme_tutar", $toplam_nakit_tutar)
                        ->col("odeme_tarih", $tarih)
                        ->col("odeme_mesaj", $not)
                        ->save_();


                if($request->input("hareket_kanal")  == "evrakislem") {

                    $result = $this->reset()->table("odemeler", Controller::$userInfo)->find($evrakli_islem_id)
                        ->col("etkisiz", 1)
                        ->update_();

                }




            }











        }

        return $result;
    }

    public function kasaDokumuGoster($kasa_id, $tip, $bas_tarih, $bit_tarih, $paginate_count) {


        if ($tip == 0) {
            $result = $this->table("kasa_haraket", Controller::$userInfo)
                    ->disableDefault()
                    ->select(" kasa_haraket.id as haraket_id ,  kasa_haraket.kasa_haraket_tutar , kasa_haraket.kasa_haraket_not, kasa_haraket.kasa_haraket_tarih, kasa_haraket.kasa_haraket_tip , cari.cari_adi ,kasalar.kasa_adi ")
                    ->innerjoin(["kasalar" => " kasa_haraket.kasa_id = kasalar.id "])
                    ->leftjoin(["cari" => " kasa_haraket.kasa_haraket_cari_id = cari.id "])
                    ->where([
                        'kasa_haraket.kasa_id' => ['=', $kasa_id],
                        'kasa_haraket.remove' => ['=', '0'],
                        'kasa_haraket.owner_id' => ['=', Controller::$userInfo['owner_id']],
                        'kasa_haraket.kasa_haraket_tarih' => ['>=', $bas_tarih],
                        'kasa_haraket.kasa_haraket_tarih ' => ['<=', $bit_tarih]
                    ])
                    ->paginate(Controller::$http_request, $paginate_count);
        } else {

            $result = $this->table("kasa_haraket", Controller::$userInfo)
                    ->disableDefault()
                    ->select(" kasa_haraket.id as haraket_id , kasa_haraket.kasa_haraket_tutar , kasa_haraket.kasa_haraket_not,kasa_haraket.kasa_haraket_tarih, kasa_haraket.kasa_haraket_tip, cari.cari_adi ,kasalar.kasa_adi ")
                    ->innerjoin(["kasalar" => " kasa_haraket.kasa_id = kasalar.id "])
                    ->leftjoin(["cari" => " kasa_haraket.kasa_haraket_cari_id = cari.id "])
                    ->where([
                        'kasa_haraket.kasa_id' => ['=', $kasa_id],
                        'kasa_haraket.remove' => ['=', '0'],
                        'kasa_haraket.owner_id' => ['=', Controller::$userInfo['owner_id']],
                        'kasa_haraket.kasa_haraket_tarih' => ['>=', $bas_tarih],
                        'kasa_haraket.kasa_haraket_tarih ' => ['<=', $bit_tarih],
                        'kasa_haraket.kasa_haraket_tip' => ['=', $tip]
                    ])
                    ->paginate(Controller::$http_request, $paginate_count);
        }



        return $result;
    }

    public function kasaDurumu($kasa_id){


        $sql = "
SELECT  kasalar.kasa_adi , SUM(CASE 
    WHEN kasa_haraket_tip = 1  
    THEN kasa_haraket_tutar 
    ELSE 0 
END) AS gelirler , SUM(CASE 
    WHEN kasa_haraket_tip = 2  
    THEN kasa_haraket_tutar 
    ELSE 0 
END) AS giderler 
FROM kasa_haraket 
LEFT JOIN kasalar ON kasa_haraket.kasa_id = kasalar.id  
WHERE kasa_haraket.remove = 0 and kasa_haraket.owner_id = ? and kasa_haraket.kasa_id = ?  ";



        $query = $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"],$kasa_id]);

        return $query->fetch(PDO::FETCH_ASSOC);

    }



    public function sonHaraketler(){
      return  $this->table("kasa_haraket", Controller::$userInfo)
            ->disableDefault()
            ->select(" kasa_haraket.iptal_mesaji , kasa_haraket.id as haraket_id ,  kasa_haraket.kasa_haraket_tutar , kasa_haraket.kasa_haraket_not, kasa_haraket.kasa_haraket_tarih, kasa_haraket.kasa_haraket_tip , cari.cari_adi ,kasalar.kasa_adi ")
            ->innerjoin(["kasalar" => " kasa_haraket.kasa_id = kasalar.id "])
            ->leftjoin(["cari" => " kasa_haraket.kasa_haraket_cari_id = cari.id "])
            ->where([
                'kasa_haraket.remove' => ['=', '0'],
                'kasa_haraket.owner_id' => ['=', Controller::$userInfo['owner_id']]
            ])
          ->orderBy(" ORDER BY kasa_haraket.id DESC LIMIT 10 ")
            ->getAll();
    }

    public function kasaIptalDokumuGoster($kasa_id, $tip, $bas_tarih, $bit_tarih, $paginate_count) {


        if ($tip == 0) {
            $result = $this->table("kasa_haraket", Controller::$userInfo)
                ->disableDefault()
                ->select(" kasa_haraket.iptal_mesaji , kasa_haraket.id as haraket_id ,  kasa_haraket.kasa_haraket_tutar , kasa_haraket.kasa_haraket_not, kasa_haraket.kasa_haraket_tarih, kasa_haraket.kasa_haraket_tip , cari.cari_adi ,kasalar.kasa_adi ")
                ->innerjoin(["kasalar" => " kasa_haraket.kasa_id = kasalar.id "])
                ->leftjoin(["cari" => " kasa_haraket.kasa_haraket_cari_id = cari.id "])
                ->where([
                    'kasa_haraket.kasa_id' => ['=', $kasa_id],
                    'kasa_haraket.remove' => ['=', '1'],
                    'kasa_haraket.owner_id' => ['=', Controller::$userInfo['owner_id']],
                    'kasa_haraket.kasa_haraket_tarih' => ['>=', $bas_tarih],
                    'kasa_haraket.kasa_haraket_tarih ' => ['<=', $bit_tarih]
                ])
                ->paginate(Controller::$http_request, $paginate_count);
        } else {

            $result = $this->table("kasa_haraket", Controller::$userInfo)
                ->disableDefault()
                ->select(" kasa_haraket.iptal_mesaji , kasa_haraket.id as haraket_id , kasa_haraket.kasa_haraket_tutar , kasa_haraket.kasa_haraket_not,kasa_haraket.kasa_haraket_tarih, kasa_haraket.kasa_haraket_tip, cari.cari_adi ,kasalar.kasa_adi ")
                ->innerjoin(["kasalar" => " kasa_haraket.kasa_id = kasalar.id "])
                ->leftjoin(["cari" => " kasa_haraket.kasa_haraket_cari_id = cari.id "])
                ->where([
                    'kasa_haraket.kasa_id' => ['=', $kasa_id],
                    'kasa_haraket.remove' => ['=', '1'],
                    'kasa_haraket.owner_id' => ['=', Controller::$userInfo['owner_id']],
                    'kasa_haraket.kasa_haraket_tarih' => ['>=', $bas_tarih],
                    'kasa_haraket.kasa_haraket_tarih ' => ['<=', $bit_tarih],
                    'kasa_haraket.kasa_haraket_tip' => ['=', $tip]
                ])
                ->paginate(Controller::$http_request, $paginate_count);
        }





        return $result;
    }


    public function kasaDurum($kasa_id) {

        return $this->table("kasalar", Controller::$userInfo)->find($kasa_id, true);
    }

    public function kasaHaraketIptal($request){

        $haraket = $this->table("kasa_haraket", Controller::$userInfo)->find($request->input("id"))->get();


        $tarih = date("d-m-Y H:i:s");

        $sebep = $request->input("iptalsebep");
        $user = $sebep." <br> (".Controller::$userInfo["name"]." ".Controller::$userInfo["surname"].")";


        if($haraket){

            if($haraket["kasa_haraket_tip"] == 1){
                //Giriş

                $tahsilat = $this->table("tahsilatlar", Controller::$userInfo)
                    ->where([
                        "islem_tip"=>["=","kasanakit"],
                        "islem_id" => ["=",$request->input("id")]
                    ])
                    ->get();



                if($tahsilat){

                    $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($tahsilat["id"])
                        ->col("iptal_mesaji",$user." tarafından {$tarih} tarihinde iptal edildi")->update_();

                    $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($tahsilat["id"])->remove_();



                    $this->reset()->table("kasa_haraket", Controller::$userInfo)
                        ->find($request->input("id"))
                        ->col("iptal_mesaji",$user." tarafından {$tarih} tarihinde iptal edildi")
                        ->update_();

                    $this->reset()->table("kasa_haraket", Controller::$userInfo)
                        ->find($request->input("id"))
                        ->remove_();

                    return true;

                }else{

                    return false;
                }





            }else if($haraket["kasa_haraket_tip"] == 2){

                //Çıkış

                $odeme = $this->table("odemeler", Controller::$userInfo)
                    ->where([
                        "odeme_tip"=>["=","kasanakit"],
                        "odeme_islem_id" => ["=",$request->input("id")]
                    ])
                    ->get();

                $tarih = date("d-m-Y H:i:s");
                $user = Controller::$userInfo["name"]." ".Controller::$userInfo["surname"];

                if($odeme){

                    $this->reset()->table("odemeler", Controller::$userInfo)->find($odeme["id"])
                        ->col("iptal_mesaji",$user." tarafından {$tarih} tarihinde iptal edildi")->update_();

                    $this->reset()->table("odemeler", Controller::$userInfo)->find($odeme["id"])->remove_();

                    $this->reset()->table("kasa_haraket", Controller::$userInfo)
                        ->find($request->input("id"))
                        ->col("iptal_mesaji",$user." tarafından {$tarih} tarihinde iptal edildi")
                        ->update_();

                    $this->reset()->table("kasa_haraket", Controller::$userInfo)
                        ->find($request->input("id"))
                        ->remove_();

                    return true;

                }else{

                    return false;
                }


            }

        }else{
            return false;
        }


    }

    public function kasalarArasiVirman($request) {

        //Kaynak Kasadan Para Çıkışını Yapıyorz


        $cikis_tarih = date("Y-m-d H:i:s");

        if ($request->input("tarih") == null) {

            $tarih = $cikis_tarih;
        } else {

            $tarih = $request->input("tarih");
        }




        $cikis_result = $this->table("kasa_haraket", Controller::$userInfo)
                ->col("kasa_id", $request->input("kaynak_kasa_id"))
                ->col("kasa_haraket_tip", 2)
                ->col("kasa_haraket_cari_id", 0)
                ->col("kasa_haraket_tutar", $request->input("tutar"))
                ->col("kasa_haraket_tarih", $tarih)
                ->col("kasa_haraket_not", "Virman için çıkış yapıldı")
                ->save_();



        if ($cikis_result) {

            $kaynak_kasa = $request->input("kaynak_kasa_id");

            $toplam_nakit_tutar = $request->input("tutar");


            $cikissql = "UPDATE kasalar SET kasa_toplam_tutar = kasa_toplam_tutar-? , update_date = ? WHERE id = ?";
            $payquery = $this->getConnection()->prepare($cikissql);
            $payquery->execute([$toplam_nakit_tutar, $cikis_tarih, $kaynak_kasa]);


            /*
             * Hedef Kasaya Para Girişi yapıyoruz
             */

            $hedef_kasa = $request->input("hedef_kasa_id");

            $giris_result = $this->table("kasa_haraket", Controller::$userInfo)
                    ->col("kasa_id", $request->input("hedef_kasa_id"))
                    ->col("kasa_haraket_tip", 1)
                    ->col("kasa_haraket_cari_id", 0)
                    ->col("kasa_haraket_tutar", $request->input("tutar"))
                    ->col("kasa_haraket_tarih", $tarih)
                    ->col("kasa_haraket_not", "Virmandan giriş yapıldı")
                    ->save_();

            if ($giris_result) {

                $girissql = "UPDATE kasalar SET kasa_toplam_tutar = kasa_toplam_tutar+? , update_date = ? WHERE id = ?";
                $girisquery = $this->getConnection()->prepare($girissql);
                $girisquery->execute([$toplam_nakit_tutar, $cikis_tarih, $hedef_kasa]);

                return true;
            } else {

                return false;
            }
        } else {
            return false;
        }
    }

}
