<?php

namespace App\Controller\Auth;

class authViewController extends \Dipa\Controller {

    public function login() {

        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {

            return $this->view("auth/global-login");
        } else {

            return $this->view("auth/login");
        }
    }

    public function register() {
        
    }

    public function resetPassword() {
        
    }

}
