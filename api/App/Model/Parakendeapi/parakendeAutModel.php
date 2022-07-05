<?php

use \Dipa\Db\Model;

/**
 *
 * @author Doğuş DİCLE
 */
class parakendeAutModel extends Model {



    public function accountControl($account_number){

        $masterquery = $this->getConnection()->prepare("SELECT * FROM account_data WHERE account_no = ? and remove = ?");

        $masterquery->execute([$account_number, 0]);

        $account_data = $masterquery->fetch();

        if ($account_data) {

            return $account_data;

        }else{
            return false;
        }

    }



    public function isUser($email, $password, $account_data) {

        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {


                    $query = $this->getConnection()->prepare("SELECT id , name , surname , owner_id ,image ,admin,offline_code,offline_activate FROM users WHERE email = ? AND password = ? AND remove = ? AND api_permission = ?");

                    $query->execute([$email, $password, 0, 1]);

                    $user_result = $query->fetch();

                    if ($user_result) {

                        $session_key = uniqid();

                        $now_time = date("Y-m-d H:i:s");

                        $updateSql = "UPDATE users SET api_session_key = :key , api_last_connection = :now WHERE id = :user_id";

                        $query = $this->getConnection()->prepare($updateSql);

                        $update = $query->execute(array(
                            "key" => $session_key,
                            "now" => $now_time,
                            "user_id" => $user_result['id']
                        ));

                        if ($update) {

                            $user_result['db_name'] = $account_data["db_name"];

                            $user_result['account_type'] = $account_data["account_type"];

                            $user_result['protocol'] = $account_data["server_connection_protocol"];

                            $user_result['server'] = $account_data["server"];

                            $user_result['api_protocol'] = $account_data["api_server_connection_protocol"];

                            $user_result['api_server'] = $account_data["api_server_url"];

                            $user_result['account_nick_name'] = $account_data["account_nick_name"];

                            $user_result['login_status'] = 1;

                            $user_result['api_session_key'] = $session_key;

                            $user_result['api_last_connection'] = $now_time;

                            $user_result["device_status"] = 0;

                            return $user_result;
                            
                        } else {

                            return ['login_status' => 0];
                        }
                    } else {

                        return false;
                    }


        } else {


            $query = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND remove = ? AND api_permission = ?");

            $query->execute([$email, $password, 0, 1]);

            return $query->fetch();
        }
    }

    public function isLogin($api_session_key, $user_id) {

        $query = $this->getConnection()->prepare("SELECT id,admin,kasa_id  FROM users WHERE id = ? AND api_session_key = ? AND remove = ? AND api_permission = ?");

        $query->execute([$user_id, $api_session_key, 0, 1]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function isDevice($device_serial, $device_type, $owner_id, $uniq) {
        
       $today = date("Y-m-d");

        $query = $this->getConnection()->prepare("SELECT id,reload_date,serial_code,device_uniq_code  FROM yazilimlar WHERE serial_code = ? AND device_type = ? AND  owner_id = ? AND device_uniq_code = ? AND remove = 0 AND device_status = 1 AND reload_date >= ?");

        $query->execute([$device_serial, $device_type, $owner_id, $uniq,$today]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function isDeviceFromLogin($device_serial, $device_type, $owner_id) {

        $query = $this->getConnection()->prepare("SELECT id,reload_date  FROM yazilimlar WHERE serial_code = ? AND device_type = ? AND  owner_id = ? AND remove = 0 AND device_status = 1 AND reload_date >= ?");

        $today = date("Y-m-d");
        
        $query->execute([$device_serial, $device_type, $owner_id,$today]);

        $result = $query->fetch();

        if ($result) {

            $device_id = $result["id"];

            $uniqid = md5(uniqid());

            $updateSql = "UPDATE yazilimlar SET device_uniq_code = :uniq , update_date = NOW() WHERE id = :id";

            $query = $this->getConnection()->prepare($updateSql);

            $update = $query->execute(array(
                "uniq" => $uniqid,
                "id" => $device_id
            ));
            
            if($update){
                
                return ["uniq"=>$uniqid,"reload_date"=>$result["reload_date"]];
                
            }else{
                
                return false;
            }
            
        } else {

            return false;
        }
    }

}
