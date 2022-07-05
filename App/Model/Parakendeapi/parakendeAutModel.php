<?php

use \Dipa\Db\Model;

/**
 *
 * @author Doğuş DİCLE
 */
class parakendeAutModel extends Model {

    public function isUser($email, $password, $account_number = NULL) {


        if (\Dipa\App::getConfig("auth", "login_with_account_number")) {

            if ($account_number == NULL) {

                return false;
            } else {

                $masterquery = $this->getConnection()->prepare("SELECT * FROM account_data WHERE account_no = ? and remove = ?");

                $masterquery->execute([$account_number, 0]);

                $account_data = $masterquery->fetch();

                if ($account_data) {

                    \Dipa\App::editConfig("db", "database", $account_data["db_name"]);

                    $this->resetConnection();

                    $query = $this->getConnection()->prepare("SELECT id , name , surname , owner_id ,image FROM users WHERE email = ? AND password = ? AND remove = ? AND api_permission = ?");

                    $query->execute([$email, $password, 0, 1]);

                    $user_result = $query->fetch();

                    if ($user_result) {

                        $session_key = md5(rand(1000, 9999));

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

                            $user_result['account_nick_name'] = $account_data["account_nick_name"];

                            $user_result['login_status'] = 1;

                            $user_result['api_session_key'] = $session_key;

                            $user_result['api_last_connection'] = $now_time;

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


            $query = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND remove = ? AND api_permission = ?");

            $query->execute([$email, $password, 0, 1]);

            return $query->fetch();
        }
    }

    public function isLogin($api_session_key, $user_id) {

        $query = $this->getConnection()->prepare("SELECT id  FROM users WHERE id = ? AND api_session_key = ? AND remove = ? AND api_permission = ?");

        $query->execute([$user_id, $api_session_key, 0, 1]);

        return $query->fetch();
    }

}
