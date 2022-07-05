<?php

namespace App\Controller\Reset;

class resetActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function resetAllData() {

        $resetModel = $this->model("reset", "resetModel");

        $resetlist = $resetModel->resetAllData();


        if ($resetlist) {

            \Dipa\Io\Log::write("sistem sıfırlandı", self::$account_no, self::$userInfo["id"]);

            $this->header->result("success", "Sistem Sıfırlandı")->to("/");
        } else {
            
            \Dipa\Io\Log::write("sistem sıfırlama başarısız oldu", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "İşlem Başarısız Oldu!")->back();
        }
    }

}
