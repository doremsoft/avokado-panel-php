<?php

namespace App\Model\Xml;

use \Dipa\Db\Dimodel;
use \Dipa\Controller;
use PDO;

set_time_limit(0);

/**
 *
 * @author Doğuş DİCLE
 */
class xmlModel extends Dimodel
{
    /*
     * Controller::$userInfo
     */
    /*
   function __construct()
   {
       // parent::__construct("pdo");
       // pdo - mysqli - pdox
   }
   */
    public $folder_fix;


    private $core_folder_path;
    private $core_thumbs_path;


    private $core_full_folder_path;
    private $core_full_thumbs_path;

    public $resim_gorevci = false;

    private $imageCount = 0;
    private $activeFolderCount = 0;
    private $imageFolderName = "";


    function __construct($lib = null, $config = null)
    {
        parent::__construct($lib, $config);

        $this->imageFolderName = time();

        $this->core_folder_path = Controller::$account_no . DS . "s";
        $this->core_thumbs_path = Controller::$account_no . DS . "t";

        $this->core_folder_full_path = MEDIA_DIR . DS . Controller::$account_no . DS . "s" . DS . "urun" . DS . $this->imageFolderName;
        $this->core_thumbs_full_path = MEDIA_DIR . DS . Controller::$account_no . DS . "t" . DS . "urun" . DS . $this->imageFolderName;

    }


    public function stokVarmi($kontrol_data = [], $varyant_kontol = false)
    {

        $result = [
            "stok_id" => 0,
            "durum" => false,
            "varyant" => false
        ];


        $where_sql = "";
        $where_execute_data = [];

        $i = 0;

        array_push($where_execute_data, Controller::$userInfo["owner_id"]);

        if ($varyant_kontol == true) {

            $kontrol_data = $this->replaceVaryant($kontrol_data);

        }

        foreach ($kontrol_data as $key => $val) {


            if ($val != NULL && $val != "") {


                if ($i == 0) {
                    $where_sql .= "  " . $key . " = ? ";
                } else {

                    $where_sql .= " or " . $key . " = ? ";
                }


                array_push($where_execute_data, $val);

                $i++;


            }

        }

        if ($where_sql == "") {

            return $result;

        }


        $query = $this->getConnection()->prepare("SELECT id , stok_parent_id FROM stok WHERE owner_id = ? and  remove = 0 and {$where_sql}    ORDER BY id DESC LIMIT 1 ");
        $query->execute($where_execute_data);
        $query_result = $query->fetch();

        if ($query_result) {
            $varyant = false;

            if ($query_result["stok_parent_id"] > 0) {

                $varyant = true;
            }

            $result = [
                "stok_id" => $query_result["id"],
                "durum" => true,
                "varyant" => $varyant
            ];
        }

        return $result;

    }


