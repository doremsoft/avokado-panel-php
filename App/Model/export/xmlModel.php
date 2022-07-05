<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class xmlModel extends Dimodel {
/*
 * Controller::$userInfo
 */
 

public function ipEkle($request,$account_no){

  return  $this->table("statik_ip_listesi",Controller::$userInfo)->col("ip_adress",$request->input("ip_adres"))->col("aciklama",$request->input("aciklama"))->col("account_no",$account_no)->save_();

}


public function acKapa($durum){


    $stokquery = $this->getConnection()->prepare("UPDATE  hesap_detaylari  SET  xml_servis = ?   WHERE  account_id = ? ");
   return  $stokquery->execute([$durum,Controller::$account_no]);

}

    public function ipIptal($id,$account_no){

        return  $this->table("statik_ip_listesi",Controller::$userInfo)->find($id)->remove_();

    }




public function getExportIpList($account ,$owner ){

    $stokquery = $this->getConnection()->prepare("SELECT *  FROM statik_ip_listesi  WHERE account_no = ?  and owner_id = ?  and  remove = 0");
    $stokquery->execute([$account ,$owner]);
    return $stokquery->fetchAll(PDO::FETCH_ASSOC);
}
    
}
