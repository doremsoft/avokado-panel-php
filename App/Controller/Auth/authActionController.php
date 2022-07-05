<?php

namespace App\Controller\Auth;

use \Dipa\Support\Hash;
use \Dipa\Http\Header;
use \Dipa\Http\Request;
use \Dipa\Sys\Auth;
use \Dipa\Sys\Cryptor;


class authActionController extends \Dipa\Controller {


    public function loginAttack() {

        $header = new Header();

        $request = new Request();

        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {

            \Dipa\App::editConfig("db", "database", \Dipa\App::getConfig("db", "masterDbName"));
        }

        $authmodel = $this->model("auth", "authActionModel");

        if (trim($request->input("email")) == "") {

            $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Bütün Bilgileri Lütfen Doldurunuz.");
        } else {

            $masterquery = $authmodel->getConnection()->prepare("SELECT * FROM account_data WHERE account_no = ? and remove = ?");

            $masterquery->execute([$request->input("account_no"), 0]);

            $account_data = $masterquery->fetch();

            if ($account_data) {

                if ($account_data["server_ip"] == "localhost") {

                    if ($user = $authmodel->isUser($request->input("email"), Hash::generate("password", $request->input("password")), $request->input("account_no"))) {

                        $auth = new Auth();

                        if ($auth->login($user, $request->input("account_no"), \Dipa\App::getConfig("system_type") , self::$system_name)) {

                            if ($authmodel->lastIdControl($user["owner_id"])) {

                                if ($request->input("remember") == "on") {

                                    setcookie("r_remember", "ok", time() + (60 * 60 * 24), '/');
                                    setcookie("r_mail", $request->input("email"), time() + (60 * 60 * 24), '/');
                                    setcookie("r_account", $request->input("account_no"), time() + (60 * 60 * 24), '/');
                                } else {

                                    setcookie("r_remember", "non", time() + (60 * 60 * 24), '/');
                                    setcookie("r_mail", "", time() + (60 * 60 * 24), '/');
                                    setcookie("r_account", "", time() + (60 * 60 * 24), '/');
                                }


                                $image_folder = MEDIA_DIR . "/" .  $request->input("account_no");
                                $image_s_folder = MEDIA_DIR . "/" .  $request->input("account_no") . "/s";
                                $image_t_folder = MEDIA_DIR . "/" .  $request->input("account_no") . "/t";


                                $private_folder = STORAGE_PATH . "/" .  $request->input("account_no")."/media/private";
                                $private_system_folder = STORAGE_PATH . "/" .  $request->input("account_no")."/media/sistem";

                                if (!file_exists($private_folder)) {

                                    mkdir($private_folder,0777,true);

                                    chmod($private_folder, 0777);
                                }

                                if (!file_exists($private_system_folder)) {

                                    mkdir($private_system_folder,0777,true);

                                    chmod($private_system_folder, 0777);
                                }


                                if (!file_exists($image_folder)) {

                                    mkdir($image_folder,0777,true);

                                    chmod($image_folder, 0777);
                                }


                                if (!file_exists($image_s_folder)) {

                                    mkdir($image_s_folder,0777,true);

                                    chmod($image_s_folder, 0777);
                                }


                                if (!file_exists($image_t_folder)) {

                                    mkdir($image_t_folder,0777,true);

                                    chmod($image_t_folder, 0777);
                                }




                                $doviz_model = $this->model("doviz", "dovizModel");

                                $doviz_model->kurlariGuncelle(true, $user);
                                
                                \Dipa\Io\Log::write("Oturum Açıldı",$request->input("account_no"),$request->input("email"));

                                $header->to();
                            } else {

                                $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Sahiplik Id Hatası!");
                            }
                        } else {

                            $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Oturum Başlatılamadı!");
                        }
                    } else {

                        $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Kullanıcı Bulunamadı");
                    }
                } else {


                    if (trim($request->input("email")) != "" &&
                            $request->input("password") != NULL &&
                            $request->input("account_no") != NULL) {
                        /*
                         * Eğer sistem bu sunucuda değil ise
                         */
                        $server_url = $account_data["server_connection_protocol"] . $account_data["server"];

                        $login_data = [
                            'type' => 'direct',
                            'email' => $request->input("email"),
                            'password' => Hash::generate("password", $request->input("password")),
                            'account_no' => $request->input("account_no"),
                            'db' => $account_data["db_name"]
                        ];

                        $cryptor = new Cryptor(\Dipa\App::getConfig("key"));

                        $crypted_token = $cryptor->encrypt(json_encode($login_data));

                        unset($login_data);

                        $login_attack_url = $server_url . "/remote-login-attack?data=" . urlencode(base64_encode($crypted_token));

                        $header->toUrl($login_attack_url);
                    } else {

                        $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Bütün Bilgileri Lütfen Doldurunuz.");
                    }
                }
            }else{

                $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Hesap Bulunamadı.");
            }
        }
    }

