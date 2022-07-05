<?php

namespace Dipa\Api;

use Dipa\Db\Model;

class Apicontrol extends Model {

    private $user_id;
    private $session_key;
    public $login_status = 0;
    public $login_user_data = 0;
    public $device_status = 0;
    public $device_reload_date = 0;
    public $device_details = 0;
    private $owner_id;
    private $device_serial;
    private $device_type;
    private $device_uniq;

    public function __construct($request) {

        header('Content-Type: application/json');

        $this->user_id = $request->input("user_id");

        $this->session_key = $request->input("api_session_key");

        $this->owner_id = $request->input("owner_id");

        $this->device_serial = $request->input("device_serial");

        $this->device_type = $request->input("device_type");

        $this->device_uniq = $request->input("device_uniq");

        \Dipa\App::editConfig("db", "database", $request->input("account_name"));

        if (!$this->auth_control()) {

            echo json_encode([
                "login_status" => $this->login_status,
                "device_status" => $this->device_status,
                "userid" => $this->user_id,
                "key" => $this->session_key,
                "account_name" => $request->input("account_name"),
                "operation_status" => 0
            ]);

            die();
        } else {

            $this->login_status = 1;
        }
    }


    public function getUserData(){
        return $this->login_user_data;
    }
    
    public function getType(){
        
        return  $this->device_type;
    }
    public function auth_control() {

        $model = \Dipa\Controller::include_model("Parakendeapi", "parakendeAutModel");

        $login_data = $model->isLogin($this->session_key, $this->user_id);

        if ($login_data) {
            
            $this->login_user_data = $login_data;

            $this->login_status = 1;

            $device_status = $model->isDevice($this->device_serial, $this->device_type, $this->owner_id, $this->device_uniq);

            if ($device_status) {
                
                $this->device_details = $device_status;

                $this->device_reload_date = $device_status["reload_date"];

                $this->device_status = 1;
            }

            return $device_status;
        }
    }
    
    
    public function getDefaultResult($status){
        
        return [
            "login_status" => $this->login_status,
            "device_status" => $this->device_status,
            "device_reload_date" => $this->device_reload_date,
            "device_details" => $this->device_details,
            "login_details" => $this->login_user_data,
            "operation_status" => $status
        ];
        
    }

    public function result($status, $resultData) {
        echo json_encode([
            "login_status" => $this->login_status,
            "device_status" => $this->device_status,
            "device_reload_date" => $this->device_reload_date,
            "device_details" => $this->device_details,
            "login_details" => $this->login_user_data,
            "operation_status" => $status,
            "result" => $resultData
        ]);
    }

}
