<?php

use Dipa\Db\Dimodel;
use Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class cariFaturaModel extends Dimodel {

    public function getCariFaturaBilgisi($id) {

        return $this->table("cari_fatura_bilgileri", Controller::$userInfo)
                        ->where(['id' => ['=', $id]])
                        ->get();
    }

    public function cariFaturaUpdate($request, $fatura_id) {
        return $this->table("cari_fatura_bilgileri", Controller::$userInfo)
                        ->find($fatura_id)
                        ->col("cari_unvan", $request->input("cari_unvan"))
                        ->col("cari_vergi_no", $request->input("cari_vergi_no"))
                        ->col("cari_vergi_daire", $request->input("cari_vergi_daire"))
                        ->col("cari_fatura_adres", $request->input("cari_fatura_adres"))
                        ->update_();
    }

}
