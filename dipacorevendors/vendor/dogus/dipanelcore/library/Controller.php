<?php

namespace Dipa;

use Dipa\Support\Curl;
use PDO;

class Controller
{

    public $templateName = null;
    public $result = null;
    public $authSystem;
    public static $system_name = "";
    public static $userInfo = NULL;
    public $request = NULL;
    public static $http_request = NULL;
    public $header = NULL;
    public static $account_no = NULL;
    public static $account_hash = NULL;
    public static $account_details = NULL;
    public static $user_auth_list = [];
    public static $account_packets = [];
    public static $abonelik_paketi;
    public static $redirect_url = [

        "depolama" => "hesap-paketleri/i/depolama",
        "web" => "hesap-paketleri/i/web",
        "hesap" => "hesap-paketleri/i/hesap",
        "barkod" => "hesap-paketleri/i/barkod",
        "adisyon" => "hesap-paketleri/i/adisyon"

    ];

    public function __construct($authControl = FALSE, $authority = FALSE, $redirectUrl = "login", $die = FALSE , $terminal = false)
    {
        if (App::getConfig("activeTemplate")) {
            $this->templateName = App::getConfig("activeTemplate");
        }

        self::$system_name = App::getConfig("system_name");


        if($terminal == false){

            if ($authControl) {

                $this->auth($authority, $die);

            }

            $this->header = new Http\Header();

            if ($_POST || $_GET) {

                $this->request = new Http\Request();

                self::$http_request = $this->request;

                if ($this->request->has("restotype")) {

                    $this->header->result(
                        base64_decode($this->request->input("restotype")),
                        base64_decode($this->request->input("restomsg"))
                    );
                }
            }

        }


    }


    public function auth($authority, $die)
    {


        $auth = new \Dipa\Sys\Auth($authority, \Dipa\App::getConfig("main_login_url"), $die, \Dipa\App::getConfig("system_type"),self::$system_name);

        if (app::getConfig("auth", "login_with_account_number")) {

            $auth->control();
        } else {

            $auth->control(true);
        }

        $this->authSystem = true;

        self::$userInfo = $auth->getUser();

        if (isset(self::$userInfo["template"])) {

            if (self::$userInfo["template"] == 'default') {

                $this->templateName = app::getConfig("activeTemplate");
            } else {
                $this->templateName = self::$userInfo["template"];

                App::editConfig("activeTemplate", "", $this->templateName);
            }

        } else {
            $this->templateName = app::getConfig("activeTemplate");
        }

        self::$user_auth_list = unserialize(self::$userInfo["auths"]);

        self::$userInfo["real_ip"] = "";

        if (app::getConfig("auth", "login_with_account_number")) {

            self::$account_no = $auth->getAccountNo();

            self::$account_details = $auth->getAccountDetails();

            self::$account_hash = self::$account_details["secure_key"];

            $service_url = app::getConfig("manager_url");

            $detay_query = Curl::post("http://localhost/manager/hesap/paketler",[""=>""],self::$account_no , self::$account_hash);

 
            $hesap_detaylar = json_decode(
               $detay_query
               ,true);


           if(isset($hesap_detaylar["login"])){

               if($hesap_detaylar["login"] == 1){

                    $pack = $hesap_detaylar["paketler"];

                   foreach ($pack as $key => $val){

                     self::$account_packets[$val["paket_key"]] = $val;

                   }

               }else{

                   echo $this->view("error/manager_secure_error");

         

               }

           }



            if (self::$account_details["durum"] == 0) {

                echo $this->view("error/disable_account");

         
            }


            if (isset(self::$account_packets["ucretsiz"])) {
                self::$abonelik_paketi = "ucretsiz";
            } else if (isset(self::$account_packets["standart"])) {
                self::$abonelik_paketi = "standart";
            } else if (isset(self::$account_packets["gold"])) {
                self::$abonelik_paketi = "gold";
            } else if (isset(self::$account_packets["bronz"])) {
                self::$abonelik_paketi = "bronz";
            } else if (isset(self::$account_packets["platinium"])) {
                self::$abonelik_paketi = "platinium";
            } else {

            }

            self::$abonelik_paketi = "standart";

        }

        if (self::$userInfo["admin"] != 1) {

            if (self::$userInfo["web"] != 1) {

                echo $this->view("error/yetkisiz_islem");

                die();

            }
        }

    }


    public function getUserInfo()
    {
        return self::$userInfo;
    }

