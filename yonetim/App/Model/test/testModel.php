<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class testModel extends Dimodel {
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


    public function deployDb($db2_json){
        
        $db1 = [
            'host' => $this->dbConfig["host"],
            'dbname' => "u8816598_adisyon",
            'username' => $this->dbConfig["username"],
            'password' => $this->dbConfig["password"]
        ];

        $sync = new \Dipa\Db\Dbsync();
        
        $sync->apiInit($db1, $db2_json);

        $control_result = $sync->compare();

  
$sync->getCompareReport();
 
  
  
  
    }
    
}
