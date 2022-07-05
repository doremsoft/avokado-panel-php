<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class bildirimModel extends Dimodel
{
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
    public function topluBildirimGonder($request)
    {

        $baslik = $request->input("baslik");
        $ikon = $request->input("ikon");
        $mesaj = $request->input("mesaj");
        $tarih = $request->input("tarih");
        $saat = $request->input("saat");
        $server = $request->input("server");


        echo $server;




        $hesaplar =  $this->getConnection()->query("SELECT db_name FROM account_data WHERE account_server_name = '{$server}' and remove = 0 ORDER BY id DESC ")->fetchAll(PDO::FETCH_ASSOC);


        if($hesaplar){

            $db_user_name =  $this->dbConfig["username"];
            $db_password = $this->dbConfig["password"];

            foreach ($hesaplar as $key => $val){


                $sb_name = $val["db_name"];
                $conn = false;


                try {
                    $conn = new \PDO('mysql:host=localhost;dbname=' .$sb_name,$db_user_name, $db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                } catch (PDOException $e) {
                    echo "Veritabanı Bağantı Hatası!:" . $e->getMessage();
                    die();
                }


                $this->bildirimEkle(new Controller(), $baslik , $mesaj, 0,  $tarih, $saat,  0 ,  0 , $ikon , $conn);

                $conn = null;
            }

        }else{
            echo "hesap Yok";
        }


    }


}
