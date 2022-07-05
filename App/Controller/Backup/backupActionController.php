<?php

namespace App\Controller\Backup;

class backupActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function removeDbBackup() {

        $model = $this->model("backup", "backupModel");


        $id = $this->request->input("id");


        $sonuc = $model->yedekSil($id);

        if ($sonuc) {



            \Dipa\Io\Log::write("Yedek Silindi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Yedek Silindi")->to("backup/list");
        } else {

            $this->header->result("fail", "Yedek Silinemedi!")->back();
        }
    }

    public function dbBackup() {

        set_time_limit(0);

        $model = $this->model("backup", "backupModel");

        $yedek_sayisi = $model->yedekSay();

        if ($yedek_sayisi < 5) {

            if ($this->request->input("yedek_adi") != NULL && $this->request->input("yedek_adi") != "" && trim($this->request->input("yedek_adi")) != "") {

                $yedek_adi = $model->dbBackup($this->request->input("yedek_adi"));

                if ($yedek_adi) {

                    $result = $model->yedekKaydet($this->request->input("yedek_adi"), $yedek_adi, "db");

                    \Dipa\Io\Log::write("Sistem Yedeklendi", self::$account_no, self::$userInfo["id"]);

                    $this->header->result("success", "Sistem Yedeklendi")->to("backup/list");
                } else {


                    \Dipa\Io\Log::write("Sistem Yedekleme Başarısız", self::$account_no, self::$userInfo["id"]);

                    $this->header->result("fail", "Yedekleme Başarısız Oldu!")->back();
                }


            } else {

                $this->header->result("fail", "Lütfen Yedek Adınızı Yazınız!")->back();
            }
        } else {

            $this->header->result("fail", "Yedek Alma Limiti Dolu!")->back();
        }
    }

    public function sqlImport() {


        set_time_limit(0);

        $model = $this->model("backup", "backupModel");


        $yedek_data = $model->getYedek($this->request->input("id"));


        if ($yedek_data["key_id"] == \Dipa\App::getConfig("key_id")) {

            $import = $model->importSqlBackup($yedek_data["yedek_dosyasi"]);

            if ($import) {

                \Dipa\Io\Log::write("Sistem Yedekten Yeniden Yüklendi", self::$account_no, self::$userInfo["id"]);

                $this->header->result("success", "Sistem Yedekten Yüklendi")->to("backup/list");
            } else {

                \Dipa\Io\Log::write("Sistem Yeniden Yükleme Başarısız oldu", self::$account_no, self::$userInfo["id"]);

                $this->header->result("fail", "Yükleme Başarısız Oldu!")->back();
            }
        } else {
            $this->header->result("fail", "Güvenlik Anahtarı Farklı Olduğundan Yedekten Yükleme Başarısız Oldu! Yetkili İle Görüşünüz!")->back();
        }
    }

}
