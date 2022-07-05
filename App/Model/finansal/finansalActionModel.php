<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class finansalActionModel extends Dimodel {
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
public function sabitOdemeEkle($request)
{

  return $this->table("sabit_odemeler",Controller::$userInfo)
      ->col("cari_id",$request->input("cari_id"))
      ->col("tutar",$request->input("tutar"))
      ->col("gun",$request->input("gun"))
      ->col("aciklama",$request->input("aciklama"))
      ->col("ondecen_bildirim",$request->input("ondecen_bildirim"))
      ->col("ayni_gun_bildirim",$request->input("ayni_gun_bildirim"))
      ->col("ertesi_gun_bildirim",$request->input("ertesi_gun_bildirim"))
      ->col("tur",1)
      ->col("baslama_tarihi",$request->input("baslama_tarihi"))
      ->col("bitis_tarihi",$request->input("bitis_tarihi"))
      ->col("baslik",$request->input("baslik"))
      ->col("tutar_doviz",$request->input("tutar_doviz"))
      ->col("bildirim_durum",$request->input("bildirim_durum"))
      ->save_();
}
    public function sabitOdemeGuncelle($request)
    {

        return $this->table("sabit_odemeler",Controller::$userInfo)
            ->find($request->input("id"))
            ->col("cari_id",$request->input("cari_id"))
            ->col("tutar",$request->input("tutar"))
            ->col("gun",$request->input("gun"))
            ->col("aciklama",$request->input("aciklama"))
            ->col("ondecen_bildirim",$request->input("ondecen_bildirim"))
            ->col("ayni_gun_bildirim",$request->input("ayni_gun_bildirim"))
            ->col("ertesi_gun_bildirim",$request->input("ertesi_gun_bildirim"))
            ->col("tur",1)
            ->col("baslama_tarihi",$request->input("baslama_tarihi"))
            ->col("bitis_tarihi",$request->input("bitis_tarihi"))
            ->col("baslik",$request->input("baslik"))
            ->col("tutar_doviz",$request->input("tutar_doviz"))
            ->col("bildirim_durum",$request->input("bildirim_durum"))
            ->update_();
    }


    
}
