<?php

namespace App\Controller\Database;

use PDO;
use PDOException;
use stdClass;
use \Dipa\Db\Dbsync;
use \Dipa\App;

class dbActionController extends \Dipa\Controller {

    private $last_sql;

    public function __construct() {
        parent::__construct(true);
    }

    public function updatePrepare($account_id) {

        $sql = "Hesap Bulunamadı!";

        $control_result = 0;

        $model = $this->model("hesaplar", "hesaplarModel");

        $hesap = $model->getAccount($account_id);

        $kaynak_db = "avok_arge";

        if ($hesap) {

            $db1 = [
                'host' => 'localhost',
                'dbname' => $kaynak_db,
                'username' => App::getConfig("db", "username"),
                'password' => App::getConfig("db", "password")
            ];

            $db2 = [
                'host' => 'localhost',
                'dbname' => $hesap["db_name"],
                'username' => App::getConfig("db", "username"),
                'password' => App::getConfig("db", "password")
            ];


            $sync = new Dbsync();

            $sync->init($db1, $db2, false, true, true);

            $control_result = $sync->compare();

            $sql = $sync->printSql();
        }



        return $this->view("db/update-prepare", [
                    'account_id' => $account_id,
                    'kaynak_db' => $kaynak_db,
                    'hedef_db' => $hesap["db_name"],
                    'sql' => $sql,
                    'control_result' => $control_result
        ]);
    }

    public function update() {
        $sync_result = 0;

        $control_result = 0;

        $model = $this->model("hesaplar", "hesaplarModel");

        $account_id = $this->request->input("accound_id");

        $hesap = $model->getAccount($account_id);

        $kaynak_db = "avok_arge";

        if ($hesap) {

            $result = $this->updateOk($kaynak_db, $hesap["db_name"]);

            if ($result) {

                $this->header->result("success", "Db Güncellendi...")->to("hesaplar/list");
            } else {

                $this->header->result("fail", "Db Güncellenemedi! :" . $this->last_sql)->back();
            }
        } else {

            $sync_result = 0;
        }
    }

    public function allDbUpdate(){

        set_time_limit(0);

        $kaynak_db = "avok_arge";


        $hesaplarmodel = $this->model("hesaplar","hesaplarModel");

        $server_name = $this->request->input("server");

        $hesaplar = $hesaplarmodel->getListFromServer($server_name);

        if($hesaplar){


            $complate = false;



            foreach ($hesaplar as $key => $val){


                $result = $this->updateOk($kaynak_db, $val["db_name"]);

                if($result){
                    $complate = true;
                }

            }



            if ($complate) {

               echo 1;
            } else {

                echo 0;
            }




        }else{

            echo 0;

        }



}

    public function argeUpdate() {

        set_time_limit(0);


        $qr = false;

        $dbConfig = [
            'lib' => 'pdo',
            'host' => 'localhost',
            'driver' => 'mysql',
            'database' => 'avok_arge',
            'username' => App::getConfig("db", "username"),
            'password' => App::getConfig("db", "password"),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => ''
        ];

        $sql = file_get_contents(SYSTEM_DIR . DS . 'sql' . DS . 'arge.sql');


        try {
            $database_connection = new PDO(
                    'mysql:host=' . $dbConfig["host"] . ';dbname=' .
                    $dbConfig["database"] . '', $dbConfig["username"], $dbConfig["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $qr = $database_connection->exec($sql);
        } catch (PDOException $e) {

            echo "Veritabanı Bağantı Hatası!:" . $e->getMessage();
        }



        if ($qr) {

            $this->header->result("success", "Arge Db Güncellendi...")->to("hesaplar/list");
        } else {

            $this->header->result("fail", "Arge Db Güncellenemedi! ")->back();
        }
    }

    private function updateOk($kaynak_db, $hedef_db) {


        $db1 = [
            'host' => 'localhost',
            'dbname' => $kaynak_db,
            'username' => App::getConfig("db", "username"),
            'password' => App::getConfig("db", "password")
        ];

        $db2 = [
            'host' => 'localhost',
            'dbname' => $hedef_db,
            'username' => App::getConfig("db", "username"),
            'password' => App::getConfig("db", "password")
        ];


        $sync = new Dbsync();

        $sync->init($db1, $db2, false, true, true);

        $control_result = $sync->compare();

        if ($control_result) {

            $sync_result = $sync->execute();
        } else {

            $this->last_sql = $sync->getLastSql();

            $sync_result = false;
        }

        return $sync_result;
    }

    public function getStocks($account_id) {

        set_time_limit(0);

        $model = $this->model("hesaplar", "hesaplarModel");

        $hesap = $model->getAccount($account_id);

        $archive_db = [
            'host' => 'localhost',
            'dbname' => App::getConfig("db", "masterDbName"),
            'username' => App::getConfig("db", "username"),
            'password' => App::getConfig("db", "password")
        ];

        $source_db = [
            'host' => 'localhost',
            'dbname' => $hesap["db_name"],
            'username' => App::getConfig("db", "username"),
            'password' => App::getConfig("db", "password")
        ];

        $stokArchineHelper = $this->helper("", "stockArchive");

        $stokArchineHelper->setArchive($archive_db);

        $status = $stokArchineHelper->getSourceStocks($source_db,$hesap["account_no"]);

        if ($status == 0) {

            $this->header->result("fail", "Alınacak Stok Bulunamadı!")->back();
        } else if ($status == 1) {

            if ($stokArchineHelper->getInsertCount() == 0 && $stokArchineHelper->getSelectStockCount() > 0) {
                $this->header->result("fail", "Kayıt Başarısız Oldu! Adet:" . $stokArchineHelper->getInsertCount() . " Bulunnan:" . $stokArchineHelper->getSelectStockCount())->back();
            } else {
                $this->header->result("success", "Stoklar Transfer Edildi Adet:" . $stokArchineHelper->getInsertCount() . " Bulunnan:" . $stokArchineHelper->getSelectStockCount())->to("hesaplar/list");
            }
        } else if ($status == 2) {

            $this->header->result("fail", "Kayıt Esnasından Hata İle Karşılaşıldı")->back();
        }
 
    }

}