    public function view($viewFile, $params = [], $noCache = false, $token = "")
    {

        $params["mevcut_abonelik"] =  self::$abonelik_paketi;

        return \Dipa\View::render($viewFile, $params, $this->templateName, $noCache, $token, self::$userInfo, self::$account_no, self::$account_details);
    }

    public function model($mfolder = null, $model, $args = null)
    {

        $mfolder = $mfolder == null ? "" : "/" . $mfolder;

        $file = APP_DIR . DS . 'Model' . "{$mfolder}/{$model}.php";

        if (file_exists($file)) {

            require_once $file;

            if (class_exists($model)) {

                return $model == null ? new $model() : new $model($args);
            } else {
                exit("Model dosyasında sınıf tanımlı değil: $model");
            }
        } else {
            exit("Model dosyası bulunamadı: {$model}.php");
        }
    }

    public static function include_model($mfolder = null, $model, $args = null)
    {

        $mfolder = $mfolder == null ? "" : "/" . $mfolder;

        if (file_exists($file = APP_DIR . DS . 'Model' . "{$mfolder}/{$model}.php")) {

            require_once $file;

            if (class_exists($model)) {

                return $model == null ? new $model() : new $model($args);
            } else {
                exit("Model dosyasında sınıf tanımlı değil: $model");
            }
        } else {
            exit("Model dosyası bulunamadı: {$model}.php");
        }
    }

    public static function helper($hfolder = null, $helper, $args = null)
    {
        $hfolder = $hfolder == null ? "" : "/" . $hfolder;

        if (file_exists($helperFile = APP_DIR . DS . 'Helper' . "{$hfolder}/{$helper}.php")) {

            require_once $helperFile;

            if (class_exists($helper)) {

                return $args == null ? new $helper() : new $helper($args);

            } else {

                exit("Helper dosyasında sınıf tanımlı değil: $helper");
            }
        } else {
            exit("Helper dosyası bulunamadı: {$helper}.php");
        }
    }

    public function paket_kontrol($gerekli_paket_key, $yoksa_yonlendirilecek_url = null)
    {


        if (is_array($gerekli_paket_key)) {

            foreach ($gerekli_paket_key as $key => $val) {

                if (array_key_exists($val, self::$account_packets)) {

                    return self::$account_packets[$val];

                }


            }

            if ($yoksa_yonlendirilecek_url == null) {
                return false;
            } else {
                $this->header->to(self::$redirect_url[$yoksa_yonlendirilecek_url]);
                die();
            }


        } else {

            if (isset(self::$account_packets[$gerekli_paket_key])) {

                return self::$account_packets[$gerekli_paket_key];

            } else {

                if ($yoksa_yonlendirilecek_url == null) {
                    return false;
                } else {
                    $this->header->to(self::$redirect_url[$yoksa_yonlendirilecek_url]);
                    die();
                }


            }


        }


    }

    public function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }
        return $size;
    }


    public function getEmptyStorageSize(){
        $depolama_limit = 0;
        $kullanimdaki_depolama = 0;
        $kalan_limit = 0;


        $depolama = $this->paket_kontrol("depolama");


        if($depolama){

            $depolama_limit = $depolama["paket_tanimlama_1"];
        }



        $public_folder = MEDIA_DIR . DS . Controller::$account_no . DS . "s";

        $private_folder = STORAGE_PATH . DS . Controller::$account_no . DS . "media" . DS . "private";

        if (file_exists($private_folder)) {

            $kullanimdaki_depolama = $kullanimdaki_depolama + $this->folderSize($private_folder);

        }

        if (file_exists($public_folder)) {

            $kullanimdaki_depolama = $kullanimdaki_depolama + $this->folderSize($public_folder);

        }

        $kalan_limit = (int) $depolama_limit - $kullanimdaki_depolama;

        if($kalan_limit < 0){

            return 0;

        }else{

            return $kalan_limit;
        }

    }


    public function get_auth($auth, $child_auth = NULL, $die = true, $pack = [], $type = 'ticari')
    {

        if (self::$userInfo["admin"] == 1) {

            return true;
        } else {

            if ($child_auth == NULL) {

                if (isset(self::$user_auth_list[$auth])) {

                    return true;
                } else {

                    if ($die) {

                        echo $this->view("error/yetkisiz_islem");

                        die();
                    } else {

                        return false;
                    }
                }
            } else {


                if (isset(self::$user_auth_list[$auth][$child_auth])) {

                    return true;
                } else {
                    if ($die) {

                        echo $this->view("error/yetkisiz_islem");

                        die();
                    } else {

                        return false;
                    }
                }
            }
        }
    }

    function __destruct()
    {
        $this->templateName = null;
        $this->result = null;
    }

}
