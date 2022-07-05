<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class backupViewModel extends Dimodel {
/*
 * Controller::$userInfo
 */
 /*
function __construct()
{
    // parent::__construct("pdo"); 
    // pdo - mysqli - pdox
}
*/
public function getBackupList()
{
    $tablo=$this->table("yedekler",Controller::$userInfo);

    return $this->where()->getAll();
}



    
}
