<?php

use \Dipa\Db\Model;

/**
 *
 * @author Doğuş DİCLE
 */
class authActionModel extends Model {

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

                    $query = $this->getConnection()->prepare("SELECT * FROM users WHERE (users.email = ? or users.username = ?)  AND users.password = ? AND users.remove = ? and users.web = ?");

                    $query->execute([$email, $email, $password, 0, 1]);

                    $user_result = $query->fetch();

                    if ($user_result) {

                        $_SESSION['extra_config'] = ['db' => [
                                'database' => $account_data["db_name"]
                        ]];

                        return $user_result;
                    } else {
                        if (isset($_SESSION['extra_config'])) {
                            $_SESSION['extra_config'] = [];
                        }
                        return false;
                    }
                } else {
                    if (isset($_SESSION['extra_config'])) {
                        $_SESSION['extra_config'] = [];
                    }
                    return false;
                }
            }
        } else {


            $query = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND remove = ?");

            $query->execute([$email, $password, 0]);

            return $query->fetch();
        }
    }


    public function isMobileUser($email, $mobileapisessionkey, $account_number = NULL) {


            if ($account_number == NULL) {

                return false;
            } else {

                $masterquery = $this->getConnection()->prepare("SELECT * FROM account_data WHERE account_no = ? and remove = ?");

                $masterquery->execute([$account_number, 0]);

                $account_data = $masterquery->fetch();

                if ($account_data) {

                    \Dipa\App::editConfig("db", "database", $account_data["db_name"]);

                    $this->resetConnection();

                    $query = $this->getConnection()->prepare("SELECT * FROM users WHERE (email = ? or username = ?)  AND mobile_api_session_key = ? AND remove = ? and mobile_api = ?");

                    $query->execute([$email, $email, $mobileapisessionkey, 0, 1]);

                    $user_result = $query->fetch();

                    if ($user_result) {

                        $_SESSION['extra_config'] = [
                            'mobile' => true,
                            'db' => [
                            'database' => $account_data["db_name"]
                        ]];

                        return $user_result;
                    } else {
                        if (isset($_SESSION['extra_config'])) {
                            $_SESSION['extra_config'] = [];
                        }
                        return false;
                    }
                } else {
                    if (isset($_SESSION['extra_config'])) {
                        $_SESSION['extra_config'] = [];
                    }
                    return false;
                }
            }

    }


    public function isIssetUser($type , $email, $password, $DB_NAME) {

        \Dipa\App::editConfig("db", "database", $DB_NAME);

        $this->resetConnection();


        if($type == "mobile"){

            $query = $this->getConnection()->prepare("SELECT * FROM users WHERE (email = ? or username = ?)  AND mobile_api_session_key = ? AND remove = ? and mobile_api = ?");

            $query->execute([$email, $email, $password, 0, 1]);

            $user_result = $query->fetch();

        }else{
            $query = $this->getConnection()->prepare("SELECT * FROM users WHERE (email = ? or username = ?)  AND password = ? AND remove = ? and web = ?");

            $query->execute([$email, $email, $password, 0, 1]);

            $user_result = $query->fetch();

        }


        if ($user_result) {

            $_SESSION['extra_config'] = ['db' => [
                    'database' =>$DB_NAME
            ]];

            return $user_result;
        } else {
            if (isset($_SESSION['extra_config'])) {
                $_SESSION['extra_config'] = [];
            }
            return false;
        }
    }

    public function lastIdControl($owner_id) {

        $query = $this->getConnection()->prepare(""
                . "SELECT last_id "
                . "FROM stok_change_listener "
                . "WHERE remove = 0 and owner_id = ? ORDER BY last_id DESC LIMIT 1 ");

        $query->execute([$owner_id]);

        if ($query->fetch()) {

            return true;
        } else {


            return $this->getConnection()->prepare("INSERT INTO stok_change_listener SET last_id = 1 , owner_id = ?  ")->execute([$owner_id]);
        }
    }

}
