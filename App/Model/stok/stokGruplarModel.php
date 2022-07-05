<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class stokGruplarModel extends Dimodel {

    public function stokGrupEkle($request) {
        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);


        $q_last_id = $query->fetch();
        $last_id = $q_last_id["last_id"];
        $last_id++;

        $addGroup = $this->table("stok_gruplar", Controller::$userInfo)
                ->col("stok_grup_adi", $request->input("stok-grup-adi"))
                ->col("stok_group_create_id", time())
                ->col("last_val", $last_id)
                ->save_();




        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);


        return $addGroup;
    }

    public function stokGruplariAl() {

        return $this->table("stok_gruplar", Controller::$userInfo)->getAll();
    }

    public function stokGrupGuncelle($request) {

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);
        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"];
        $last_id++;

        $updateGroup = $this->table("stok_gruplar", Controller::$userInfo)->find($request->input("id"))
                ->col("stok_grup_adi", $request->input("value"))
                ->col("last_val", $last_id)
                ->update_();





        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);


        return $updateGroup;
    }

    public function stokGrupSil($request) {


        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);
        $q_last_id = $query->fetch();
        $last_id = $q_last_id["last_id"];
        $last_id++;

        $updateGroup = $this->table("stok_gruplar", Controller::$userInfo)->find($request->input("id"))
                ->col("remove", 0)
                ->col("last_val", $last_id)
                ->update_();



        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);


        return $updateGroup;
    }

}