    private function markaControl($marka_adi)
    {


        $query = $this->getConnection()->prepare("SELECT id FROM markalar WHERE marka_adi = ? and  owner_id = ? and remove = 0  ");

        $query->execute([$marka_adi, Controller::$userInfo["owner_id"]]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }


    public function xmlDosyaEkle($dosya_adi, $ad, $dosya_url_adresi)
    {

        $kod = uniqid();

        $zaman = date("Y-m-d H:i:s");

        $insert_sql = "INSERT INTO xml_dosyalari (xml_dosya_adi,ad,owner_id,dosya_url_adresi,dosya_kod,update_date) VALUES (?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($insert_sql);

        return $query->execute([$dosya_adi, $ad, Controller::$userInfo["owner_id"], $dosya_url_adresi, $kod, $zaman]);


    }

    public function xmlDosyaListesi()
    {


        $query = $this->getConnection()->prepare("SELECT * FROM xml_dosyalari WHERE  owner_id = ? and remove = 0  ");

        $query->execute([Controller::$userInfo["owner_id"]]);

        return $query->fetchAll(PDO::FETCH_ASSOC);

    }

    public function xmlDosyasi($id)
    {


        $query = $this->getConnection()->prepare("SELECT * FROM xml_dosyalari WHERE  owner_id = ? and remove = 0 and id = ?   ");

        $query->execute([Controller::$userInfo["owner_id"], $id]);

        return $query->fetch();

    }

    public function xmlDosyasiAdIle($dosya_adi)
    {


        $query = $this->getConnection()->prepare("SELECT * FROM xml_dosyalari WHERE  owner_id = ? and remove = 0 and xml_dosya_adi = ?   ");

        $query->execute([Controller::$userInfo["owner_id"], $dosya_adi]);

        return $query->fetch();

    }



    public function xmlFavoriIslem($id, $durum)
    {

        $nowdate = date("Y-m-d H:i:s");

        $insert_sql = "UPDATE xml_yuklemeler SET favori = ? , update_date = ? WHERE owner_id = ? and  id = ?";

        $query = $this->getConnection()->prepare($insert_sql);

        return $query->execute([$durum, $nowdate, Controller::$userInfo["owner_id"], $id]);


    }

    public function xmlDosyaGuncelle($id)
    {

        $zaman = date("Y-m-d H:i:s");

        $insert_sql = "UPDATE xml_dosyalari SET update_date = ?  WHERE id = ? ";

        $query = $this->getConnection()->prepare($insert_sql);

        return $query->execute([$zaman, $id]);

    }


    private function markaEkle($marka_adi)
    {

        $insert_sql = "INSERT INTO markalar (marka_adi,owner_id) VALUES (?,?)";

        $query = $this->getConnection()->prepare($insert_sql);

        $query->execute([$marka_adi, Controller::$userInfo["owner_id"]]);

        return $this->getConnection()->lastInsertId();

    }

    private function imageControl($key, $url)
    {

        $query = $this->getConnection()->prepare("SELECT resim_kayit_adi FROM indirilen_resimler WHERE resim_url = ? and  owner_id = ? and remove = 0  ");

        $query->execute([$url, Controller::$userInfo["owner_id"]]);

        return $query->fetch();
    }


    public function xmlYuklemeleri($request)
    {

        return $this->table("xml_yuklemeler", Controller::$userInfo)->paginate($request);
    }


    public function xmlFavoriYuklemeleri($request)
    {

        return $this->table("xml_yuklemeler", Controller::$userInfo)->where([
            "favori" => ["=", 1]
        ])->paginate($request);
    }

    public function xmlYuklemeBilgileri($id)
    {
        return $this->table("xml_yuklemeler", Controller::$userInfo)->find($id)->get();
    }


    private function changeId()
    {

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        if ($q_last_id) {
            $last_id = $q_last_id["last_id"];
        } else {

            $last_id = 0;
        }

        return $last_id + 1;
    }

    private function updateChangeId($new_id)
    {

        $owner_id = Controller::$userInfo["owner_id"];

        return $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$new_id, $owner_id]);

    }

    private function downloadRemoteImage($sira, $source, $name)
    {


        $explode_result = preg_split("/\?/", $source);

        if (isset($explode_result[0])) {

            $source_name = $explode_result[0];

        } else {

            $source_name = $source;
        }

        $uniqkod = uniqid();

        $temp = explode(".", $source_name);
        $fileex = '.' . end($temp);
        $fileex = strtolower($fileex);
        $resim_dosya_adi = $name . $fileex;


        $result_image_name = "urun/" . $this->imageFolderName . "/" . $this->activeFolderCount . "/" . $resim_dosya_adi;


        $insert_sql = "INSERT INTO indirilen_resimler (resim_url,resim_kayit_adi,owner_id,indirme_durum,thumbs_folder,image_folder,resim_dosya_adi,resim_kod) VALUES (?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($insert_sql);

        $query->execute([$source, $result_image_name, Controller::$userInfo["owner_id"], 0, $this->core_thumbs_path, $this->core_folder_path, $resim_dosya_adi, $uniqkod]);


        $this->resim_gorevci = true;

        $this->imageCount++;


        if ($this->imageCount == 50) {

            $this->activeFolderCount++;

            $this->imageCount = 0;
        }


        return $result_image_name;
    }


    public function seo_url($s)
    {
        $tr = array('ş', 'Ş', 'ı', 'I', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç', '(', ')', '/', ':', ',', '\'', '"', "?");
        $eng = array('s', 's', 'i', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c', '', '', '-', '-', '', '-', '-', '-');
        $s = str_replace($tr, $eng, $s);
        $s = strtolower($s);
        $s = preg_replace('/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/', '', $s);
        $s = preg_replace('/\s+/', '-', $s);
        $s = preg_replace('|-+|', '-', $s);
        $s = preg_replace('/#/', '', $s);
        $s = str_replace('.', '', $s);
        $s = trim($s, '-');
        return $s;
    }

    public function stokEkle($kontrol_data = [], $vergi_dahil = false)
    {


        $where_sql = "";
        $where_execute_data = [];
        $seo_url = "";


        if (isset($kontrol_data["stok_adi"])) {

            $seo_url = $this->seo_url($kontrol_data["stok_adi"]);

        }

        $where_sql .= " , stok_seo_url = ? ";

        array_push($where_execute_data, $seo_url);


        $changeid = $this->changeId();

        $where_sql .= " , last_val = ? ";

        array_push($where_execute_data, $changeid);

        $where_sql .= " , stok_create_id = ? ";

        array_push($where_execute_data, uniqid());


        foreach ($kontrol_data as $key => $val) {


            if ($key == "stok_resim" || $key == "stok_resim2" || $key == "stok_resim3" || $key == "stok_resim4" || $key == "stok_resim5") {


                $resim_url = $seo_url;


                if ($key == "stok_resim") {
                    $resim_url = $resim_url;
                }

                if ($key == "stok_resim2") {
                    $resim_url = $resim_url . "-2";
                }

                if ($key == "stok_resim3") {
                    $resim_url = $resim_url . "-3";
                }

                if ($key == "stok_resim4") {
                    $resim_url = $resim_url . "-4";
                }

                if ($key == "stok_resim5") {
                    $resim_url = $resim_url . "-5";
                }


                $val = $this->downloadRemoteImage($key, $val, $resim_url);

            }






            if ($key == "stok_satis_fiyati") {

                    $val = $this->tofloat($val);

                    if ($vergi_dahil == true) {

                        $val = $val / 1.18;
                    }

            }



            if ($key == "stok_doviz") {

                if ($val == "EURO") {

                    $val = "EUR";

                } else if ($val == "DOLAR") {

                    $val = "USD";
                }else if ($val == "YTL") {

                    $val = "TL";
                }




            }


            $where_sql .= " , " . $key . " = ? ";

            array_push($where_execute_data, $val);
        }

        $where_sql .= " , owner_id= ? ";

        array_push($where_execute_data, Controller::$userInfo["owner_id"]);

        $query = $this->getConnection()->prepare("INSERT INTO  stok SET  remove = 0 {$where_sql} ");

        $insert = $query->execute($where_execute_data);

        if ($insert) {

            $id = $this->getConnection()->lastInsertId();

            $this->updateChangeId($changeid);

            return $id;

        } else {

            return 0;
        }


    }

    function tofloat($num)
    {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num)))
        );
    }

    public function stokGuncelle($kontrol_data = [], $stok_id, $vergi_dahil = false)
    {

        $where_sql = "";

        $where_execute_data = [];

        $resim_adi = "";


        if (isset($kontrol_data["stok_adi"])) {

            $resim_adi = $this->seo_url($kontrol_data["stok_adi"]);

        }


        if (trim($resim_adi) == "") {

            $resim_adi = uniqid();
        }


        $changeid = $this->changeId();

        $where_sql .= " , last_val = ? ";

        array_push($where_execute_data, $changeid);

        foreach ($kontrol_data as $key => $val) {


            if ($key == "stok_resim" || $key == "stok_resim2" || $key == "stok_resim3" || $key == "stok_resim4" || $key == "stok_resim5") {


                $resim_url = $resim_adi;


                if ($key == "stok_resim2") {
                    $resim_url = $resim_url . "-2";
                }

                if ($key == "stok_resim3") {
                    $resim_url = $resim_url . "-3";
                }

                if ($key == "stok_resim4") {
                    $resim_url = $resim_url . "-4";
                }

                if ($key == "stok_resim5") {
                    $resim_url = $resim_url . "-5";
                }


                $val = $this->downloadRemoteImage($key, $val, $resim_url);

            }


            if ($key == "stok_doviz") {


                if ($val == "EURO") {

                    $val = "EUR";

                } else if ($val == "DOLAR") {

                    $val = "USD";
                }else if ($val == "YTL") {

                    $val = "TL";
                }


            }

            if ($key == "stok_satis_fiyati") {

                $val = $this->tofloat($val);

                if ($vergi_dahil == true) {

                    $val = $val / 1.18;
                }

            }


            $where_sql .= " , " . $key . " = ? ";

            array_push($where_execute_data, $val);

        }

        array_push($where_execute_data, $stok_id);

        array_push($where_execute_data, Controller::$userInfo["owner_id"]);

        $query = $this->getConnection()->prepare("UPDATE  stok SET  remove = 0 {$where_sql} WHERE id = ? and owner_id = ? ");

        $update = $query->execute($where_execute_data);

        if ($update) {

            $this->updateChangeId($changeid);

            return true;

        } else {
            return false;
        }


    }

    public function varyantStokEkle($kontrol_data = [], $parent_stok_id, $vergi_dahil = false)
    {


        $where_sql = "";
        $where_execute_data = [];

        $kontrol_data = $this->replaceVaryant($kontrol_data);

        foreach ($kontrol_data as $key => $val) {

            if ($val != NULL && $val != "") {

                if ($key == "stok_satis_fiyati") {

                    $val = $this->tofloat($val);

                    if ($vergi_dahil == true) {

                        $val = $val / 1.18;
                    }

                }




                $where_sql .= " , " . $key . " = ? ";

                array_push($where_execute_data, $val);

            }

        }

        $seo_url = "";


        if (isset($kontrol_data["stok_adi"]) && isset($kontrol_data["varyantadi"]) && isset($kontrol_data["vstokkodu"])) {

            $seo_url = $this->seo_url($kontrol_data["stok_adi"]) . "-" . $this->seo_url($kontrol_data["varyantadi"]) . "-" . $this->seo_url($kontrol_data["vstokkodu"]);

        }

        $where_sql .= " , stok_seo_url = ? ";

        array_push($where_execute_data, $seo_url);


        $where_sql .= " , owner_id= ? ";

        array_push($where_execute_data, Controller::$userInfo["owner_id"]);

        $where_sql .= " , stok_parent_id= ? ";

        array_push($where_execute_data, $parent_stok_id);


        $where_sql .= " , stok_create_id= ? ";

        array_push($where_execute_data, uniqid());


        $changeid = $this->changeId();

        $where_sql .= " , last_val = ? ";

        array_push($where_execute_data, $changeid);

        $query = $this->getConnection()->prepare("INSERT INTO  stok SET  remove = 0 {$where_sql} ");

        $insert = $query->execute($where_execute_data);

        if ($insert) {


            $last_id = $this->getConnection()->lastInsertId();

            $this->updateChangeId($changeid);

            return $last_id;

        } else {


            return 0;
        }


    }

    public function varyantStokGuncelle($kontrol_data = [], $stok_id, $parent_stok_id, $vergi_dahil = false)
    {

        $where_sql = "";

        $where_execute_data = [];

        $kontrol_data = $this->replaceVaryant($kontrol_data);

        foreach ($kontrol_data as $key => $val) {

            if ($val != NULL && $val != "") {
                if ($key == "stok_satis_fiyati") {

                    $val = $this->tofloat($val);

                    if ($vergi_dahil == true) {

                        $val = $val / 1.18;
                    }

                }


                $where_sql .= " , " . $key . " = ? ";

                array_push($where_execute_data, $val);
            }

        }


        $changeid = $this->changeId();

        $where_sql .= " , last_val = ? ";

        array_push($where_execute_data, $changeid);


        array_push($where_execute_data, $stok_id);
        array_push($where_execute_data, Controller::$userInfo["owner_id"]);


        $query = $this->getConnection()->prepare("UPDATE  stok SET  remove = 0 {$where_sql} WHERE id = ? and owner_id = ? ");

        $update = $query->execute($where_execute_data);

        if ($update) {

            $this->updateChangeId($changeid);

            return true;

        } else {
            return false;
        }


    }

    private function replaceVaryant($kontrol_data)
    {

        if (isset($kontrol_data["varyant_barkod"])) {

            $kontrol_data["stok_barkod_no"] = $kontrol_data["varyant_barkod"];
            $kontrol_data["varyant_barkod"] = NULL;

        }

        if (isset($kontrol_data["varyant_stok_kod"])) {

            $kontrol_data["stok_kod"] = $kontrol_data["varyant_stok_kod"];
            $kontrol_data["varyant_stok_kod"] = NULL;

        }

        if (isset($kontrol_data["varyant_satis_fiyat1"])) {

            $kontrol_data["stok_satis_fiyati"] = $kontrol_data["varyant_satis_fiyat1"];

            $kontrol_data["varyant_satis_fiyat1"] = NULL;

        }

        if (isset($kontrol_data["varyant_satis_fiyat2"])) {

            $kontrol_data["stok_satis_fiyati2"] = $kontrol_data["varyant_satis_fiyat2"];
            $kontrol_data["varyant_satis_fiyat2"] = NULL;

        }

        if (isset($kontrol_data["varyant_satis_fiyat3"])) {

            $kontrol_data["stok_satis_fiyati3"] = $kontrol_data["varyant_satis_fiyat3"];
            $kontrol_data["varyant_satis_fiyat3"] = NULL;

        }

        return $kontrol_data;
    }


    public function xmlSonucKaydet($url_adresi, $firma_adi, $jsondata, $result_json, $xml_adi, $mod, $xml_kod)
    {

        $nowdate = date("Y-m-d H:i:s");

        $insert_sql = "INSERT INTO xml_yuklemeler (url_adresi,firma_adi,xml_data,owner_id,result_json,xml_adi,durum,created_date,created_user,xml_kod) VALUES (?,?,?,?,?,?,?,?,?,?)";

        $query = $this->getConnection()->prepare($insert_sql);

        $query->execute([$url_adresi, $firma_adi, $jsondata, Controller::$userInfo["owner_id"], $result_json, $xml_adi, $mod, $nowdate, Controller::$userInfo["id"], $xml_kod]);

        return $this->getConnection()->lastInsertId();

    }
    /*
     *
     *     private function compress($source, $destination, $quality)
        {

            $info = getimagesize($source);

            if ($info['mime'] == 'image/jpeg')
                $image = imagecreatefromjpeg($source);
            elseif ($info['mime'] == 'image/jpg')
                $image = imagecreatefromjpeg($source);

            elseif ($info['mime'] == 'image/gif')
                $image = imagecreatefromgif($source);

            elseif ($info['mime'] == 'image/png')
                $image = imagecreatefrompng($source);

            imagejpeg($image, $destination, $quality);

            return $destination;
        }


        private function downloadRemoteImage($source, $name)
        {


            $explode_result = preg_split("/\?/", $source);


            if (isset($explode_result[0])) {

                $source_name = $explode_result[0];

            } else {

                $source_name = $source;
            }


            $temp = explode(".", $source_name);
            $fileex = '.' . end($temp);
            $fileex = strtolower($fileex);
            $resim_adi = $name . $fileex;

            $result_image_name = "images" . DS . "stok" . DS . date("Y") . DS . date("m");

            $core_folder_path = MEDIA_DIR . DS . Controller::$account_no . DS . "s" . DS . "images" . DS . "stok" . DS . date("Y") . DS . date("m");
            $core_thumbs_path = MEDIA_DIR . DS . Controller::$account_no . DS . "t" . DS . "images" . DS . "stok" . DS . date("Y") . DS . date("m");


            if ($this->folder_fix != null) {

                $folder_path = $core_folder_path . DS . $this->folder_fix;
                $thumbs_path = $core_thumbs_path . DS . $this->folder_fix;
            }else{
                $folder_path = $core_folder_path;
                $thumbs_path = $core_thumbs_path;

            }

            if (!file_exists($folder_path)) {

                mkdir($folder_path, 0777, true);

            }

            if (!file_exists($thumbs_path)) {

                mkdir($thumbs_path, 0777, true);

            }


            $filecount = 0;

            if ($handle = opendir($folder_path)) {

                while (($file = readdir($handle)) !== false) {
                    if (!in_array($file, array('.', '..')) && !is_dir($folder_path . $file))
                        $filecount++;
                }
            }


            if ($filecount >= 200) {

                $this->folder_fix = date("His");
                $folder_path = $core_folder_path . DS . $this->folder_fix;
                $thumbs_path = $core_thumbs_path . DS . $this->folder_fix;

            }


            if ($this->folder_fix == null) {

                $result_image_name = $result_image_name . "/" . $resim_adi;

            } else {

                $result_image_name = $result_image_name . "/" . $this->folder_fix . "/" . $resim_adi;

            }


            if (!file_exists($folder_path)) {

                mkdir($folder_path, 0777, true);

            }

            if (!file_exists($thumbs_path)) {

                mkdir($thumbs_path, 0777, true);

            }


            $image_file = $folder_path . DS . $resim_adi;
            $thumbs_file = $thumbs_path . DS . $resim_adi;


            if (!file_exists($image_file)) {

                copy($source, $image_file);

                try {

                    $this->compress($image_file, $image_file, 50);

                    $image = new \claviska\SimpleImage($image_file);

                    $Width = $image->getWidth();

                    if ($Width > 1400) {

                        $image->resize(1400, null)->toFile($image_file);

                    }

                    $image = null;

                } catch (Exception $err) {


                }


                try {

                    $image = new \claviska\SimpleImage($image_file);

                    $image->resize(122, 91)->toFile($thumbs_file);

                    $image = null;

                } catch (Exception $err) {


                }


                $insert_sql = "INSERT INTO indirilen_resimler (resim_url,resim_kayit_adi,owner_id) VALUES (?,?,?)";

                $query = $this->getConnection()->prepare($insert_sql);

                $query->execute([$source, $result_image_name, Controller::$userInfo["owner_id"]]);

            }


            return $result_image_name;
        }
    */

}
