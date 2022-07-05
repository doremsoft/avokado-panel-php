<?php

namespace App\Controller\User;

use \Dipa\Http\Request;
use \Dipa\Http\Header;
use \Dipa\Sys\Session;
use \Dipa\Io\File;

class userActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function authUpdate() {

        $request = new Request();
        $header = new Header();
        $user_id = $request->input("user_id");
        $auth_selected = $request->input("auth_selected");
        $list = [];

        if (is_array($auth_selected)) {

            foreach ($auth_selected as $key => $value) {



                if (is_array($value)) {

                    foreach ($value as $key2 => $value2) {

                        $list[$key][$key2] = 1;
                    }
                } else {

                    $list[$key] = 1;
                }
            }
        }


        $list = serialize($list);


        $userModel = $this->model("user", "userActionModel");

        $result = $userModel->updateAuth($user_id, $list);

        if ($result) {

            \Dipa\Io\Log::write("kullanıcı yetkileri güncellendi", self::$account_no, self::$userInfo["id"]);


            $header->result("success", "Yetkiler Güncellendi")->to("user/auth/" . $request->input("user_id"));
        } else {

            \Dipa\Io\Log::write("kullanıcı yetkileri güncellenemedi", self::$account_no, self::$userInfo["id"]);


            $header->result("fail", "Yetkiler Güncellenemedi!")->back();
        }
    }

    public function update() {

        $request = new Request();

        $header = new Header();

        if ($request->has("name") && $request->has("surname") && $request->has("email")) {

            if (isset($_FILES['image'])) {

                $file = new File();

                $kontroller = ['ex' => ['jpg'], 'max-size' => 241242];

                $session = new Session();

                $account_no = $session->get("account_no");

                if ($file->upload($_FILES['image'], "media/" . $account_no . "/userpicture/", $kontroller, time())) {

                    $request->append("image", $file->get_base_name());
                }
            }

            $userModel = $this->model("user", "userActionModel");

            $result = $userModel->updateUser($request, $request->input("user_id"));

            if ($result) {
                \Dipa\Io\Log::write("kullanıcı bilgileri güncellendi", self::$account_no, self::$userInfo["id"]);


                $header->result("success", "Bilgiler Güncellendi")->to("user/edit/" . $request->input("user_id"));
            } else {

                \Dipa\Io\Log::write("kullanıcı bilgiletri güncellenemedi", self::$account_no, self::$userInfo["id"]);



                $header->result("fail", "Bilgiler Güncellenemedi!")->back();
            }
        } else {
            $header->result("fail", "Bütün Zorunlu Alanlar Doldurulmalı!")->back();
        }
    }

    public function passwordUpdate() {

        $request = new Request();

        $header = new Header();

        if ($request->has("eskisifre") && $request->has("yenisifre") && $request->has("yenisifretekrar")) {

            if ($request->has("yenisifre") == $request->has("yenisifretekrar")) {

                $userModel = $this->model("user", "userActionModel");

                $result = $userModel->updateUserPassword($request, self::$userInfo['id']);

                if ($result) {

                         \Dipa\Io\Log::write("kullanıcı şifresi güncellendi", self::$account_no, self::$userInfo["id"]);


                         
                    $header->result("success", "Şifre Güncellendi")->to("user/password-edit");
                } else {
                           \Dipa\Io\Log::write("kullanıcı şifresi güncellenemedi", self::$account_no, self::$userInfo["id"]);



                    $header->result("fail", "Şifre Güncellenemedi!")->back();
                }
            } else {
                     \Dipa\Io\Log::write("kullanıcı şifresi güncellenemedi iki şifre uyuşmuyor", self::$account_no, self::$userInfo["id"]);


                $header->result("fail", "İki Şifre Birbiri İle Uyuşmuyor!")->back();
            }
        } else {
            $header->result("fail", "Bütün Zorunlu Alanlar Doldurulmalı!")->back();
        }
    }

    public function append() {

        $request = new Request();

        $header = new Header();

        if ($request->has("name") && $request->has("surname") && $request->has("email")) {

            if (isset($_FILES['image'])) {

                $file = new File();

                $kontroller = ['ex' => ['jpg'], 'max-size' => 241242];

                $session = new Session();

                $account_no = $session->get("account_no");

                if ($file->upload($_FILES['image'], "media/" . $account_no . "/userpicture/", $kontroller, time())) {

                    $request->append("image", $file->get_base_name());
                }
            }

            $userModel = $this->model("user", "userActionModel");

            $result = $userModel->addUser($request);

            if ($result) {
                
                    \Dipa\Io\Log::write("yeni kullanıcı eklendi", self::$account_no, self::$userInfo["id"]);


                $header->result("success", "Kullanıcı Eklendi")->to("user/list");
            } else {
                
                   \Dipa\Io\Log::write("yeni kullanıcı eklenemedi", self::$account_no, self::$userInfo["id"]);


                $header->result("fail", "Kullanıcı Eklenemedi!")->back();
            }
        } else {
            $header->result("fail", "Bütün Zorunlu Alanlar Doldurulmalı!")->back();
        }
    }


    public function faturaGorunumDegistir(){

        $request = new Request();

        $userModel = $this->model("user", "userActionModel");

        $result = $userModel->faturaGorunumDuzenle($request, self::$userInfo["id"]);

        if ($result) {
            echo 1;
        }else{
            echo 0;
        }



    }
}