    public function remoteLoginAttack() {

        $header = new Header();

        $request = new Request();

        $authmodel = $this->model("auth", "authActionModel");

        $login_data_string = base64_decode(urldecode($request->input("data")));

        $cryptor = new Cryptor(\Dipa\App::getConfig("key"));

        $login_data = $cryptor->decrypt($login_data_string);

        $login_data = json_decode($login_data, true);

    if($login_data["type"] == "mobile"){

        $user = $authmodel->isIssetUser("mobile",$login_data["email"], $login_data["mobilekey"], $login_data["db"]);

    }else{

        $user = $authmodel->isIssetUser("other",$login_data["email"], $login_data["password"], $login_data["db"]);
    }


        if ($user) {

            $auth = new Auth();

            if ($auth->login($user, $login_data["account_no"], \Dipa\App::getConfig("system_type"), self::$system_name)) {

                if ($authmodel->lastIdControl($user["owner_id"])) {

                    if ($request->input("remember") == "on") {

                        setcookie("r_remember", "ok", time() + (60 * 60 * 24), '/');
                        setcookie("r_mail", $login_data["email"], time() + (60 * 60 * 24), '/');
                        setcookie("r_account", $login_data["account_no"], time() + (60 * 60 * 24), '/');
                    } else {

                        setcookie("r_remember", "non", time() + (60 * 60 * 24), '/');
                        setcookie("r_mail", "", time() + (60 * 60 * 24), '/');
                        setcookie("r_account", "", time() + (60 * 60 * 24), '/');
                    }

                    $image_folder = MEDIA_DIR . "/" . $login_data["account_no"];
                    $image_s_folder = MEDIA_DIR . "/" . $login_data["account_no"] . "/s";
                    $image_t_folder = MEDIA_DIR . "/" . $login_data["account_no"] . "/t";


                    $private_folder = STORAGE_PATH . "/" .  $login_data["account_no"]."/media/private";
                    $private_system_folder = STORAGE_PATH . "/" . $login_data["account_no"]."/media/sistem";

                    if (!file_exists($private_folder)) {

                        mkdir($private_folder,0777,true);

                        chmod($private_folder, 0777);
                    }

                    if (!file_exists($private_system_folder)) {

                        mkdir($private_system_folder,0777,true);

                        chmod($private_system_folder, 0777);
                    }




                    if (!file_exists($image_folder)) {

                                    mkdir($image_folder,0777,true);

                                    chmod($image_folder, 0777);
                                }


                                if (!file_exists($image_s_folder)) {

                                    mkdir($image_s_folder,0777,true);

                                    chmod($image_s_folder, 0777);
                                }


                                if (!file_exists($image_t_folder)) {

                                    mkdir($image_t_folder,0777,true);

                                    chmod($image_t_folder, 0777);
                                }





                    $doviz_model = $this->model("doviz", "dovizModel");

                    $doviz_model->kurlariGuncelle(true, $user);
                    
                  \Dipa\Io\Log::write("Oturum Açıldı",$request->input("account_no"),$request->input("email"));

                  $header->to();

                } else {

                    $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Sahiplik Id Hatası!");
                }
            } else {

                $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Oturum Başlatılamadı!");
            }
        } else {

            $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Kullanıcı Bulunamadı");
        }
    }

    public function logout() {

         \Dipa\Io\Log::write("Oturum Kapatıldı",self::$account_no,self::$userInfo["id"]);
         
        $auth = new Auth();

        $auth->logout("login");
    }


