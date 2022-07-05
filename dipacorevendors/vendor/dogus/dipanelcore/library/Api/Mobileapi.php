<?php
namespace Dipa\Api;

use Dipa\Db\Model;
/**
 * Description of Mobileapi
 *
 * @author dogus
 */
class Mobileapi extends Model {

    private $user_id;
    private $session_key;
    public $login_status = 0;
    public $login_user_data = 0;
    public $device_status = 0;
    public $device_reload_date = 0;
    public $device_details = 0;
    private $owner_id;
    private $model;
    private $account_info;

    
    public function __construct($request) {

        $this->user_id = $request->input("userid");

        $this->session_key = $request->input("userpass");

        $this->owner_id = $request->input("ownerid");

        \Dipa\App::editConfig("db", "database", $request->input("account_name"));

        $this->model = \Dipa\Controller::include_model("mobile", "mobileAuthModel");
        
        if (!$this->auth_control()) {

            echo json_encode([
                "login_status" => $this->login_status,
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

    public function auth_control() {


        $login_data =  $this->model->isLogin($this->session_key, $this->user_id);

        if ($login_data) {
            
            $this->login_user_data = $login_data;

            $this->login_status = 1;

            $this->account_info =  $this->model->getAccountData($this->login_user_data["hesap_no"]);

            return $login_data;
        }
    }

    public function getAccountInfo(){


        return $this->account_info;
    }


    public function getUserInfo(){
        return $this->login_user_data;
    }

    public function getAccountNo(){

        return $this->login_user_data["hesap_no"];
    }
    
    public function getDefaultResult($status){
        
        return [
            "login_status" => $this->login_status,
            "device_status" => $this->device_status,
            "login_details" => $this->login_user_data,
            "operation_status" => $status
        ];
        
    }

    public function result($status, $resultData) {
        echo json_encode([
            "login_status" => $this->login_status,
            "login_details" => $this->login_user_data,
            "operation_status" => $status,
            "result" => $resultData
        ]);
    }
    
    
}
