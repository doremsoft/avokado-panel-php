<?php

use \Dipa\Db\Model;

/**
 *
 * @author Doğuş DİCLE
 */
class authActionModel extends Model {


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





    public function isUser($email, $password , $owner_id) {

                    $query = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND remove = ?");

                    $query->execute([$email, $password, 0]);

                    $user_result = $query->fetch();

                    if ($user_result) {

                        return $user_result;
                    } else {

                        return false;
                    }

    }


    public function isRemoteUser($email, $password , $owner_id) {

                    $query = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND remove = ?");

                    $query->execute([$email, $password, 0]);

                    $user_result = $query->fetch();

                    if ($user_result) {

                        return $user_result;
                    } else {
                        return false;
                    }


    }

}
