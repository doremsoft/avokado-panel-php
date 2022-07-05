<?php

namespace App\Controller\Parakendeapi;

use Dipa\Support\Curl;
use \Dipa\Support\Hash;
use \Dipa\Http\Header;
use \Dipa\Http\Request;
use \Dipa\Sys\Auth;

class parakendeapiAutController extends \Dipa\Controller
{


    public function login()
    {

        $result = [];

        $header = new Header();

        $request = new Request();

        $authmodel = $this->model("Parakendeapi", "parakendeAutModel");

        

        $res = Curl::post(\Dipa\App::getConfig("manager_url")."/hesap/detaylar",["account_no"=>$request->input("account_no")],   "" , "",false);


        $account_data =  json_decode($res,true);

        if ($account_data) {


            /*

            if($account_data["server_ip"] != "localhost"){

                $apiserver_url = $account_data["api_server_connection_protocol"].$account_data["api_server_url"]."/parakende-api/remote-login";

                $postfields = [
                    "db" => $account_data["db_name"],
                    "email" => $request->input("email"),
                    "password" => Hash::generate("password", $request->input("password")),
                    "account_no" => $request->input("account_no"),
                    "device_type" => $request->input("device_type"),
                    "device_serial" => $request->input("device_serial"),
                    "account_data"=> json_encode($account_data)
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiserver_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $result = curl_exec($ch);

                echo $result;

            }
            */


                if($account_data["server_ip"] == "localhost"){

                \Dipa\App::editConfig("db", "database", $account_data["db_name"]);
                \Dipa\App::editConfig("db", "host", $account_data["server_ip"]);

                $authmodel->resetConnection();

                $user = $authmodel->isUser($request->input("email"), Hash::generate("password", $request->input("password")),$account_data);

                if ($user) {

                    $device = $authmodel->isDeviceFromLogin($request->input("device_serial"), $request->input("device_type"), $user["owner_id"]);

                    if ($device) {

                        $user["device_reload_date"] = $device["reload_date"];

                        $user["device_secure_code"] = $device["uniq"];

                        $user["device_status"] = 1;

                        echo json_encode($user);

                        \Dipa\Io\Log::write("Oturum Açıldı", $request->input("account_no"), $request->input("email"), "software");

                    } else {

                        $user_result = [
                            "device_reload_date" => 0,
                            "device_secure_code" => 0,
                            "login_status" => 1,
                            "device_status" => 0,
                            "admin" => $user["admin"],
                            "offline_code" => $user["offline_code"],
                            "offline_activate" => $user["offline_activate"]


                        ];

                        echo json_encode($user_result);

                        \Dipa\Io\Log::write("Cihaza İzin Verilmedi", $request->input("account_no"), $request->input("email"), "software");
                    }

                } else {

                    echo json_encode(['login_status' => 0, 'device_status' => 0]);

                    \Dipa\Io\Log::write("Kullanıcı Bulunamadı", $request->input("account_no"), $request->input("email"), "software");
                }

            }

        } else {

            echo json_encode(['login_status' => 0, 'device_status' => 0 , 'account_status'=> 0 , 'message' => 'hesap bulunmaadı']);

            \Dipa\Io\Log::write("Hesap Bulunamadı", $request->input("account_no"), $request->input("email"), "software");

        }


    }


    public function remoteLogin()
    {

        $result = [];

        $request = new Request();

        $db_name = $request->input("db");
        $email = $request->input("email");
        $password = $request->input("password");
        $account_no = $request->input("account_no");
        $device_type = $request->input("device_type");
        $device_serial = $request->input("device_serial");
        $account_data = json_decode($request->input("account_data") , true);

        \Dipa\App::editConfig("db", "database", $db_name);

        $authmodel = $this->model("Parakendeapi", "parakendeAutModel");

        $user = $authmodel->isUser($email, $password, $account_data);

        if ($user) {

            $device = $authmodel->isDeviceFromLogin($device_serial, $device_type, $user["owner_id"]);

            if ($device) {

                $user["device_reload_date"] = $device["reload_date"];

                $user["device_secure_code"] = $device["uniq"];

                $user["device_status"] = 1;

                \Dipa\Io\Log::write("Oturum Açıldı", $request->input("account_no"), $request->input("email"), "software");

                echo json_encode($user);

            } else {

                $user_result = [
                    "device_reload_date" => 0,
                    "device_secure_code" => 0,
                    "login_status" => 1,
                    "device_status" => 0,
                    "admin" => $user["admin"],
                    "offline_code" => $user["offline_code"],
                    "offline_activate" => $user["offline_activate"]

                ];


                \Dipa\Io\Log::write("Cihaza İzin Verilmedi", $request->input("account_no"), $request->input("email"), "software");

                echo json_encode($user_result);
  }
        } else {

            \Dipa\Io\Log::write("Kullanıcı Bulunamadı", $request->input("account_no"), $request->input("email"), "software");

            echo json_encode(['login_status' => 0, 'device_status' => 0]);

       }
    }
}
