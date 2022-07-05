<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;
use \Dipa\Io\File;
use \Dipa\Sys\Session;

/**
 *
 * @author Doğuş DİCLE
 */
class stokActionModel extends Dimodel
{

    private function compress($source, $destination, $quality)
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

    private function seo_url($s)
    {
        $tr = array('ş', 'Ş', 'ı', 'I', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç', '(', ')', '/', ':', ',');
        $eng = array('s', 's', 'i', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c', '', '', '-', '-', '');
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


    public function addStok($request)
    {

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();


        $last_id = $q_last_id["last_id"];

        $last_id++;

        $session = new Session();

        $account_no = $session->get("account_no");

        $image_upload = 0;

        $resim_adi = $request->input("stok_resim");


        if ($request->input("stok_kdv_oran") > 9) {

            $kdv_duzelt = "1." . $request->input("stok_kdv_oran");
        } else {

            $kdv_duzelt = "1.0" . $request->input("stok_kdv_oran");
        }


        if ($request->input("fiyatVergiDurum") == 1) {

            $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
            $stok_satis_fiyat = $request->input("stok_satis_fiyati");
            $stok_alis_fiyat = $request->input("stok_alis_fiyati");


            if ($request->input("stok_kdv_oran") > 0) {

                $stok_kdv_dahil_satis_fiyati = $stok_satis_fiyat * $kdv_duzelt;
            } else {

                $stok_kdv_dahil_satis_fiyati = $stok_satis_fiyat;
            }
        } else if ($request->input("fiyatVergiDurum") == 2) {


            $stok_kdv_dahil_satis_fiyati = $request->input("stok_satis_fiyati");


            if ($request->input("stok_kdv_oran") > 0) {


                $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
                $stok_satis_fiyat = $request->input("stok_satis_fiyati");
                $stok_alis_fiyat = $request->input("stok_alis_fiyati");


                if ($max_iskontolu_satis_fiyat > 0) {

                    $max_iskontolu_satis_fiyat = $max_iskontolu_satis_fiyat / $kdv_duzelt;
                }

                if ($stok_alis_fiyat > 0) {

                    $stok_alis_fiyat = $stok_alis_fiyat / $kdv_duzelt;
                }

                if ($stok_satis_fiyat > 0) {

                    $stok_satis_fiyat = $stok_satis_fiyat / $kdv_duzelt;
                }
            } else {

                $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
                $stok_satis_fiyat = $request->input("stok_satis_fiyati");
                $stok_alis_fiyat = $request->input("stok_alis_fiyati");
            }
        }

        $paket_stok = 0;

        if ($request->has("paketlenmisurun")) {

            $paket_stok = 1;
        }




        $url = $this->seo_url($request->input("stok_adi"));




        $stok_add = $this->table("stok", Controller::$userInfo)
            ->col("stok_barkod_no", $request->input("stok_barkod_no"))
            ->col("stok_kod", $request->input("stok_kod"))
            ->col("stok_ozel_kod", $request->input("stok_ozel_kod"))
            ->col("stok_adi", $request->input("stok_adi"))
            ->col("stok_cinsi", $request->input("stok_cinsi"))
            ->col("stok_birimi", $request->input("stok_birimi"))
            ->col("stok_sinif", $request->input("stok_sinif"))
            ->col("stok_grup", $request->input("stok_grup"))
            ->col("stok_min_seviyesi", $request->input("stok_min_seviyesi"))
            ->col("stok_max_seviyesi", $request->input("stok_max_seviyesi"))
            ->col("stok_alis_fiyati", $stok_alis_fiyat)
            ->col("stok_satis_fiyati", $stok_satis_fiyat)
            ->col("stok_max_iskontolu_satis_fiyati", $max_iskontolu_satis_fiyat)
            ->col("stok_kdv_dahil_satis_fiyati", $stok_kdv_dahil_satis_fiyati)
            ->col("stok_kdv_oran", $request->input("stok_kdv_oran"))
            ->col("stok_detayi", $request->input("stok_detayi"))
            ->col("stok_resim", $resim_adi)
            ->col("last_val", $last_id)
            ->col("stok_create_id", uniqid())
            ->col("stok_alim_doviz", $request->input("stok_alim_doviz"))
            ->col("stok_fiyat_vergi_durum", $request->input("fiyatVergiDurum"))
            ->col("stok_standart_adet", $request->input("stok_standart_adet"))
            ->col("stok_doviz", $request->input("stok_doviz"))
            ->col("stok_alim_iskonto_oran", $request->input("stok_alim_iskonto_oran"))
            ->col("stok_satis_iskonto_oran", $request->input("stok_satis_iskonto_oran"))
            ->col("stok_marka_id", $request->input("stok_marka"))
            ->col("stok_parent_id", $request->input("stok_parent_id") == NULL ? 0 : $request->input("stok_parent_id"))
            ->col("stok_parent_stok_kod", $request->input("stok_parent_stok_kod") == NULL ? "" : $request->input("stok_parent_stok_kod"))
            ->col("stok_varyant_adi", $request->input("stok_varyant_adi") == NULL ? "" : $request->input("stok_varyant_adi"))
            ->col("stok_varyant_deger", $request->input("stok_varyant_deger") == NULL ? "" : $request->input("stok_varyant_deger"))
            ->col("paket_stok", $paket_stok)
            ->col("stok_seo_url", $this->seo_url($url))
            ->save_();

        if ($stok_add) {

            if ($request->has("grupstoklari")) {

                $gruplar = $request->input("grupstoklari");

                $sql = "";

                $owner_id = Controller::$userInfo["owner_id"];
                $user_id = Controller::$userInfo["id"];

                if (is_array($gruplar)) {


                    $cr_date = date("Y-m-d H:i:s");


                    foreach ($gruplar as $grup => $val) {

                        $sql .= "INSERT INTO stok_grup_stoklari SET stok_id = {$stok_add} ,
 grup_stok_id = {$grup} , 
 miktar = {$val} , 
 owner_id = {$owner_id} , 
 created_date = '{$cr_date}' , created_user = {$user_id} ;";
                    }

                    if ($sql != "") {

                        $sg_inster = $this->getConnection()->prepare($sql);
                        $sg_inster->execute();
                        $sg_inster->closeCursor();

                    }

                }

            }

        }


        $tags = $request->input("etiketler");

        if (is_array($tags)) {

            $etiketler = $this->reset()->table("stok_etiketler", Controller::$userInfo);

            foreach ($tags as $key => $tag) {

                $etiketler->col("tag_id", $tag)->col("stok_id", $stok_add)->save_();
            }
        }


        if ($request->input("stok_parent_id") != NULL) {

            $queryseourl = $this->getConnection()->prepare("SELECT   
stok.* , 
stok.stok_parent_id ,
stok.id , 
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,
stok.stok_parent_id , 
IF(lst.id IS NULL ,stok.stok_seo_url,lst.stok_seo_url) AS stok_url , 
stok.id as st_id 
 
FROM 
 
 stok  
 
 LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id 

 WHERE 
 
 stok.id = ?
");
            $queryseourl->execute([$stok_add]);

            $stok_data = $queryseourl->fetch();

            if ($stok_data) {


                if ($stok_data["stok_parent_id"] > 0) {

                    $stok_seo_url = $stok_data["stok_url"];

                    $stok_varyant_adi = $stok_data["stok_varyant_adi"];

                    $stok_varyant_deger = $stok_data["stok_varyant_deger"];

                    $url = $stok_seo_url . "-" . $this->seo_url($stok_varyant_adi) . "-" . $this->seo_url($stok_varyant_deger);


                    $this->getConnection()->prepare("UPDATE  stok SET stok_seo_url = ? WHERE id = ? ")->execute([

                        $url,

                        $stok_data["id"]]);
                }


            }


        }

        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);

        return $stok_add;
    }

    public function updateStok($request)
    {

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"];

        $last_id++;

        $session = new Session();

        $account_no = $session->get("account_no");


        if ($request->input("stok_kdv_oran") > 9) {

            $kdv_duzelt = "1." . $request->input("stok_kdv_oran");
        } else {

            $kdv_duzelt = "1.0" . $request->input("stok_kdv_oran");
        }


        $paket_stok = 0;

        if ($request->has("paketlenmisurun")) {

            $paket_stok = 1;
        }


        if ($request->input("fiyatVergiDurum") == 1) {

            $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
            $stok_satis_fiyat = $request->input("stok_satis_fiyati");
            $stok_alis_fiyat = $request->input("stok_alis_fiyati");


            if ($request->input("stok_kdv_oran") > 0) {

                $stok_kdv_dahil_satis_fiyati = $stok_satis_fiyat * $kdv_duzelt;
            } else {

                $stok_kdv_dahil_satis_fiyati = $stok_satis_fiyat;
            }
        } else if ($request->input("fiyatVergiDurum") == 2) {

            $stok_kdv_dahil_satis_fiyati = $request->input("stok_satis_fiyati");


            if ($request->input("stok_kdv_oran") > 0) {


                $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
                $stok_satis_fiyat = $request->input("stok_satis_fiyati");
                $stok_alis_fiyat = $request->input("stok_alis_fiyati");


                if ($max_iskontolu_satis_fiyat > 0) {

                    $max_iskontolu_satis_fiyat = $max_iskontolu_satis_fiyat / $kdv_duzelt;
                }

                if ($stok_alis_fiyat > 0) {

                    $stok_alis_fiyat = $stok_alis_fiyat / $kdv_duzelt;
                }

                if ($stok_satis_fiyat > 0) {

                    $stok_satis_fiyat = $stok_satis_fiyat / $kdv_duzelt;
                }
            } else {

                $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
                $stok_satis_fiyat = $request->input("stok_satis_fiyati");
                $stok_alis_fiyat = $request->input("stok_alis_fiyati");
            }
        }


        $stok_update = $this->table("stok", Controller::$userInfo)
            ->find($request->input("stok_id"))
            ->col("stok_barkod_no", "{$request->input("stok_barkod_no")}", "string")
            ->col("stok_kod", $request->input("stok_kod"))
            ->col("stok_ozel_kod", $request->input("stok_ozel_kod"))
            ->col("stok_adi", $request->input("stok_adi"))
            ->col("stok_cinsi", $request->input("stok_cinsi"))
            ->col("stok_birimi", $request->input("stok_birimi"))
            ->col("stok_sinif", $request->input("stok_sinif"))
            ->col("stok_grup", $request->input("stok_grup"))
            ->col("stok_min_seviyesi", $request->input("stok_min_seviyesi"))
            ->col("stok_max_seviyesi", $request->input("stok_max_seviyesi"))
            ->col("stok_alis_fiyati", $stok_alis_fiyat)
            ->col("stok_satis_fiyati", $stok_satis_fiyat)
            ->col("stok_kdv_dahil_satis_fiyati", $stok_kdv_dahil_satis_fiyati)
            ->col("stok_max_iskontolu_satis_fiyati", $max_iskontolu_satis_fiyat)
            ->col("stok_kdv_oran", $request->input("stok_kdv_oran"))
            ->col("stok_detayi", $request->input("stok_detayi"))
            ->col("stok_resim", $request->input("stok_resim"))
            ->col("stok_resim2", $request->input("stok_resim2"))
            ->col("stok_resim3", $request->input("stok_resim3"))
            ->col("stok_resim4", $request->input("stok_resim4"))
            ->col("last_val", $last_id)
            ->col("stok_fiyat_vergi_durum", $request->input("fiyatVergiDurum"))
            ->col("stok_standart_adet", $request->input("stok_standart_adet"))
            ->col("stok_doviz", $request->input("stok_doviz"))
            ->col("stok_alim_iskonto_oran", $request->input("stok_alim_iskonto_oran"))
            ->col("stok_satis_iskonto_oran", $request->input("stok_satis_iskonto_oran"))
            ->col("stok_marka_id", $request->input("stok_marka"))
            ->col("stok_alim_doviz", $request->input("stok_alim_doviz"))
            ->col("stok_parent_id", $request->input("stok_parent_id") == NULL ? 0 : $request->input("stok_parent_id"))
            ->col("stok_parent_stok_kod", $request->input("stok_parent_stok_kod") == NULL ? "" : $request->input("stok_parent_stok_kod"))
            ->col("stok_varyant_adi", $request->input("stok_varyant_adi") == NULL ? "" : $request->input("stok_varyant_adi"))
            ->col("stok_varyant_deger", $request->input("stok_varyant_deger") == NULL ? "" : $request->input("stok_varyant_deger"))
            ->col("paket_stok", $paket_stok)
            ->col("stok_web_title", $request->input("stok_web_title"))
            ->col("stok_web_description", $request->input("stok_web_description"))
            ->update_();


        if ($stok_update) {

            $stgruplar_table_query = $this->getConnection()->prepare("UPDATE stok_grup_stoklari SET remove = 1 WHERE stok_id = ? and owner_id = ? ");

            $stgruplar_table_query->execute([$request->input("stok_id"), Controller::$userInfo["owner_id"]]);


            if ($request->has("grupstoklari")) {

                $gruplar = $request->input("grupstoklari");

                $sql = "";

                $owner_id = Controller::$userInfo["owner_id"];
                $user_id = Controller::$userInfo["id"];
                $stok_id = $request->input("stok_id");

                if (is_array($gruplar)) {


                    $cr_date = date("Y-m-d H:i:s");


                    foreach ($gruplar as $grup => $val) {

                        $sql .= "INSERT INTO stok_grup_stoklari SET stok_id = {$stok_id} ,
 grup_stok_id = {$grup} , 
 miktar = {$val} , 
 owner_id = {$owner_id} , 
 created_date = '{$cr_date}' , created_user = {$user_id} ;";
                    }

                    if ($sql != "") {

                        $sg_inster = $this->getConnection()->prepare($sql);
                        $sg_inster->execute();
                        $sg_inster->closeCursor();

                    }

                }

            }

        }


        $etiketler_table_query = $this->getConnection()->prepare("UPDATE stok_etiketler SET remove = 1 WHERE stok_id = ? and owner_id = ? ");

        $etiketler_table_query->execute([$request->input("stok_id"), Controller::$userInfo["owner_id"]]);

        $etiketler_table = $this->reset()->table("stok_etiketler", Controller::$userInfo);

        $tags = $request->input("etiketler");

        if (is_array($tags)) {

            foreach ($tags as $key => $tag) {

                $etiketler_table->col("tag_id", $tag)->col("stok_id", $request->input("stok_id"))->save_();
            }
        }

        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);


        return $stok_update;
    }

    public function updateStokPrice($request)
    {

        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare(""
            . "SELECT last_id "
            . "FROM stok_change_listener "
            . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        $q_last_id = $query->fetch();

        $last_id = $q_last_id["last_id"];

        $last_id++;

        $session = new Session();

        $account_no = $session->get("account_no");


        if ($request->input("stok_kdv_oran") > 9) {

            $kdv_duzelt = "1." . $request->input("stok_kdv_oran");
        } else {

            $kdv_duzelt = "1.0" . $request->input("stok_kdv_oran");
        }


        if ($request->input("fiyatVergiDurum") == 1) {

            $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
            $stok_satis_fiyat = $request->input("stok_satis_fiyati");
            $stok_alis_fiyat = $request->input("stok_alis_fiyati");


            if ($request->input("stok_kdv_oran") > 0) {

                $stok_kdv_dahil_satis_fiyati = $stok_satis_fiyat * $kdv_duzelt;
            } else {

                $stok_kdv_dahil_satis_fiyati = $stok_satis_fiyat;
            }
        } else if ($request->input("fiyatVergiDurum") == 2) {

            $stok_kdv_dahil_satis_fiyati = $request->input("stok_satis_fiyati");


            if ($request->input("stok_kdv_oran") > 0) {


                $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
                $stok_satis_fiyat = $request->input("stok_satis_fiyati");
                $stok_alis_fiyat = $request->input("stok_alis_fiyati");


                if ($max_iskontolu_satis_fiyat > 0) {

                    $max_iskontolu_satis_fiyat = $max_iskontolu_satis_fiyat / $kdv_duzelt;
                }

                if ($stok_alis_fiyat > 0) {

                    $stok_alis_fiyat = $stok_alis_fiyat / $kdv_duzelt;
                }

                if ($stok_satis_fiyat > 0) {

                    $stok_satis_fiyat = $stok_satis_fiyat / $kdv_duzelt;
                }
            } else {

                $max_iskontolu_satis_fiyat = $request->input("stok_max_iskontolu_satis_fiyati");
                $stok_satis_fiyat = $request->input("stok_satis_fiyati");
                $stok_alis_fiyat = $request->input("stok_alis_fiyati");
            }
        }


        $stok_update = $this->table("stok", Controller::$userInfo)
            ->find($request->input("stok_id"))
            ->col("stok_min_seviyesi", $request->input("stok_min_seviyesi"))
            ->col("stok_max_seviyesi", $request->input("stok_max_seviyesi"))
            ->col("stok_alis_fiyati", $stok_alis_fiyat)
            ->col("stok_satis_fiyati", $stok_satis_fiyat)
            ->col("stok_kdv_dahil_satis_fiyati", $stok_kdv_dahil_satis_fiyati)
            ->col("stok_max_iskontolu_satis_fiyati", $max_iskontolu_satis_fiyat)
            ->col("stok_kdv_oran", $request->input("stok_kdv_oran"))
            ->col("last_val", $last_id)
            ->col("stok_fiyat_vergi_durum", $request->input("fiyatVergiDurum"))
            ->col("stok_standart_adet", $request->input("stok_standart_adet"))
            ->update_();


        $updateQuery = $this->getConnection()->prepare("UPDATE stok_change_listener SET last_id = ? WHERE owner_id = ? ")->execute([$last_id, $owner_id]);

        return $stok_update;
    }

    public function stokArama($str, $mevcutta = 0)
    {


        $mvsql = "";
        if ($mevcutta > 0) {

            $mvsql = " and stok.stok_adet > 0 ";
        }

        $sql = "SELECT "
            . Controller::helper(null, "stokModelHelper")->getStokVaryantSelect(true) . " "
            . "FROM "
            . "stok  LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  "
            . "WHERE "
            . "stok.remove = 0 and "
            . "stok.owner_id = :owner and "
            . "(stok.stok_barkod_no = :barkod OR stok.stok_kod = :kod OR stok.stok_adi LIKE :str ) {$mvsql} LIMIT 20";

        $query = $this->getConnection()->prepare($sql);
        $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
        $query->BindValue(":str", "%" . $str . "%", PDO::PARAM_STR);
        $query->BindParam(":barkod", $str, PDO::PARAM_STR);
        $query->BindParam(":kod", $str, PDO::PARAM_STR);
        $query->execute();


        if ($query->rowCount() > 0) {

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {

            return false;
        }
    }

    public function stokBarkodKontrol($barkod)
    {

        $sql = "SELECT id FROM stok WHERE stok_barkod_no = :barkod and remove = 0 and owner_id = :owner";
        $query = $this->getConnection()->prepare($sql);
        $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
        $query->BindParam(":barkod", $barkod, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {

            return false;
        }
    }

    public function stokGuncellemeBarkodKontrol($barkod, $id)
    {

        $sql = "SELECT id FROM stok WHERE stok_barkod_no = :barkod and owner_id = :owner and id != :id";
        $query = $this->getConnection()->prepare($sql);
        $query->BindParam(":owner", Controller::$userInfo['owner_id'], PDO::PARAM_STR);
        $query->BindParam(":barkod", $barkod, PDO::PARAM_STR);
        $query->BindParam(":id", $id, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {

            return false;
        }
    }


    public function updateWebStatus($id, $status)
    {
        return $this->getConnection()->prepare("UPDATE stok SET web_status = ? WHERE id = ? ")->execute([$status, $id]);

    }

}
