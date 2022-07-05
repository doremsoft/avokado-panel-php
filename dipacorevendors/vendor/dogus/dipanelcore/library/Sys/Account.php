<?php
namespace Dipa\Sys;

use \Dipa\Sys\Session;
use \Dipa\Db\Dimodel;
use PDO;

/**
 * @author Doğuş DİCLE
 */
class Account extends Dimodel {
    
    
    public function getDetails($account_no){

        $sql="SELECT * FROM hesap_detaylari WHERE account_id = ?";
        
         $query = $this->getConnection()->prepare($sql);

        $query->execute([$account_no]);

        return $query->fetch();
        
        
    }


    
    
}