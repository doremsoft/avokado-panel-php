<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class masaModel extends Dimodel {
/*
 * Controller::$userInfo
 */
 
    
    
    public function getGrupMasaList(){
        
        
        return $this->table("masa_kategorileri",Controller::$userInfo)->getAll();
    }
    
    public function masaKaydet($request){
        
        
        return $this->table("masalar",Controller::$userInfo)
                ->col("masa_adi",$request->input("masa_adi"))
                   ->col("masa_kategori_id",$request->input("kat_id"))
                ->save_();
        
    }
    
    public function masaGrupEkle($request){


        return $this->table("masa_kategorileri",Controller::$userInfo)
                ->col("masa_kategori_adi",$request->input("masa_kategori_adi"))
                ->save_();
        
    }
    
    public function kategoriSil($request)
    {
        
        return $this->table("masa_kategorileri",Controller::$userInfo)
                ->find($request->input("id"))
                ->remove_();
        
    }
    
      public function kategoriGuncelle($request){


        return $this->table("masa_kategorileri",Controller::$userInfo)
                ->find($request->input("id"))
                ->col("masa_kategori_adi",$request->input("value"))
                ->update_();
        
    }
    
    
    public function getMasaListWithCatIs($cat_id){
        
             return $this->table("masalar",Controller::$userInfo)
                     
                     ->where(['masa_kategori_id'=>[
                         
                         '=',$cat_id
                         
                         
                     ]])
                     ->getAll(); 
    }
    
    
        public function sil($request)
    {
        
        return $this->table("masalar",Controller::$userInfo)
                ->find($request->input("id"))
                ->remove_();
        
    }
    
      public function guncelle($request){


        return $this->table("masalar",Controller::$userInfo)
                ->find($request->input("id"))
                ->col("masa_adi",$request->input("value"))
                ->update_();
        
    }
    
}
