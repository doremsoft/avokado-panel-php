<?php

namespace App\Controller\Import;

class excelActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function stokUpload() {


        if (isset($_FILES["stoklar"])) {


            $newname = md5(uniqid());

            $fileClass = new \Dipa\Io\File();

            $upload_path = "temp/";

            $result = $fileClass->upload(
                    $_FILES["stoklar"],
                    $upload_path, [
                'ex' => ['xls', 'xlsx'],
                'max-size' => 3000000
                    ],
                    $newname
            );


            if ($result) {

                $model = $this->model("import", "excelModel");

                $info = $fileClass->get_info();

                $file_path = $info["full_path"];

                $ext = $fileClass->get_ext();

                $fileClass = NULL;

                $dbResult = $model->stokUpload($ext, $file_path, self::$http_request);

                if ($dbResult) {

                    \Dipa\Io\Log::write("Stok listesi excelden aktarıldı", self::$account_no, self::$userInfo["id"]);


                    $this->header->result("success", "Stok Listes Excelden Aktarıldı")->to("stok/list");
                } else {
                    \Dipa\Io\Log::write("stok listesi excelden aktarılamadı", self::$account_no, self::$userInfo["id"]);
                    $this->header->result("fail", "Veritabanına Aktarım Sağlanamadı!")->back();
                }
            } else {

                $error = "Hata:<br>";

                foreach ($fileClass->get_errors() as $key => $value) {

                    $error .= $value . "<br>";
                }
                $this->header->result("fail", $error)->back();
            }
        } else {
            $this->header->result("fail", "Dosya Seçmelisiniz!")->back();
        }
    }

    public function stokUpdateUpload() {


        if (isset($_FILES["stoklar"])) {


            $newname = md5(uniqid());

            $fileClass = new \Dipa\Io\File();

            $upload_path = "temp/";

            $result = $fileClass->upload(
                    $_FILES["stoklar"],
                    $upload_path, [
                'ex' => ['xls', 'xlsx'],
                'max-size' => 3000000
                    ],
                    $newname
            );


            if ($result) {

                $model = $this->model("import", "excelModel");

                $info = $fileClass->get_info();

                $file_path = $info["full_path"];

                $ext = $fileClass->get_ext();

                $fileClass = NULL;

                $dbResult = $model->stokUpdateUpload($ext, $file_path, self::$http_request);

                if ($dbResult) {

                    \Dipa\Io\Log::write("Stoklar excelden güncellendi", self::$account_no, self::$userInfo["id"]);

                    $this->header->result("success", "Stoklar Excelden Güncellendi")->to("excel-import/stok-guncelleme-index");
                } else {

                    \Dipa\Io\Log::write("Stoklar excelden güncellenemedi", self::$account_no, self::$userInfo["id"]);
                    $this->header->result("fail", "Veritabanına Aktarım Sağlanamadı!")->back();
                }
            } else {

                $error = "Hata:<br>";

                foreach ($fileClass->get_errors() as $key => $value) {

                    $error .= $value . "<br>";
                }

                $this->header->result("fail", $error)->back();
            }
        } else {
            $this->header->result("fail", "Dosya Seçmelisiniz!")->back();
        }
    }



    public function stokAdetlerGuncelle() {


        if (isset($_FILES["stoklar"])) {

            $newname = md5(uniqid());

            $fileClass = new \Dipa\Io\File();

            $upload_path = "temp/";

            $result = $fileClass->upload(
                $_FILES["stoklar"],
                $upload_path, [
                'ex' => ['xls', 'xlsx'],
                'max-size' => 3000000
            ],
                $newname
            );


            if ($result) {

                $model = $this->model("import", "excelModel");

                $info = $fileClass->get_info();

                $file_path = $info["full_path"];

                $ext = $fileClass->get_ext();

                $fileClass = NULL;

                $dbResult = $model->stokAdetGuncelle($ext, $file_path, self::$http_request);

                if ($dbResult) {


                  $this->header->result("success", "Stoklar Excelden Güncellendi")->to("excel-import/stok-adetler-upload");

                } else {


                    $this->header->result("fail", "Veritabanına Aktarım Sağlanamadı!")->back();
                }
            } else {

                $error = "Hata:<br>";

                foreach ($fileClass->get_errors() as $key => $value) {

                    $error .= $value . "<br>";
                }

                $this->header->result("fail", $error)->back();
            }
        } else {
            $this->header->result("fail", "Dosya Seçmelisiniz!")->back();
        }
    }

}
