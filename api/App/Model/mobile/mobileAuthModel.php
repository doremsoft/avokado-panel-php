<?php
use \Dipa\Db\Model;
use Dipa\Support\Curl;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mobileAuthModel
 *
 * @author dogus
 */
class mobileAuthModel extends Model {

    public function isUser($email, $password, $account_number = NULL) {

        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {

            if ($account_number == NULL) {

                return false;

            } else {

                $res = Curl::post(\Dipa\App::getConfig("manager_url")."/hesap/detaylar",["account_no"=>$account_number],   "" , "",false);

                $account_data =  json_decode($res,true);

                if ($account_data) {

                    \Dipa\App::editConfig("db", "database", $account_data["db_name"]);

                    $this->resetConnection();

                    $query = $this->getConnection()->prepare("SELECT id , name , surname , owner_id ,image ,admin , bildirim_session_key  FROM users WHERE email = ? AND password = ? AND remove = ? AND mobile_api = ?");

                    $query->execute([$email, $password, 0, 1]);

                    $user_result = $query->fetch(PDO::FETCH_ASSOC);

                    if ($user_result) {

                        $session_key =

                        $bildirimkey = md5(base64_encode(uniqid()));

                        $now_time = date("Y-m-d H:i:s");

                        $updateSql = "UPDATE users SET mobile_api_session_key = :key , mobile_api_last_connection = :now , bildirim_session_key = :bildirimkey WHERE id = :user_id";

                        $query = $this->getConnection()->prepare($updateSql);

                        $update = $query->execute(array(
                            "key" => $session_key,
                            "now" => $now_time,
                            "user_id" => $user_result['id'],
                            "bildirimkey" => $bildirimkey
                        ));

                        if ($update) {

                            $user_result['bildirim_session_key'] = $bildirimkey;

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

                    return false;
                }
            }
        } else {


            $query = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND remove = ? AND mobile_api = ?");

            $query->execute([$email, $password, 0, 1]);

            return $query->fetch();
        }
    }
    
    public function isLogin($api_session_key, $user_id) {

        $query = $this->getConnection()->prepare("SELECT id,admin,bildirim_session_key,hesap_no  FROM users WHERE id = ? AND password = ? AND remove = ? AND mobile_api = ?");

        $query->execute([$user_id, $api_session_key, 0, 1]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }



    public function getAccountData($account_no){

        $query = $this->getConnection()->prepare("SELECT *  FROM hesap_detaylari WHERE  durum = ? AND account_id = ?");

        $query->execute([ 1, $account_no]);

        return $query->fetch(PDO::FETCH_ASSOC);


    }

}