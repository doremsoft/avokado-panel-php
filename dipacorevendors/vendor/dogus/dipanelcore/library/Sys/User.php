<?php
namespace Dipa\Sys;

use \Dipa\Sys\Session;
use \Dipa\Db\Dimodel;

/**
 * @author Doğuş DİCLE
 */
class User extends Dimodel {

    public $info;
    public $userId;

    public function __construct() {

        parent::__construct();

        $session = new Session();

        $this->userId = $session->get("panel_user_id_".\Dipa\App::getConfig("system_name"));

        $this->info = $this->table("users")->find($this->userId, true);
    }

    public function setHash($code) {


        $this->col["hash"] = $code;

        $this->update();
    }

}
