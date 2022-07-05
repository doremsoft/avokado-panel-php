<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class stokBirimlerModel extends Dimodel {

    public function stokBirimEkle($request) {

        return $this->table("stok_birimler", Controller::$userInfo)
                        ->col("stok_birim_adi", $request->input("stok-birim-adi"))
                        ->save_();
    }
    public function stokBirimleriAl() {

        return $this->table("stok_birimler", Controller::$userInfo)->getAll();
    }
    public function stokBirimGuncelle($request){
        
        return $this->table("stok_birimler", Controller::$userInfo)->find($request->input("id"))
                        ->col("stok_birim_adi", $request->input("value"))
                        ->update_();
        
    }
    
    public function stokBirimSil($request){
        
        return $this->table("stok_birimler", Controller::$userInfo)->find($request->input("id"))->remove_();
        
    }
}
