<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 * @author Doğuş DİCLE
 */
class stokDepolarModel extends Dimodel {

    public function stokDepoEkle($request) {

        return $this->table("stok_depolar", Controller::$userInfo)
                        ->col("stok_depo_adi", $request->input("stok-depo-adi"))
                        ->save_();
    }

    public function stokDepolariAl() {

        return $this->table("stok_depolar", Controller::$userInfo)->getAll();
    }

    public function stokDepoGuncelle($request) {

        return $this->table("stok_depolar", Controller::$userInfo)->find($request->input("id"))
                        ->col("stok_depo_adi", $request->input("value"))
                        ->update_();
    }

    public function stokDepoSil($request) {

        return $this->table("stok_depolar", Controller::$userInfo)->find($request->input("id"))->remove_();
    }

    public function urununDepolardakiListesi($stok_id) {

        $urunListesi = ['ozel' => [], 'klasik' => []];

        $sql = "SELECT seri_no,depo,adet,ozel_urun,id as ozel_urun_id FROM stok_haraket_giris WHERE remove = 0 and owner_id = :owner and stok_id = :stok_id and ozel_urun = 1 and ozel_urun_durum = 0";

        $query = $this->getConnection()->prepare($sql);

        $query->execute(array(':owner' => Controller::$userInfo['owner_id'], ':stok_id' => $stok_id));

        if ($query->rowCount() > 0) {

            $urunListesi["ozel"] = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        /*
         * 
         */




        $cikis_sql = "SELECT SUM(adet) as adet FROM stok_haraket_cikis "
                . "WHERE remove = 0 and owner_id = :owner and stok_id = :stok_id and ozel_urun_id = 0 and depo = stok_haraket_giris.depo  GROUP BY depo";


        $giris_sql = "SELECT seri_no,depo,SUM(adet) as adet,ozel_urun ,($cikis_sql) as cikisadet , alis_fiyati   FROM stok_haraket_giris "
                . "WHERE remove = 0 and owner_id = :owner and stok_id = :stok_id and ozel_urun = 0 GROUP BY depo";

        $giris_query = $this->getConnection()->prepare($giris_sql);

        $giris_query->execute(array(':owner' => Controller::$userInfo['owner_id'], ':stok_id' => $stok_id));

        if ($giris_query->rowCount() > 0) {

            $urunListesi["klasik"] = $giris_query->fetchAll(PDO::FETCH_ASSOC);
        }


        return $urunListesi;
    }

    public function stokDepoAl($id) {

        return $this->table("stok_depolar", Controller::$userInfo)->find($id)->get();
    }

    public function deponunStoklari($depo_id) {

        $urunListesi = ['ozel' => [], 'klasik' => []];

        $sql = "SELECT "
                . "stok_haraket_giris.seri_no,"
                . "stok_haraket_giris.depo,"
                . "stok_haraket_giris.adet,"
                . "stok_haraket_giris.ozel_urun,"
                . "stok_haraket_giris.id as ozel_urun_id , "
                . "stok.stok_adi , "
                . "stok.stok_kod , "
                . "stok.id as stok_id  "
                . "FROM stok_haraket_giris "
                . "INNER JOIN "
                . "stok "
                . "ON stok_haraket_giris.stok_id = stok.id "
                . "WHERE "
                . "stok_haraket_giris.remove = 0 and "
                . "stok_haraket_giris.owner_id = :owner and "
                . "stok_haraket_giris.depo = :depo_id and "
                . "stok_haraket_giris.ozel_urun = 1 and "
                . "stok_haraket_giris.ozel_urun_durum = 0";

        $query = $this->getConnection()->prepare($sql);

        $query->execute(array(':owner' => Controller::$userInfo['owner_id'], ':depo_id' => $depo_id));

        if ($query->rowCount() > 0) {

            $urunListesi["ozel"] = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        /*
         * 
         */

        $cikis_sql = "SELECT "
                . "SUM(stok_haraket_cikis.adet) as stok_cikis_adet "
                . "FROM "
                . "stok_haraket_cikis "
                . "WHERE "
                . "stok_haraket_cikis.remove = 0 and "
                . "stok_haraket_cikis.owner_id = :owner and "
                . "stok_haraket_cikis.ozel_urun_id = 0 and "
                . "stok_haraket_cikis.depo = :depo_id and "
                . "stok_haraket_cikis.stok_id = stok_haraket_giris.stok_id "
                . "GROUP BY "
                . "stok_haraket_cikis.stok_id";


        $giris_sql = ""
                . "SELECT "
                . "stok_haraket_giris.seri_no,"
                . "stok_haraket_giris.depo,"
                . "SUM(stok_haraket_giris.adet) as adet,"
                . "stok_haraket_giris.ozel_urun ,"
                . "stok.stok_adi , "
                . "stok.stok_kod , "
                . "stok.id as stok_id , "
                . "($cikis_sql) as cikisadet  "
                . "FROM "
                . "stok_haraket_giris "
                . "INNER JOIN "
                . "stok "
                . "ON stok_haraket_giris.stok_id = stok.id  "
                . "WHERE "
                . "stok_haraket_giris.remove = 0 and "
                . "stok_haraket_giris.owner_id = :owner and "
                . "stok_haraket_giris.depo = :depo_id and "
                . "stok_haraket_giris.ozel_urun = 0 "
                . "GROUP BY "
                . "stok_haraket_giris.stok_id";

        $giris_query = $this->getConnection()->prepare($giris_sql);

        $giris_query->execute(array(':owner' => Controller::$userInfo['owner_id'], ':depo_id' => $depo_id));

        if ($giris_query->rowCount() > 0) {

            $urunListesi["klasik"] = $giris_query->fetchAll(PDO::FETCH_ASSOC);
        }


        return $urunListesi;
    }

}