    public function mobileLoginAttack() {

        $header = new Header();

        $request = new Request();

        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {

            \Dipa\App::editConfig("db", "database", \Dipa\App::getConfig("db", "masterDbName"));
        }

        $authmodel = $this->model("auth", "authActionModel");

        if (trim($request->input("email")) == "") {

            $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Bütün Bilgileri Lütfen Doldurunuz.");
        } else {

            $masterquery = $authmodel->getConnection()->prepare("SELECT * FROM account_data WHERE account_no = ? and remove = ?");

            $masterquery->execute([$request->input("account_no"), 0]);

            $account_data = $masterquery->fetch();

            if ($account_data) {

                if ($account_data["server_ip"] == "localhost") {

                    if ($user = $authmodel->isMobileUser($request->input("email"), $request->input("mobilekey"), $request->input("account_no"))) {

                        $auth = new Auth();

                        if ($auth->login($user, $request->input("account_no"), \Dipa\App::getConfig("system_type"), self::$system_name)) {

                            if ($authmodel->lastIdControl($user["owner_id"])) {

                                if ($request->input("remember") == "on") {

                                    setcookie("r_remember", "ok", time() + (60 * 60 * 24), '/');
                                    setcookie("r_mail", $request->input("email"), time() + (60 * 60 * 24), '/');
                                    setcookie("r_account", $request->input("account_no"), time() + (60 * 60 * 24), '/');
                                } else {

                                    setcookie("r_remember", "non", time() + (60 * 60 * 24), '/');
                                    setcookie("r_mail", "", time() + (60 * 60 * 24), '/');
                                    setcookie("r_account", "", time() + (60 * 60 * 24), '/');
                                }

                                $image_folder = MEDIA_DIR . "/" .  $request->input("account_no");
                                $image_s_folder = MEDIA_DIR . "/" .  $request->input("account_no") . "/s";
                                $image_t_folder = MEDIA_DIR . "/" .  $request->input("account_no") . "/t";


                                $private_folder = STORAGE_PATH . "/" .  $request->input("account_no")."/media/private";
                                $private_system_folder = STORAGE_PATH . "/" .  $request->input("account_no")."/media/sistem";

                                if (!file_exists($private_folder)) {

                                    mkdir($private_folder,0777,true);

                                    chmod($private_folder, 0777);
                                }

                                if (!file_exists($private_system_folder)) {

                                    mkdir($private_system_folder,0777,true);

                                    chmod($private_system_folder, 0777);
                                }


                                if (!file_exists($image_folder)) {

                                    mkdir($image_folder,0777,true);

                                    chmod($image_folder, 0777);
                                }


                                if (!file_exists($image_s_folder)) {

                                    mkdir($image_s_folder,0777,true);

                                    chmod($image_s_folder, 0777);
                                }


                                if (!file_exists($image_t_folder)) {

                                    mkdir($image_t_folder,0777,true);

                                    chmod($image_t_folder, 0777);
                                }




                                $doviz_model = $this->model("doviz", "dovizModel");

                                $doviz_model->kurlariGuncelle(true, $user);

                                \Dipa\Io\Log::write("Oturum Açıldı",$request->input("account_no"),$request->input("email"));

                                $header->to();
                            } else {

                                $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Sahiplik Id Hatası!");
                            }
                        } else {

                            $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Oturum Başlatılamadı!");
                        }
                    } else {

                        $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Kullanıcı Bulunamadı");
                    }
                } else {


                    if (trim($request->input("email")) != "" &&
                        $request->input("mobilekey") != NULL &&
                        $request->input("account_no") != NULL) {
                        /*
                         * Eğer sistem bu sunucuda değil ise
                         */
                        $server_url = $account_data["server_connection_protocol"] . $account_data["server"];

                        $login_data = [
                            'type' => 'mobile',
                            'email' => $request->input("email"),
                            'mobilekey' => $request->input("mobilekey"),
                            'account_no' => $request->input("account_no"),
                            'db' => $account_data["db_name"]
                        ];

                        $cryptor = new Cryptor(\Dipa\App::getConfig("key"));

                        $crypted_token = $cryptor->encrypt(json_encode($login_data));

                        unset($login_data);

                        $login_attack_url = $server_url . "/remote-login-attack?data=" . urlencode(base64_encode($crypted_token));

                        $header->toUrl($login_attack_url);


                    } else {

                        $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Bütün Bilgileri Lütfen Doldurunuz.");
                    }
                }
            }else{

                $header->toUrl(\Dipa\App::getConfig("main_login_url"), "fail", "Hesap Bulunamadı.");
            }
        }
    }

}
