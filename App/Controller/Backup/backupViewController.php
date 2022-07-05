<?php
namespace App\Controller\Backup;

class backupViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }



    public function backupList() {

        $backupModel = $this->model("backup", "backupViewModel");

        $backuplist = $backupModel->getBackupList();
        
               
        \Dipa\Io\Log::write("",self::$account_no,self::$userInfo["id"]);

        return $this->view("backup/backup-list", ['yedekler' => $backuplist]);
    }
    
      
}