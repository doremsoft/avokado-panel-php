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

                    $query = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND remove = ?");

                    $query->execute([$email, $password, 0]);

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

}
