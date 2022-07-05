<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class bankaModel extends Dimodel {

    public function bankaListesi() {

        return $this->table("bankalar", Controller::$userInfo)->getAll();
    }

    public function bankaEkle($request) {

        if ($request->input("banka-adi") == NULL) {

            return false;
        } else {
            return $this->table("bankalar", Controller::$userInfo)->col("banka_adi", $request->input("banka-adi"))->save_();
        }
    }

    public function bankaGuncelle($request) {


        return $this->table("bankalar", Controller::$userInfo)->find($request->input("id"))
                        ->col("banka_adi", $request->input("value"))
                        ->update_();
    }

    public function bankaSil($request) {

        return $this->table("bankalar", Controller::$userInfo)->find($request->input("id"))->remove_();
    }

    public function bankaDetaylari($banka_id) {

        return $this->reset()->table("bankalar", Controller::$userInfo)->find($banka_id)->get();
    }

    public function hesapGetir($hesap_id) {

        return $this->reset()->table("banka_hesaplari", Controller::$userInfo)->find($hesap_id)->get();
    }

    public function bankaHesapEkle($request) {

        if ($request->input("banka_id") == NULL) {

            return false;
        } else {
            return $this->table("banka_hesaplari", Controller::$userInfo)
                            ->col("banka_id", $request->input("banka_id"))
                            ->col("hesap_adi", $request->input("hesap_adi"))
                            ->col("hesap_bakiyesi", "0.000")
                            ->col("hesap_iban", $request->input("hesap_iban"))
                            ->col("hesap_sube_no", $request->input("hesap_sube_no"))
                            ->col("hesap_no", $request->input("hesap_no"))
                            ->save_();
        }
    }

    public function hesapListesi($banka_id) {
        return $this->reset()->table("banka_hesaplari", Controller::$userInfo)->where(['banka_id' => ['=', $banka_id]])->getAll();
    }

    public function butunhesapListesi() {
        return $this->table("banka_hesaplari", Controller::$userInfo)->getAll();
    }

    public function bankaHesapSil($request) {

        return $this->table("banka_hesaplari", Controller::$userInfo)->find($request->input("id"))->remove_();
    }

    public function hesapGuncelle($request) {

        if ($request->input("banka_id") == NULL) {

            return false;
        } else {
            return $this->table("banka_hesaplari", Controller::$userInfo)
                            ->find($request->input("hesap_id"))
                            ->col("hesap_adi", $request->input("hesap_adi"))
                            ->col("hesap_iban", $request->input("hesap_iban"))
                            ->col("hesap_sube_no", $request->input("hesap_sube_no"))
                            ->col("hesap_no", $request->input("hesap_no"))
                            ->update_();
        }
    }

    public function bankaHesapHareketEkle($request) {


        if ($request->input("banka_id") == NULL || $request->input("tutar") == NULL) {

            return false;
        } else {



            $result = $this->table("banka_hareket", Controller::$userInfo)
                    ->col("banka_id", $request->input("banka_id"))
                    ->col("banka_haraket_tip", $request->input("tip"))
                    ->col("banka_haraket_cari_id", 0)
                    ->col("banka_haraket_tutar", $request->input("tutar"))
                    ->col("banka_haraket_tarih", $request->input("tarih"))
                    ->col("banka_haraket_baslik", $request->input("baslik"))
                    ->col("banka_hesap_id", $request->input("hesap_id"))
                    ->save_();


            if ($result) {

                if ($request->input("tip") == 1) {

                    $this->reset()->table("banka_hesaplari", Controller::$userInfo)
                            ->find($request->input("hesap_id"))
                            ->increase_("hesap_bakiyesi", $request->input("tutar"));
                } else if ($request->input("tip") == 2) {


                    $this->reset()->table("banka_hesaplari", Controller::$userInfo)
                            ->find($request->input("hesap_id"))
                            ->decrease_("hesap_bakiyesi", $request->input("tutar"));
                }

                return true;
            } else {
                return false;
            }
        }
    }

    public function hesapOzetleri() {

        $sql = "SELECT banka_hesaplari.* , bankalar.banka_adi as banka_adi  FROM banka_hesaplari INNER JOIN bankalar ON banka_hesaplari.banka_id = bankalar.id WHERE banka_hesaplari.remove = 0 and banka_hesaplari.owner_id = ?";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo['owner_id']]);

        return $query->fetchAll();
    }

    public function hesapSonHareketleri(){

        $sql = "SELECT banka_hareket.* , banka_hesaplari.hesap_adi as hesap_adi , bankalar.banka_adi as banka_adi  FROM banka_hareket INNER JOIN banka_hesaplari ON banka_hareket.banka_hesap_id = banka_hesaplari.id  INNER JOIN bankalar ON banka_hesaplari.banka_id = bankalar.id WHERE banka_hareket.remove = 0 and banka_hareket.owner_id = ? ORDER BY banka_hareket.id DESC LIMIT 10  ";
        $query = $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo['owner_id']]);
        return $query->fetchAll();

    }

    public function hesapHareketleri($request) {



        if ($request->input("stur") == 0) {

            $sql = "SELECT banka_hareket.* , banka_hesaplari.hesap_adi as hesap_adi , bankalar.banka_adi as banka_adi  FROM banka_hareket INNER JOIN banka_hesaplari ON banka_hareket.banka_hesap_id = banka_hesaplari.id  INNER JOIN bankalar ON banka_hesaplari.banka_id = bankalar.id WHERE banka_hareket.remove = 0 and banka_hareket.owner_id = ? and banka_hareket.banka_hesap_id = ? ";
            $query = $this->getConnection()->prepare($sql);
            $query->execute([Controller::$userInfo['owner_id'], $request->input("shesap")]);
        } else {

            $sql = "SELECT banka_hareket.* , banka_hesaplari.hesap_adi as hesap_adi , bankalar.banka_adi as banka_adi  FROM banka_hareket INNER JOIN banka_hesaplari ON banka_hareket.banka_hesap_id = banka_hesaplari.id  INNER JOIN bankalar ON banka_hesaplari.banka_id = bankalar.id WHERE banka_hareket.remove = 0 and banka_hareket.owner_id = ? and banka_hareket.banka_hesap_id = ? and banka_hareket.banka_haraket_tip = ? ";
            $query = $this->getConnection()->prepare($sql);
            $query->execute([Controller::$userInfo['owner_id'], $request->input("shesap"), $request->input("stur")]);
        }

        return $query->fetchAll();
    }

    public function bankaHesapHareketEkleAjax($request) {


        if ($request->input("hesap_id") == NULL || $request->input("banka_haraket_tutar") == NULL) {

            return false;
        } else {


            $not = "";

            $evrakli_islem_id = 0;

            $not = $request->input("banka_hareket_baslik");



            $cikis_tarih = date("Y-m-d H:i:s");

            if ($request->input("kasa_haraket_tarih") == null) {

                $tarih = $cikis_tarih;
            } else {

                $tarih = $request->input("kasa_haraket_tarih");
            }




            if($request->input("hareket_kanal")  == "evrakislem"){

                $evrak_tur = $request->input("evrak_tur");

                $evrak_id = $request->input("evrak_id");


                $haraket_turu = "";

                if($request->input("tip") == 1){

                    $haraket_turu = " tahsil ";


                    $resultt = $this->reset()->table("tahsilatlar", Controller::$userInfo)->where([
                        "islem_tip" => ["=","senet"],
                        "islem_id"=>["=",$evrak_id]
                    ])->get();

                    $evrakli_islem_id =  $resultt["id"];



                }else  if($request->input("tip") == 2){

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





            $bankahesap = $this->table("banka_hesaplari", Controller::$userInfo)->find($request->input("hesap_id"))->get();


            $result = $this->reset()->table("banka_hareket", Controller::$userInfo)
                    ->col("banka_id", $bankahesap["banka_id"])
                    ->col("banka_haraket_tip", $request->input("tip"))
                    ->col("banka_haraket_cari_id", $request->input("banka_haraket_cari_id"))
                    ->col("banka_haraket_tutar", $request->input("banka_haraket_tutar"))
                    ->col("banka_haraket_tarih", $request->input("tarih"))
                    ->col("banka_haraket_baslik", $not)
                    ->col("banka_hesap_id", $request->input("hesap_id"))
                    ->save_();




            if ($result) {

                $islem_id = $this->getConnection()->lastInsertId();
                
                
                if($request->input("tip") == 1){
                    
                    
                           $result = $this->reset()->table("tahsilatlar", Controller::$userInfo)
                        ->col("cari_id", $request->input("banka_haraket_cari_id"))
                        ->col("islem_tip", "banka")
                        ->col("islem_id", $islem_id)
                        ->col("islem_tutar", $request->input("banka_haraket_tutar"))
                         ->col("islem_tarih", $request->input("tarih"))
                       ->col("islem_mesaj", $not)
                        ->save_();


                    if($request->input("hareket_kanal")  == "evrakislem") {

                         $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($evrakli_islem_id)
                            ->col("etkisiz", 1)
                            ->update_();
                    }



                }else if($request->input("tip") == 2){
                    
                               
                $result = $this->reset()->table("odemeler", Controller::$userInfo)
                        ->col("cari_id", $request->input("banka_haraket_cari_id"))
                        ->col("odeme_tip", "banka")
                        ->col("odeme_islem_id", $islem_id)
                         ->col("odeme_tarih", $request->input("tarih"))
                        ->col("odeme_tutar", $request->input("banka_haraket_tutar"))
                        ->col("odeme_mesaj", $not)
                        ->save_();


                    if($request->input("hareket_kanal")  == "evrakislem") {

                        $result = $this->reset()->table("odemeler", Controller::$userInfo)->find($evrakli_islem_id)
                            ->col("etkisiz", 1)
                            ->update_();

                    }

  
                    
                }

         
                
                
                
   

                return true;
            } else {
                return false;
            }
        }
    }

}
