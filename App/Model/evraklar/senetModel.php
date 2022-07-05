<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class senetModel extends Dimodel {
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

    public function senetEkle($request) {

        $result = $this->table("kiymetli_evraklar", Controller::$userInfo)
                ->col("evrak_tip", $request->input("senet_tur"))
                ->col("evrak_no", $request->input("evrak_no"))
                ->col("evrak_tur", "senet")
                ->col("evrak_son_odeme_tarihi", $request->input("odemetarih"))
                ->col("evrak_olusturma_tarihi", $request->input("duzenlemetarih"))
                ->col("evrak_bedeli", $request->input("tutar"))
                ->col("evrak_not", $request->input("not"))
                ->col("evrak_cari_id", $request->input("cari_id"))
                ->save_();



        if ($result) {

            $islem_id = $this->getConnection()->lastInsertId();



            if ($request->input("senet_tur") == 1) {


                 $this->reset()->table("tahsilatlar", Controller::$userInfo)
                                ->col("cari_id", $request->input("cari_id"))
                                ->col("islem_tip", "senet")
                                ->col("islem_id", $islem_id)
                                ->col("islem_tarih", $request->input("duzenlemetarih"))
                                ->col("islem_tutar", $request->input("tutar"))
                                ->col("islem_mesaj", $request->input("evrak_no") . " Evrak Numaralı Senet İle Tahsil")
                                ->save_();
            } else {


                 $this->reset()->table("odemeler", Controller::$userInfo)
                                ->col("cari_id", $request->input("cari_id"))
                                ->col("odeme_tip", "senet")
                                ->col("odeme_islem_id", $islem_id)
                                ->col("odeme_tutar", $request->input("tutar"))
                                ->col("odeme_tarih", $request->input("duzenlemetarih"))
                                ->col("odeme_mesaj", $request->input("evrak_no") . " Evrak Numaralı Senet İle Ödeme")
                                ->save_();
            }


            return $islem_id;


        } else {

            return false;
        }
    }

    public function senetListesi($request) {

        $odeme_durum = $request->input("odeme_durum");

        $evrak_tip = $request->input("evrak_tip");

        $whereArray["kiymetli_evraklar.evrak_tur"] = ["=", "senet"];

        if ($odeme_durum <= 1) {
            $whereArray["kiymetli_evraklar.odeme_durum"] = ["=", $odeme_durum];
        }


        if ($evrak_tip <= 2) {
            $whereArray["kiymetli_evraklar.evrak_tip"] = ["=", $evrak_tip];
        }


        $whereArray["kiymetli_evraklar.remove"] = ["=", 0];

        $whereArray["kiymetli_evraklar.evrak_son_odeme_tarihi"] = [">=", $request->input("bas_tarih")];
        $whereArray[" kiymetli_evraklar.evrak_son_odeme_tarihi "] = ["<=", $request->input("bit_tarih")];



        return $this->table("kiymetli_evraklar", Controller::$userInfo)
                        ->disableDefault()
                        ->select("  kiymetli_evraklar.* , cari.cari_adi ,cari.id as cari_id ")
                        ->leftjoin(["cari" => " kiymetli_evraklar.evrak_cari_id = cari.id "])
                        ->where($whereArray)
                        ->getAll();
    }

    public function senetDetaylari($senet_id) {


        $whereArray["kiymetli_evraklar.evrak_tur"] = ["=", "senet"];
        $whereArray["kiymetli_evraklar.id"] = ["=", $senet_id];
        $whereArray["kiymetli_evraklar.remove"] = ["=", 0];

        return $this->table("kiymetli_evraklar", Controller::$userInfo)
                        ->disableDefault()
                        ->select("  kiymetli_evraklar.* , cari.cari_adi ,cari.id as cari_id ")
                        ->leftjoin(["cari" => " kiymetli_evraklar.evrak_cari_id = cari.id "])
                        ->where($whereArray)
                        ->get();
    }



    public function senetIptal($senet_id){

          $senet_data = $this->table("kiymetli_evraklar", Controller::$userInfo)->find($senet_id)->get();


          if($senet_data){

              $evrak_tip = $senet_data["evrak_tip"];

              $this->table("kiymetli_evraklar", Controller::$userInfo)->find($senet_id)->remove_();



              if ($evrak_tip == 1) {


                  $tahsilat =  $this->reset()->table("tahsilatlar", Controller::$userInfo)
                      ->where([
                          "islem_tip"=>["=","senet"],
                          "islem_id"=>["=",$senet_id]
                      ])
                      ->get();


                  $tarih = date("Y-m-d H:i:s");


                  $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($tahsilat["id"])
                      ->col("iptal_mesaji",Controller::$userInfo["id"]." tarafından {$tarih} tarihinde senet iptalinden iptal edildi")->update_();

              return    $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($tahsilat["id"])->remove_();



              } else  if ($evrak_tip == 2) {





                  $odeme = $this->table("odemeler", Controller::$userInfo)
                      ->where([
                          "odeme_tip"=>["=","senet"],
                          "odeme_islem_id" => ["=",$senet_id]
                      ])
                      ->get();

                  $tarih = date("d-m-Y H:i:s");
                  $user = Controller::$userInfo["name"]." ".Controller::$userInfo["surname"];

                  if($odeme){

                      $this->reset()->table("odemeler", Controller::$userInfo)->find($odeme["id"])
                          ->col("iptal_mesaji",$user." tarafından {$tarih} tarihinde senet iptalinden iptal edildi")->update_();

                      return $this->reset()->table("odemeler", Controller::$userInfo)->find($odeme["id"])->remove_();




                  }else{


                      return false;
                  }
              }





          }else{

return false;
          }

    }


    public function senetOdenemedi($senet_id){

        $senet_data = $this->table("kiymetli_evraklar", Controller::$userInfo)->find($senet_id)->get();

        if($senet_data){

            $evrak_tip = $senet_data["evrak_tip"];

            $this->table("kiymetli_evraklar", Controller::$userInfo)->find($senet_id)->remove_();


            if ($evrak_tip == 1) {

                $tahsilat =  $this->reset()->table("tahsilatlar", Controller::$userInfo)
                    ->where([
                        "islem_tip"=>["=","senet"],
                        "islem_id"=>["=",$senet_id]
                    ])
                    ->get();


                $tarih = date("Y-m-d H:i:s");


                $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($tahsilat["id"])
                    ->col("iptal_mesaji",Controller::$userInfo["id"]." tarafından {$tarih} tarihinde senet Ödenemediğinden iptal edildi")->update_();

                return   $this->reset()->table("tahsilatlar", Controller::$userInfo)->find($tahsilat["id"])->remove_();


            } else  if ($evrak_tip == 2) {

                $odeme = $this->table("odemeler", Controller::$userInfo)
                    ->where([
                        "odeme_tip"=>["=","senet"],
                        "odeme_islem_id" => ["=",$senet_id]
                    ])
                    ->get();

                $tarih = date("d-m-Y H:i:s");
                $user = Controller::$userInfo["name"]." ".Controller::$userInfo["surname"];

                if($odeme){

                    $this->reset()->table("odemeler", Controller::$userInfo)->find($odeme["id"])
                        ->col("iptal_mesaji",$user." tarafından {$tarih} tarihinde senet Ödenemediğinden  iptal edildi")->update_();

                    return $this->reset()->table("odemeler", Controller::$userInfo)->find($odeme["id"])->remove_();




                }else{


                    return false;
                }
            }



        }else{

            return false;
        }

    }

}
