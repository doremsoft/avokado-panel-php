<?php

namespace Dipa\Sys;

use Dipa\App;

/**
 *
 * @author Doğuş DİCLE
 */
class Auth extends Session {

    private $necessaryAuthority;
    private $user_has_authority;
    private $die;
    private $loggenIn;
    private $redirectUrl;
    private $userData;
    private $account;
    private $type;
    private $system_name;

    public function __construct($authority = false, $redirectUrl = NULL, $die = false, $type = "dipa" , $system_name = "avokado") {

        $this->system_name = $system_name;

        $this->necessaryAuthority = $authority;

        $this->die = $die;
        
        $this->type = $type;
       
        $this->redirectUrl = $redirectUrl;

        $this->loggenIn = $this->inLogged();

    }

    public function control($not_account = false) {

        if ($this->loggenIn) {

            $this->userData = new \Dipa\Sys\User();

                $this->user_has_authority = $this->userData->info['auths'] != NULL ? unserialize($this->userData->info['auths']) : FALSE;

                if (!$not_account) {

                    $account = new \Dipa\Sys\Account();

                    $this->account = $account->getDetails($this->getAccountNo());

                }

                return true;
                
          
        } else {

            if ($this->redirectUrl != NULL) {

                (new \Dipa\Http\Header)->toUrl($this->redirectUrl,"fail", "Oturum Açılmamış");

                die();
            } else {

                if ($this->die) {

                    die();
                } else {

                    return false;
                }
            }
        }
    }

    public function getUser() {


        return $this->userData->info;
    }



    public function getAccountNo() {

        return $this->get("account_no_".$this->system_name);
    }





    public function getAccountDetails() {

        return $this->account;
    }

    public function login($userData, $account_no = 0 , $type = "dipa", $system_name = "avokadopanel") {

        $hash = md5(rand(1000, 9999));
        $this->set("panel_login_".$system_name, true);
        $this->set("panel_user_id_".$system_name, $userData['id']);
        $this->set("panel_username_".$system_name, $userData['name']);
        $this->set("account_no_".$system_name, $account_no);
        $this->set("type_".$system_name, $type);
        $this->set("panel_user_key_".$system_name, $hash);

        return true;
    }

    public function logout($redirectUrl) {

        $this->clean();

        (new \Dipa\Http\Header)->to($redirectUrl);
    }

    public function inLogged() {

        if (
            $this->has("panel_login_".$this->system_name) &&
            $this->has("panel_user_id_".$this->system_name) &&
            $this->has("panel_username_".$this->system_name)  &&
            $this->get("type_".$this->system_name) == $this->type) {

            return true;
        } else {

            return false;
        }
    }

}
