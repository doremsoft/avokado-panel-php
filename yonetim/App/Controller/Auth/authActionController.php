<?php

namespace App\Controller\Auth;

use \Dipa\Support\Hash;
use \Dipa\Http\Header;
use \Dipa\Http\Request;
use \Dipa\Sys\Auth;

class authActionController extends \Dipa\Controller {

    public function loginAttack() {

        $header = new Header();

        $request = new Request();

        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {

            \Dipa\App::editConfig("db", "database", \Dipa\App::getConfig("db", "masterDbName"));
        }

        $authmodel = $this->model("auth", "authActionModel");

        if ($user = $authmodel->isUser($request->input("email"), Hash::generate("password", $request->input("password")), $request->input("account_no"))) {

            $auth = new Auth();

            if ($auth->login($user,$request->input("account_no"),"yonetim", self::$system_name)) {

                $header->to();
            } else {

                $header->result("fail", "Oturum Başlatılamadı!")->back();
            }
        } else {

            $header->result("fail", "Kullanıcı Bulunamadı")->back();
        }
    }

    public function logout() {


        $auth = new Auth();

        $auth->logout("login");
    }

}
