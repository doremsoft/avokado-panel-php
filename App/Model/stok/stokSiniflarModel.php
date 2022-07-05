<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class stokSiniflarModel extends Dimodel {
 

        public function stokSinifEkle($request) {

        return $this->table("stok_siniflar", Controller::$userInfo)
                        ->col("stok_sinif_adi", $request->input("stok-sinif-adi"))
                        ->save_();
    }
    public function stokSiniflariAl() {

        return $this->table("stok_siniflar", Controller::$userInfo)->getAll();
    }
    public function stokSinifGuncelle($request){
        
        return $this->table("stok_siniflar", Controller::$userInfo)->find($request->input("id"))
                        ->col("stok_sinif_adi", $request->input("value"))
                        ->update_();
        
    }
    
    public function stokSinifSil($request){
        
        return $this->table("stok_siniflar", Controller::$userInfo)->find($request->input("id"))->remove_();
        
    }
    
}
