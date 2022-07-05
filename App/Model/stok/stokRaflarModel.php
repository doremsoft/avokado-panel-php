<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class stokRaflarModel extends Dimodel {

    public function stokRaflariAl() {


        $owner_id = Controller::$userInfo["id"];
        $sql="SELECT "
                    . "stok_raflar.id as raf_id , "
                    . "stok_raflar.stok_raf_adi as raf_adi , "
                    . "stok_depolar.id as depo_id , "
                    . "stok_depolar.stok_depo_adi as depo_adi "
                . "FROM "
                     . "stok_raflar "
                . "INNER JOIN "
                     . "stok_depolar "
                . "ON "
                     . "stok_depolar.id = stok_raflar.stok_depo_id "
                . "WHERE "
                     . "stok_raflar.remove = 0 AND stok_raflar.owner_id = $owner_id";
        
        $query = $this->getConnection()->query($sql, PDO::FETCH_ASSOC);
        if ($query->rowCount()) {
            return $query;
        } else {
            return false;
        }
    }

    public function stokRafEkle($request) {

        return $this->table("stok_raflar", Controller::$userInfo)
                        ->col("stok_raf_adi", $request->input("stok-raf-adi"))
                        ->col("stok_depo_id", $request->input("stok-depo-id"))
                        ->save_();
    }

    public function stokRafGuncelle($request) {

        return $this->table("stok_raflar", Controller::$userInfo)->find($request->input("id"))
                        ->col("stok_raf_adi", $request->input("value"))
                        ->col("stok_depo_id", $request->input("depo_id"))
                        ->update_();
    }

    public function stokRafSil($request) {

        return $this->table("stok_raflar", Controller::$userInfo)->find($request->input("id"))->remove_();
    }
    
    public function deponunRaflari($depo_id){
        
        
        return $this->table("stok_raflar", Controller::$userInfo)->findWhere(['stok_depo_id'=>['=',$depo_id]])->getAll();
        
    }

}
