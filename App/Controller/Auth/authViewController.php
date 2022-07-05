<?php

namespace App\Controller\Auth;

class authViewController extends \Dipa\Controller {

    public function login() {

        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {

            $r_mail = "";
            $r_account = "";
            $r_remember = "";

            if (isset($_COOKIE["r_mail"])) {

                $r_mail = $_COOKIE["r_mail"];
            }

            if (isset($_COOKIE["r_account"])) {

                $r_account = $_COOKIE["r_account"];
            }

            if (isset($_COOKIE["r_remember"])) {

                $r_remember = $_COOKIE["r_remember"];
            }



            return $this->view("auth/global-login", [
                        'r_mail' => $r_mail,
                        'r_account' => $r_account,
                        'r_remember' => $r_remember,
                        'main_url'=>\Dipa\App::getConfig("main_url")
            ]);
        } else {

            return $this->view("auth/login",[
                'main_url'=>\Dipa\App::getConfig("main_url")
            ]);
        }
    }

    public function register() {
        
    }

    public function resetPassword() {
        
    }

}
