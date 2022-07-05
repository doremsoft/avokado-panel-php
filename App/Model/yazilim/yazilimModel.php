<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class yazilimModel extends Dimodel {
/*
 * Controller::$userInfo
 */
 
public function getList(){
    
    
     return $this->table("yazilimlar", Controller::$userInfo)->getAll();
}
    

public function getDevice($device_id){
    
  return $this->table("yazilimlar", Controller::$userInfo)->find($device_id)->get(); 
    
}


    public function stokDepolariAl() {

        return $this->table("stok_depolar", Controller::$userInfo)->getAll();
    }


    public function kasaListesi() {
        $tablo = $this->table("kasalar", Controller::$userInfo);

        return $this->where()->getAll();
    }
    
    public function hesaplar(){
        
          return $this->table("banka_hesaplari", Controller::$userInfo)->getAll();
    }
    
    public function guncelle($request){
        
        
        $hesap =  $this->table("banka_hesaplari", Controller::$userInfo)->find($request->input("pos_hesap_id"))->get();
        
        
         return $this->table("yazilimlar", Controller::$userInfo)
                 ->find($request->input("id"))
                 ->col("takma_adi",$request->input("takma_adi"))
                 ->col("master_parakende_cari_hesap_id",$request->input("master_parakende_cari_hesap_id"))
                 ->col("cikis_yapacagi_depo_id",$request->input("cikis_yapacagi_depo_id"))
                 ->col("kasa_id",$request->input("kasa_id"))
                 ->col("pos_hesap_id",$request->input("pos_hesap_id"))
                 ->col("pos_hesap_banka_id",$hesap["banka_id"])
                 ->col("activate",$request->input("activate"))
                 ->update_();
        
    }
}
