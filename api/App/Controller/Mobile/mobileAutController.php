<?php

namespace App\Controller\Mobile;

use \Dipa\Api\Apicontrol;
use \Dipa\Support\Hash;
use \Dipa\Http\Header;
use \Dipa\Http\Request;
use \Dipa\Sys\Auth;

class mobileAutController extends \Dipa\Controller {

    public function login() {

        header('Content-Type: application/json');

        $result = [];

        $header = new Header();

        $request = new Request();


        $authmodel = $this->model("mobile", "mobileAuthModel");

        $user = $authmodel->isUser($request->input("email"), $request->input("password"), $request->input("account_no"));

        if ($user) {

            $user["login_status"] = 1;

            \Dipa\Io\Log::write("Oturum Açıldı", $request->input("account_no"), $request->input("email"), "mobile");


            echo json_encode($user);

        } else {
            \Dipa\Io\Log::write("Oturum İzin Verilmedi", $request->input("account_no"), $request->input("email"), "mobile");
            echo json_encode(['login_status' => 0]);
        }
    }

}
