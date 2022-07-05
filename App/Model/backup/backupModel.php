<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;
use Daveismyname\SqlImport\Import;

/**
 *
 * @author Doğuş DİCLE
 */
class backupModel extends Dimodel {
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

    public function dbBackup($backup_name = "") {


        $tables = array();

        $ignore_tables = [
            "users",
            "yazilimlar",
            "yedekler",
            "hesap_detaylari",
            "paketler",
            "hesap_paketleri"
        ];

        return $this->backup_tables($this->getConnection(), true, $tables, $ignore_tables, false, $backup_name);
    }

    public function getYedek($id) {

        return $this->table("yedekler", Controller::$userInfo)->find($id)->get();
    }

    public function importSqlBackup($file_name) {


        $BACKUP_PATH =  LOG_PATH . DS . Controller::$account_no . DS."media".DS."sistem".DS."yedekler". DS . $file_name;

        if (file_exists($BACKUP_PATH)) {



                $command = 'mysql --user=' . $this->dbConfig["username"] . ' --password=' . $this->dbConfig["password"] . ' ' . $this->dbConfig["database"] . '  < ' . $BACKUP_PATH ;
                
                   $result = 3;
                   
                   $output = [];
                   
                exec($command, $output, $result);


                switch ($result) {
                    case 0:
                        
                  
                        return true;
                        break;
                    case 1:
                        return true;
                        break;
                }

        } else {
            
            echo "Yedekleme Dosyası Bulunamadı!";
            
            return false;
        }
    }
    
    public function yedekSay(){
        
        
        $sql="SELECT count(*) FROM yedekler WHERE owner_id = ? and remove = 0";
        
        
        $result = $this->getConnection()->prepare($sql); 
        
        $result->execute([Controller::$userInfo["owner_id"]]); 
        
        return $result->fetchColumn(); 


        
    }
           
                
    public function yedekSil($id){
        
        
        $yedek =  $this->table("yedekler", Controller::$userInfo)->find($id)->get();

        if($yedek){

            $yedek_dosyasi =  LOG_PATH . DS . Controller::$account_no . DS."media".DS."sistem".DS."yedekler".DS.$yedek["yedek_dosyasi"];


            
          if(file_exists($yedek_dosyasi)){
              
              unlink($yedek_dosyasi);
              
          }

        return $this->table("yedekler", Controller::$userInfo)->find($id)->remove_();
            
        }else{
            
            return false;
        }
         
        
    }


    public function yedekKaydet($yedek_adi, $yedek_dosya_adi, $yedek_tipi) {

        return $this->table("yedekler", Controller::$userInfo)
                        ->col("yedek_adi", $yedek_adi)
                        ->col("yedek_dosyasi", $yedek_dosya_adi)
                        ->col("yedek_tipi", $yedek_tipi)
                        ->col("key_id", \Dipa\App::getConfig("key_id"))
                        ->save_();
    }

    private function backup_tables($DBH, $cry = false, $tables, $ignore_tables, $compression = false, $backup_name = "") {

        $DBH->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);




        $BACKUP_PATH =  LOG_PATH . DS . Controller::$account_no . DS."media".DS."sistem".DS."yedekler";

        if (!file_exists($BACKUP_PATH)) {

            mkdir($BACKUP_PATH, 0750, true);

        }

        $BACKUP_PATH = $BACKUP_PATH . DS;

        $nowtimename = date("Y-m-d")."-".md5(time());

        $file_name = $nowtimename . '.txt';

        $sql_file = $BACKUP_PATH . $file_name;

        $pstm1 = $DBH->query('SHOW TABLES');

        $table_string = "";

        $ignore_string = "";

        while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {

            if (in_array($row[0], $ignore_tables)) {

                $ignore_string .= " --ignore-table=" . $this->dbConfig["database"] . "." . $row[0] . " ";
            } else {
                $table_string .= $row[0] . " ";
            }
        }

        $command = 'mysqldump --user=' . $this->dbConfig["username"] . ' --password=' . $this->dbConfig["password"] . ' ' . $this->dbConfig["database"] . ' ' . $ignore_string . ' > ' . $sql_file ;
        
        $result = 0;

        exec($command, $output, $result);

        if ($result == 0) {

            if (file_exists($sql_file)) {

                $sql_dosya_icerigi = "";

                $file_lines = file($sql_file);
                
                foreach ($file_lines as $line) {
                    
                    $sql_dosya_icerigi.= $line;
                }

                $dosya = fopen($sql_file, "w+");

                if ($dosya == false) {

                    echo ("Dosya açılırken hata oluştu.");

                    exit();
                }



                fwrite($dosya, $sql_dosya_icerigi);

                fclose($dosya);

                return $file_name;
            } else {

                echo ("Dosya Bulunamadı");
                return false;
            }
        } else {
            echo ("exec  başarısız oldu");
           
                return false;
        }
    }

}
