<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class stokImportModel extends Dimodel {
    
    
    public function varyantEkleGuncelle($val) {
        
        
        $exits_id = $this->ifExits(trim($val["stok_kod"]),trim($val["stok_barkod_no"]));


        if ($exits_id) {


            $this->updateVaryant($val, $exits_id["id"]);
            
        } else {



            $this->addVaryant($val);
        }
        
    }
    
    

    public function stokEkleGuncelle($val) {

        $exits_id = $this->ifExits(trim($val["stok_kod"]), trim($val["stok_barkod_no"]));

        if ($val["stok_marka_ad"] == "no") {
            
              $val["stok_marka_id"] = 0;
              
        }else{

            $marka_id = $this->markaControl($val["stok_marka_ad"]);

            if ($marka_id) {

                $val["stok_marka_id"] = $marka_id["id"];
            } else {

                $val["stok_marka_id"] = $this->markaEkle($val["stok_marka_ad"]);
            }
        }

        if ($exits_id) {


            if ($val["stok_resim"] != "noimage.jpg") {

                $resim = $this->imageControl($val["stok_resim"]);

                if ($resim) {

                    $val["stok_resim"] = $resim["resim_kayit_adi"];
                } else {

                    $val["stok_resim"] = $this->downloadRemoteImage($val["stok_resim"], $val["stok_seo_url"]);
                }
            }


            $this->updateStok($val, $exits_id["id"]);
            
            return $exits_id["id"];
           
        } else {


            if ($val["stok_resim"] != "noimage.jpg") {

                $resim = $this->imageControl($val["stok_resim"]);

                if ($resim) {

                    $val["stok_resim"] = $resim["resim_kayit_adi"];
                } else {

                    $val["stok_resim"] = $this->downloadRemoteImage($val["stok_resim"], $val["stok_seo_url"]);
                }
            }

           return $this->addStok($val);
        }
    }

    private function markaControl($marka_adi) {


        $query = $this->getConnection()->prepare("SELECT id FROM markalar WHERE marka_adi = ? and  owner_id = ? and remove = 0  ");

        $query->execute([$marka_adi, Controller::$userInfo["owner_id"]]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function markaEkle($marka_adi) {

        $insert_sql = "INSERT INTO markalar (marka_adi,owner_id) VALUES (?,?)";

        $query = $this->getConnection()->prepare($insert_sql);



        try {
            $this->getConnection()->beginTransaction();
            $query->execute([$marka_adi, Controller::$userInfo["owner_id"]]);
            $this->getConnection()->commit();
            return $this->getConnection()->lastInsertId();
        } catch (PDOExecption $e) {
            $dbh->rollback();
            return 0;
        }
    }

    private function imageControl($url) {

        $query = $this->getConnection()->prepare("SELECT resim_kayit_adi FROM indirilen_resimler WHERE resim_url = ? and  owner_id = ? and remove = 0  ");

        $query->execute([$url, Controller::$userInfo["owner_id"]]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function ifExits($stok_code, $barcode) {
        
        
        if($stok_code == "" && $barcode == ""){
            return false;
        }else if($stok_code != "" && $barcode != ""){
            
            $query = $this->getConnection()->prepare("SELECT id FROM stok WHERE (stok_kod = ? OR stok_barkod_no = ?) and  owner_id = ? and remove = 0  ");

            $query->execute([$stok_code, $barcode, Controller::$userInfo["owner_id"]]);

            return $query->fetch(PDO::FETCH_ASSOC);
        
        }else if($stok_code != "" && $barcode == ""){
            
            $query = $this->getConnection()->prepare("SELECT id FROM stok WHERE stok_kod = ? and  owner_id = ? and remove = 0  ");

            $query->execute([$stok_code, Controller::$userInfo["owner_id"]]);

            return $query->fetch(PDO::FETCH_ASSOC);
        
        }else if($stok_code == "" && $barcode != ""){
            
            $query = $this->getConnection()->prepare("SELECT id FROM stok WHERE  stok_barkod_no = ? and  owner_id = ? and remove = 0  ");

            $query->execute([ $barcode, Controller::$userInfo["owner_id"]]);

            return $query->fetch(PDO::FETCH_ASSOC);
        
        }else{
            
            return false;
        }


       
    }

    private function downloadRemoteImage($source, $name) {


        $explode_result = preg_split("/\?/", $source);

        $source_name = "";

        if (isset($explode_result[0])) {

            $source_name = $explode_result[0];
        } else {

            $source_name = $source;
        }


        $resim_adi = $name . "." . $this->getImageType(exif_imagetype($source_name));

        $media_folder = \Dipa\App::getConfig("mediaUrl");

        $account_no = Controller::$account_no;
        
        

        $image = MEDIA_DIR . "/" . $account_no . "/s/stok-foto/" . $resim_adi;

        if (!file_exists($image)) {

            copy($source, $image);
            
            $insert_sql = "INSERT INTO indirilen_resimler (resim_url,resim_kayit_adi,owner_id) VALUES (?,?,?)";

            $query = $this->getConnection()->prepare($insert_sql);

            $query->execute([$source, $resim_adi, Controller::$userInfo["owner_id"]]);
            
        }



        return $resim_adi;
    }

    private function getImageType($type) {


        switch ($type) {

            case 1:
                return "gif";
                break;
            case 2;
                return "jpg";
                break;
            case 3;
                return "png";
                break;
        }
    }

    private function ifUrlImage($str) {

        if (strpos($str, "http://") || strpos($str, "https://")) {

            return true;
        } else {
            return false;
        }
    }

    private function addStok($val) {


        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();


        $last_id = $q_last_id["last_id"];

        $last_id = $last_id + 1;

        extract($val);

        $stok_create_id = uniqid();

        $last_val = $last_id;

        $insert_sql = "INSERT INTO stok (stok_barkod_no,
stok_kod,
stok_ozel_kod,
stok_adi,
stok_cinsi,
stok_birimi,
stok_sinif,
stok_grup,
stok_resim,
stok_adet,
stok_ozel_urun_adet,
stok_min_seviyesi,
stok_max_seviyesi,
stok_alis_fiyati,
stok_satis_fiyati,
stok_max_iskontolu_satis_fiyati,
stok_kdv_oran,
stok_kdv_detay,
stok_detayi,
stok_create_id,
last_val,
stok_kdv_dahil_satis_fiyati,
stok_fiyat_vergi_durum,
aktif,
stok_standart_adet,
stok_doviz,
stok_satis_iskonto_oran,
stok_alim_iskonto_oran,
stok_seo_url,
stok_parent_id,
stok_marka_id,
stok_tipi,
stok_varyant_adi,
stok_perakende_satis,
stok_web_satis,
stok_portal_satis,owner_id,created_date,created_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";



        $query = $this->getConnection()->prepare($insert_sql);

        $query->execute([
            $stok_barkod_no,
            $stok_kod,
            $stok_ozel_kod,
            $stok_adi,
            $stok_cinsi,
            $stok_birimi,
            $stok_sinif,
            $stok_grup,
            $stok_resim,
            $stok_adet,
            $stok_ozel_urun_adet,
            $stok_min_seviyesi,
            $stok_max_seviyesi,
            $stok_alis_fiyati,
            $stok_satis_fiyati,
            $stok_max_iskontolu_satis_fiyati,
            $stok_kdv_oran,
            $stok_kdv_detay,
            $stok_detayi,
            $stok_create_id,
            $last_val,
            $stok_kdv_dahil_satis_fiyati,
            $stok_fiyat_vergi_durum,
            $aktif,
            $stok_standart_adet,
            $stok_doviz,
            $stok_satis_iskonto_oran,
            $stok_alim_iskonto_oran,
            $stok_seo_url,
            $stok_parent_id,
            $stok_marka_id,
            $stok_tipi,
            $stok_varyant_adi,
            $stok_perakende_satis,
            $stok_web_satis,
            $stok_portal_satis,
            $owner_id,
            date("Y-m-d H:i:s"),
            Controller::$userInfo["id"]
        ]);
        
        $stok_id = $this->getConnection()->lastInsertId();


        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);
    
        
        return $stok_id;
        
    }

    private function updateStok($val, $id) {


        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();


        $last_id = $q_last_id["last_id"];

        $last_id = $last_id + 1;

        extract($val);

        $stok_create_id = uniqid();

        $last_val = $last_id;

        $update_sql = "UPDATE stok SET stok_barkod_no=?,
stok_kod=?,
stok_ozel_kod=?,
stok_adi=?,
stok_cinsi=?,
stok_birimi=?,
stok_sinif=?,
stok_grup=?,
stok_resim=?,
stok_adet=?,
stok_ozel_urun_adet=?,
stok_min_seviyesi=?,
stok_max_seviyesi=?,
stok_alis_fiyati=?,
stok_satis_fiyati=?,
stok_max_iskontolu_satis_fiyati=?,
stok_kdv_oran=?,
stok_kdv_detay=?,
stok_detayi=?,
last_val=?,
stok_kdv_dahil_satis_fiyati=?,
stok_fiyat_vergi_durum=?,
aktif=?,
stok_standart_adet=?,
stok_doviz=?,
stok_satis_iskonto_oran=?,
stok_alim_iskonto_oran=?,
stok_parent_id=?,
stok_marka_id=?,
stok_tipi=?,
stok_varyant_adi=?,
stok_perakende_satis=?,
stok_web_satis=?,
stok_portal_satis=? WHERE id = ?";

        $query = $this->getConnection()->prepare($update_sql);

        $query->execute([
            $stok_barkod_no,
            $stok_kod,
            $stok_ozel_kod,
            $stok_adi,
            $stok_cinsi,
            $stok_birimi,
            $stok_sinif,
            $stok_grup,
            $stok_resim,
            $stok_adet,
            $stok_ozel_urun_adet,
            $stok_min_seviyesi,
            $stok_max_seviyesi,
            $stok_alis_fiyati,
            $stok_satis_fiyati,
            $stok_max_iskontolu_satis_fiyati,
            $stok_kdv_oran,
            $stok_kdv_detay,
            $stok_detayi,
            $last_val,
            $stok_kdv_dahil_satis_fiyati,
            $stok_fiyat_vergi_durum,
            $aktif,
            $stok_standart_adet,
            $stok_doviz,
            $stok_satis_iskonto_oran,
            $stok_alim_iskonto_oran,
            $stok_parent_id,
            $stok_marka_id,
            $stok_tipi,
            $stok_varyant_adi,
            $stok_perakende_satis,
            $stok_web_satis,
            $stok_portal_satis,
            $id
        ]);

        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);
    
        return $id;
    }
    
    
    
    private function updateVaryant($val, $id){
        
        
    }
    
  private function addVaryant($val) {


        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();


        $last_id = $q_last_id["last_id"];

        $last_id = $last_id + 1;

        extract($val);

        $stok_create_id = uniqid();

        $last_val = $last_id;

        $insert_sql = "INSERT INTO stok (stok_barkod_no,
stok_kod,
stok_adet,
stok_min_seviyesi,
stok_max_seviyesi,
stok_alis_fiyati,
stok_satis_fiyati,
stok_max_iskontolu_satis_fiyati,
stok_create_id,
last_val,
stok_kdv_dahil_satis_fiyati,
aktif,
stok_standart_adet,
stok_satis_iskonto_oran,
stok_alim_iskonto_oran,
stok_seo_url,
stok_parent_id,
stok_tipi,
stok_varyant_adi,
stok_varyant_deger,
stok_perakende_satis,
stok_web_satis,
stok_portal_satis,
owner_id,
created_date,
created_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";



        $query = $this->getConnection()->prepare($insert_sql);

        $query->execute([
            $stok_barkod_no,
            $stok_kod,
            $stok_adet,
            $stok_min_seviyesi,
            $stok_max_seviyesi,
            $stok_alis_fiyati,
            $stok_satis_fiyati,
            $stok_max_iskontolu_satis_fiyati,
            $stok_create_id,
            $last_val,
            $stok_kdv_dahil_satis_fiyati,
            $aktif,
            $stok_standart_adet,
            $stok_satis_iskonto_oran,
            $stok_alim_iskonto_oran,
            $stok_seo_url,
            $stok_parent_id,
            $stok_tipi,
            $stok_varyant_adi,
            $stok_varyant_deger,
            $stok_perakende_satis,
            $stok_web_satis,
            $stok_portal_satis,
            $owner_id,
            date("Y-m-d H:i:s"),
            Controller::$userInfo["id"]
        ]);
        
        $stok_id = $this->getConnection()->lastInsertId();


        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);
    
        
        return $stok_id;
        
    }


}
