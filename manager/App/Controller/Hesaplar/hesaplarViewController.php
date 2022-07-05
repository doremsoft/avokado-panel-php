<?php

namespace App\Controller\Hesaplar;
use PDO;
use PDOException;
use stdClass;
use \Dipa\Db\Dbsync;
use \Dipa\App;

class hesaplarViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function list() {

        $model = $this->model("hesaplar", "hesaplarModel");


        $hesaplar = $model->getList();


        return $this->view("hesaplar/list", ['hesaplar' => $hesaplar]);
    }

    public function add() {



        return $this->view("hesaplar/add");
    }

    public function edit($hesap_id) {

        $model = $this->model("hesaplar", "hesaplarModel");


        $hesap = $model->getAccount($hesap_id);


        return $this->view("hesaplar/edit", ['hesap' => $hesap]);
    }

    public function update() {

        $model = $this->model("hesaplar", "hesaplarModel");


        $hesap_update = $model->update(self::$http_request);


        if ($hesap_update) {

            $this->header->result("success", "Hesap Bilgileri Güncellendi...")->to("hesaplar/list");
        } else {

            $this->header->result("fail", "Hesap Bilgileri Güncellenemedi! ")->back();
        }
    }

    public function append() {

        $model = $this->model("hesaplar", "hesaplarModel");

        if ($model->mailKontrol($this->request->input("mail"))) {

            $this->header->result("fail", "Bu Mail Sistem Üzerinde Başka Bir Hesap Tarafından Kullanılmakta!")->back();
            
        } else {

            $acid = $model->lastAccountId();

            if ($acid["account_no"] == null) {

                $acid["account_no"] = 100;
            }

            $account_id = $acid["account_no"] + 1;

            $mail_code = md5(uniqid());

            $result = $model->register($this->request, $account_id, $mail_code);
            
            

            if ($result) {

                $sifre = rand(1000, 9999);

                $mail_data = $model->getMailHashData($this->request->input("mail"));

                $mailadresi = $mail_data["mail"];

                $hesapno = $mail_data["account_no"];

                $register_id = $mail_data["id"];

                $register_ok = $model->activateAccount($this->request, $register_id, $sifre);


                if ($register_ok) {
                    
                    $request = $this->request;
                    

                     $this->updateOk("avok_arge", $request->input("server_ip"), $request->input("db_name"));

               
                    try {

                            $database_connection = new \PDO('mysql:host=localhost;dbname=' . $request->input("db_name") . '', App::getConfig("db", "username"), App::getConfig("db", "password"), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

                            $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            $userInsertresult = $model->userInsert($database_connection, $request, $sifre,$hesapno,$this->request->input("account_type"));

                            if($userInsertresult){


                                $model->sendMail($hesapno,$mailadresi,$sifre);
                                
                                
                                  $this->header->result("success", "Hesap Oluşturuldu")->to("hesaplar/list");
                                
                                
                            }else{
                                  $this->header->result("fail", "Kullanıcı Oluşturulamadı ")->back();
                                
                            }
                            
                            
                        } catch (Exception $ex) {

                            $this->header->result("fail", "Hesap Oluşturuldu Veritabanı Oluşturulamadı!")->back();
                        }
                        
                 

                } else {

                    $this->header->result("fail", "Hesap Aktif Edilemedi! Kayıt id :".$register_id)->back();
                }
            } else {
                $this->header->result("fail", "(Register)Kayıt Başarısız Oldu!")->back();
            }
        }
    }

    public function input_kontrol($input, $type = null) {

        $inp = trim($input);

        if ($inp != "" && !empty($inp)) {


            if ($type == null) {

                return true;
            } else if ($type == "mail") {

                if (filter_var($inp, FILTER_VALIDATE_EMAIL)) {
                    return true;
                } else {
                    return false;
                }
            } else if ($type == "tel") {

                return $this->telefon($inp);
            }
        } else {
            return false;
        }
    }

    public function telefon($text) {
        $text = preg_replace("/[^0-9]/", "", $text);
        $first = substr("$text", 0, 1);
        if ($first == "0") {
            $text = substr($text, 1);
        }

        $doksan = substr("$text", 0, 2);
        if ($doksan != "90") {
            //$new_telefon = "Gecersiz: Ulke kodu TR degil.";

            return false;
        } else {
            $numara = substr($text, 2);
            if (substr("$numara", 0, 1) == "0") {
                $numara = substr($numara, 1);
            }

            if (strlen($numara) != "10") {
                //$new_telefon = "Gecersiz: TR telefon formatina uygun degil (901112223344)";

                return false;
            } else {
                $new_telefon = "+$doksan$numara";
            }
        }

        return true;
    }
    
        private function updateOk($kaynak_db, $hedef_db_host,$hedef_db_name ) {


        $db1 = [
            'host' => 'localhost',
            'dbname' => $kaynak_db,
            'username' => App::getConfig("db", "username"),
            'password' => App::getConfig("db", "password")
        ];

        $db2 = [
            'host' => 'localhost',
            'dbname' => $hedef_db_name,
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

}
