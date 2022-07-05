<?php

namespace Dipa\Db;

use \Dipa\App;
use PDO;
use PDOException;
use stdClass;

class Dimodel {

    private $dbConnection;
    private $dbEngine;
    protected $dbConfig;
    private $dbdriver;
    private $connLib;
    private $prepare;
    private $returnSql = false;
    private $dumpSql = false;
    private $querySql;
    /*
     * Tablo sistemi ile
     */
    protected $defined_table = false;
    public $table;
    public $tableName;
    public $col;
    protected $table_cols = NULL;
    protected $disable_default = false;
    public $selectedId;
    protected $select = " * ";
    protected $from = NULL;
    protected $join = "";
    protected $orderby = NULL;
    protected $in = NULL;
    protected $where = NULL;
    protected $whereTemp = NULL;
    protected $whereQuery = "";
    protected $limit = NULL;
    protected $getAllQuery;
    protected $like =NULL;

    public function __construct($lib = null, $config = null) {

        $this->connLib = "pdo";

        $this->dbdriver = $config == null ? App::getConfig("db", "driver") : $config['driver'];

        $this->dbConfig = $config == null ? App::getConfig("db") : $config;

        $this->pdoConnect();

        $this->cols = new stdClass();
    }

    protected function pdoConnect() {
        try {
            $database_connection = new \PDO(
                $this->dbConfig["driver"].':host=' . $this->dbConfig["host"] . ';dbname=' .
                $this->dbConfig["database"] . '', $this->dbConfig["username"], $this->dbConfig["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->dbConnection = $database_connection;
        } catch (PDOException $e) {

            echo "Veritabanı Bağantı Hatası!:" . $e->getMessage();

            if (isset($_SESSION["extra_config"]["db"]["database"])) {
                unset($_SESSION["extra_config"]);
            }


            die();
        }
    }

    public function getConnection() {
        return $this->dbConnection;
    }

    public function getEngine() {
        return $this->dbEngine;
    }

    public function getLibName() {
        return $this->connLib;
    }

    public function select($select = NULL) {

        if ($select != NULL) {

            if (is_array($select)) {

                $this->select = "";


                foreach ($select as $key) {


                    $this->select .= " " . $key . " ,";
                }

                $this->select = rtrim($this->select, ",");
            } else {
                $this->select = $select;
            }
        }

        return $this;
    }


    public function like($like_string = null){

        $this->like = $like_string;
        return $this;

    }

    public function from($from = NULL) {

        if ($from != NULL) {

            $this->from = $from;
        }

        return $this;
    }

    /*
     *  ->where(["cari_vergi_now" =>["=",3128]])
     *  ->where("remove = 0")
     */

    public function where($where = NULL) {

        if ($where != NULL) {

            if (is_array($where)) {


                if ($this->whereTemp == NULL) {

                    $this->whereTemp = $where;
                } else {

                    foreach ($where as $key => $value) {
                        $this->whereTemp[$key] = $value;
                    }
                }
            } else {

                $sql = $where;


                if (strpos($sql, '<=')) {
                    $ex = explode("<=", $sql);
                    $this->whereTemp[$ex[0]] = ["<=", $ex[1]];
                } else if (strpos($sql, '>=')) {
                    $ex = explode(">=", $sql);
                    $this->whereTemp[$ex[0]] = [">=", $ex[1]];
                } else if (strpos($sql, '<>')) {
                    $ex = explode("<>", $sql);
                    $this->whereTemp[$ex[0]] = ["<>", $ex[1]];
                } else if (strpos($sql, '!=')) {
                    $ex = explode("!=", $sql);
                    $this->whereTemp[$ex[0]] = ["!=", $ex[1]];
                } else if (strpos($sql, '!==')) {
                    $ex = explode("!==", $sql);
                    $this->whereTemp[$ex[0]] = ["!==", $ex[1]];
                } else if (strpos($sql, '===')) {
                    $ex = explode("===", $sql);
                    $this->whereTemp[$ex[0]] = ["===", $ex[1]];
                } else if (strpos($sql, '==')) {
                    $ex = explode("==", $sql);
                    $this->whereTemp[$ex[0]] = ["==", $ex[1]];
                } else if (strpos($sql, '=')) {
                    $ex = explode("=", $sql);
                    $this->whereTemp[$ex[0]] = ["=", $ex[1]];
                } else if (strpos($sql, '>')) {
                    $ex = explode(">", $sql);
                    $this->whereTemp[$ex[0]] = [">", $ex[1]];
                } else if (strpos($sql, '<')) {
                    $ex = explode("<", $sql);
                    $this->whereTemp[$ex[0]] = ["<", $ex[1]];
                }
            }
        }
        return $this;
    }

    public function whereSql($sql) {

        $this->whereQuery .= " " . $sql;
        return $this;
    }

    public function disableDefault() {

        $this->disable_default = true;

        return $this;
    }

    public function getWhere($type = NULL) {


        if (isset($this->table->selectWhere)) {

            if (is_array($this->table->selectWhere) && $this->disable_default == false) {

                if (is_array($this->whereTemp)) {

                    $this->whereTemp = array_merge($this->whereTemp, $this->table->selectWhere);
                } else {
                    $this->whereTemp = $this->table->selectWhere;
                }
            }
        }


        $whereString = "";

        $i = 0;


        if (is_array($this->whereTemp)) {


            foreach ($this->whereTemp as $key => $val) {

                if ($i != 0) {

                    $whereString .= " and ";
                }

                if (is_numeric($val[1])) {


                    $whereString .= $key . " " . $val[0] . " " . $val[1];
                } else {

                    if (isset($val[2])) {

                        $whereString .= $key . " " . $val[0] . " $val[1] ";
                    } else {
                        $whereString .= $key . " " . $val[0] . " \"$val[1]\" ";
                    }
                }

                $i++;
            }
        }



        if ($whereString == "") {

            if ($whereString != "" && $whereString != NULL) {

                $this->where = "WHERE " . $whereString;

            } else {

                $this->where = "";
            }
        } else {

            $this->where = "WHERE " . $whereString;



        }

        return true;
    }

    public function getFrom() {

        if ($this->defined_table) {

            return $this->tableName;
        } else {

            return $this->from;
        }
    }

    public function orderBy($orderBy, $orderDir = null) {


        if (!is_null($orderDir)) {


            $this->orderby = $orderBy . ' ' . strtoupper($orderDir);
        } else {

            if (stristr($orderBy, ' ') || $orderBy == 'rand()') {

                $this->orderby = $orderBy;


            } else {
                $this->orderby = $orderBy . ' ASC';
            }
        }



        return $this;
    }

    public function limit($limit, $limitEnd = null) {

        if (!is_null($limitEnd)) {
            $this->limit = $limit . ', ' . $limitEnd;
        } else {
            $this->limit = $limit;
        }



        return $this;
    }

    public function in($column, $in) {

        $this->in = " {$column} in ($in)";

        return $this;
    }

    public function returnSql($status = true) {

        $this->returnSql = $status;

        return $this;
    }

    public function dumpSql($status = true) {

        $this->dumpSql = $status;

        return $this;
    }

    public function prepareQuery($queryString = NULL) {

        $this->querySql = "SELECT {$this->select} FROM {$this->getFrom()}";


        $this->querySql .= $this->join;


        if ($this->getWhere()) {

            $this->querySql .= ' ' . $this->where;

            if ($this->in != NULL) {

                $this->querySql .= ' and ' . $this->in;
            }
        } else {

            if ($this->in != NULL) {

                $this->querySql .= ' WHERE ' . $this->in;
            }
        }

        if ($queryString != NULL) {

            $this->querySql .= ' ' . $queryString;
        }

        if ($this->orderby != NULL) {

            $this->querySql .= ' ' . $this->orderby;
        }

        if ($this->limit != NULL) {

            $this->querySql .= ' ' . $this->limit;
        }
    }

    public function get($queryString = NULL) {

        $this->prepareQuery($queryString);

        if ($this->dumpSql) {
            var_dump($this->querySql);
        }

        if ($this->returnSql) {

            return $this->querySql;

            die();
        } else {

            if ($this->querySql != "" && $this->querySql != NULL) {


                $result = $this->dbConnection->query($this->querySql)->fetch();


                if ($this->defined_table) {

                    if ($result) {

                        $this->col = array_merge($this->col, $result);
                    }
                }


                return $result;
            } else {
                return false;
            }
        }
    }

    public function getAll($queryString = NULL) {


        $this->prepareQuery($queryString);

        if ($this->dumpSql) {
            var_dump($this->querySql);
        }

        if ($this->returnSql) {


            return $this->querySql;

            die();
        } else {

            return $this->dbConnection->query($this->querySql)->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function exists($queryString = NULL) {

        $sql = "SELECT {$this->select} FROM {$this->getFrom()}";

        if ($this->getWhere()) {

            $sql .= ' ' . $this->where;
        }

        if ($queryString != NULL) {

            $sql .= ' ' . $queryString;
        }

        if ($this->returnSql) {
            return $sql;
            die();
        }

        $getAll = $this->dbConnection->query($sql);


        if ($getAll->rowCount() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function getSql() {
        return $this->querySql;
    }

    public function setSql($sql) {
        $this->querySql = $sql;
        return $this;
    }

    public function run($arguments = NULL) {

        $query = $this->dbConnection->prepare($sql);

        if ($arguments == NULL) {

            return $query->execute();
        } else {

            if (is_array($arguments)) {

                return $query->execute($arguments);
            } else {

                return false;
            }
        }
    }

    //------------------------Table Sistem Methodları--------------------------//

    public function table($tableName, $args = NULL, $return = false) {

        if (file_exists($tableFile = APP_DIR . DS . "Tables/{$tableName}.php")) {

            require_once $tableFile;

            if (class_exists($tableName)) {

                if ($this->defined_table) {

                    $this->reset();
                }



                $this->tableName = $tableName;

                if ($args == NULL) {

                    $table = new $tableName();
                } else {

                    $table = new $tableName($args);
                }


                $this->table = $table;

                if (isset($table->cols)) {

                    if (is_array($table->cols)) {

                        $this->col = $table->cols;

                        $this->table_cols = $table->cols;
                    } else {
                        $this->table_cols = NULL;
                    }
                } else {
                    $this->table_cols = NULL;
                }

                $this->defined_table = true;
            } else {

                exit("Table dosyasında sınıf tanımlı değil: $tableName");
            }
        } else {
            exit("Table dosyası bulunamadı: {$tableName}.php");
        }

        if ($return) {

            return $this->col;
        } else {

            return $this;
        }
    }

    public function col($key, $val, $type = null) {

        if (array_key_exists($key, $this->col)) {


            if ($type == null) {

                $this->col[$key] = $val;
            } else {
                switch ($type) {

                    case "string":

                        $this->col[$key] = "{$val}";

                        break;

                    default:
                        $this->col[$key] = $val;
                        break;
                }
            }


            return $this;
        } else {


            exit("Sütün Bulanamadı! = " . $key);
        }
    }

    public function clearCols() {
        $this->col = $table->cols;
    }

    public function find($id, $return = false) {

        $this->selectedId = $id;

        $this->where([$this->table->index => ["=", $id]]);

        $this->prepareQuery();

        $result = $this->dbConnection->query($this->querySql)->fetch(PDO::FETCH_ASSOC);

        if ($this->defined_table) {

            if ($result) {

                $this->col = array_merge($this->col, $result);
            }
        }

        if ($return) {

            return $this->col;
        } else {
            return $this;
        }
    }

    public function findWhere($where, $return = false) {

        $this->where($where);

        $this->prepareQuery();

        $result = $this->dbConnection->query($this->querySql)->fetch(PDO::FETCH_ASSOC);

        if ($this->defined_table) {

            if ($result) {

                $this->col = array_merge($this->col, $result);
            }
        }

        if ($return) {

            return $this->col;
        } else {
            return $this;
        }
    }

    public function increase_($col, $number) {

        if ($this->selectedId) {

            $this->querySql = "UPDATE {$this->tableName} SET ";

            $this->querySql .= " {$col} = {$col}+{$number} ";

            $this->querySql .= " WHERE id = {$this->selectedId}";

            $this->prepare = $this->dbConnection->prepare($this->querySql);

            return $this->prepare->execute();
        } else {
            return false;
        }
    }

    public function decrease_($col, $number) {

        if ($this->selectedId) {

            $this->querySql = "UPDATE {$this->tableName} SET ";

            $this->querySql .= " {$col} = {$col}-{$number} ";

            $this->querySql .= " WHERE id = {$this->selectedId}";

            $this->prepare = $this->dbConnection->prepare($this->querySql);

            return $this->prepare->execute();
        } else {
            return false;
        }
    }

    public function update_() {

        if ($this->selectedId) {

            $this->querySql = "UPDATE {$this->tableName} SET ";

            $this->col = array_merge($this->col, $this->table->update);

            $arrays = [];

            foreach ($this->col as $key => $value) {

                if ($key != $this->table->index) {


                    $this->querySql .= " {$key} = ? ,";

                    array_push($arrays, $value);
                }
            }

            $this->querySql = rtrim($this->querySql, ",");

            $this->querySql .= " WHERE id = ?";

            array_push($arrays, $this->selectedId);

            $this->prepare = $this->dbConnection->prepare($this->querySql);

            return $this->prepare->execute($arrays);
        } else {
            return false;
        }
    }

    public function paginate($request, $quantity = NULL) {

        $sayfa = 1;

        $sayfada = $quantity == null ? 10 : $quantity;

        if ($request != null) {

            if ($request->has("quantity")) {

                $sayfada = $request->input("quantity");
            }

            if ($request->has("page")) {

                $sayfa = $request->input("page");
            }
        }

        $select = "SELECT {$this->select} ";

        $from = "FROM {$this->getFrom()} ";

        $sql = " FROM {$this->getFrom()}";

        $selectSql = $sql;

        if ($this->join != null) {

            $selectSql .= ' ' . $this->join;
        }



        if ($this->getWhere()) {
            $sql .= ' ' . $this->where;
            $selectSql .= ' ' . $this->where;
        }



        if($this->like != NULL){


            if ($selectSql == "") {

                $selectSql = $this->like;


            }else{

                $selectSql= $selectSql." AND ".$this->like;
            }



            if ($sql == "") {

                $sql = $this->like;


            }else{

                $sql= $sql." AND ".$this->like;
            }



        }




        if ($sayfa < 1) {

            $sayfa = 1;
        }
        /*
         * Toplam İçerik Ve Sayfa Sayısı
         */
        $result = $this->dbConnection->prepare("SELECT COUNT(*) AS toplam $sql");
        $result->execute();
        $toplam_icerik = $result->fetchColumn();
        $toplam_sayfa = ceil($toplam_icerik / $sayfada);


        if ($sayfa > $toplam_sayfa) {

            $sayfa = $toplam_sayfa;
        }

        $limit = ($sayfa - 1) * $sayfada;

        if ($limit < 0) {

            $limit = 0;
        }

        if ($this->orderby != NULL) {

            $selectSql .= ' ' . $this->orderby;
        }



        if($this->dbdriver == "pgsql"){


            if($limit == 0){

                $sql_string = $select . $selectSql . ' OFFSET ' . $sayfada;
            }else{

                $sql_string = $select . $selectSql . " LIMIT " . $limit . ' OFFSET ' . $sayfada;
            }


        }else{
            $sql_string = $select . $selectSql . " LIMIT " . $limit . ', ' . $sayfada;

        }





        if ($this->dumpSql) {
            var_dump($sql_string);
        }






        $getQuery = $this->dbConnection->prepare($sql_string);

        $getQuery->execute();

        $getAll = $getQuery->fetchAll(PDO::FETCH_ASSOC);



        return [
            'result' => $getAll,
            'paginate' => [
                'total_page' => $toplam_sayfa,
                'quantity' => $sayfada,
                'now_page' => $sayfa
            ]
        ];
    }

    public function save_() {

        $this->querySql = "INSERT INTO {$this->tableName} ";

        $keys = "";

        $vals = "";

        $exec_data = [];

        $i = 0;

        $this->col = array_merge($this->col, $this->table->insert);

        foreach ($this->col as $key => $value) {

            if ($key != $this->table->index) {

                if ($value != NULL) {
                    $keys .= "{$key} ,";

                    $vals .= "? ,";

                    $exec_data[$i] = $value;

                    $i++;
                }
            }
        }

        $keys = rtrim($keys, ",");

        $vals = rtrim($vals, ",");

        $this->querySql .= " ({$keys}) VALUES ({$vals})";

        $this->prepare = $this->dbConnection->prepare($this->querySql);

        $result = $this->prepare->execute($exec_data);

        $this->prepare->closeCursor();

        if ($result) {

            $id = $this->dbConnection->lastInsertId();


            return $id;
        } else {

            return false;
        }
    }

    public function remove_() {
        if ($this->selectedId) {

            $this->querySql = "UPDATE {$this->tableName} SET ";


            $this->table->update["remove"] = 1;

            foreach ($this->table->update as $key => $value) {

                if ($key != $this->table->index) {

                    if (is_numeric($value)) {

                        $this->querySql .= " {$key} = {$value} ,";
                    } else {

                        $this->querySql .= " {$key} = \"{$value}\" ,";
                    }
                }
            }

            $this->querySql = rtrim($this->querySql, ",");

            $this->querySql .= " WHERE id = {$this->selectedId}";

            $this->prepare = $this->dbConnection->prepare($this->querySql);

            return $this->prepare->execute();
        } else {
            return false;
        }
    }

    public function resetTable() {
        $this->returnSql = false;
        $this->dumpSql = false;
        $this->querySql = NULL;

        $this->col = $this->table_cols;
        $this->disable_default = false;
        $this->selectedId = NULL;
        $this->select = " * ";
        $this->from = NULL;
        $this->join = "";
        $this->orderby = NULL;
        $this->in = NULL;
        $this->where = NULL;
        $this->whereTemp = NULL;
        $this->whereQuery = "";
        $this->limit = NULL;
        $this->getAllQuery = NULL;
        return $this;
    }

    public function reset() {

        $this->returnSql = false;
        $this->dumpSql = false;
        $this->querySql = NULL;
        $this->defined_table = false;
        $this->table = NULL;
        $this->tableName = NULL;
        $this->col = NULL;
        $this->disable_default = false;
        $this->selectedId = NULL;
        $this->select = " * ";
        $this->from = NULL;
        $this->join = "";
        $this->orderby = NULL;
        $this->in = NULL;
        $this->where = NULL;
        $this->whereTemp = NULL;
        $this->whereQuery = "";
        $this->limit = NULL;
        $this->getAllQuery = NULL;
        return $this;
    }

    public function innerjoin($joinData = null) {
        if ($joinData != NULL) {

            if (is_array($joinData)) {

                foreach ($joinData as $key => $value) {

                    $this->join .= " INNER JOIN $key ON {$value}";
                }
            } else {

                $this->join .= $joinData;
            }
        }
        return $this;
    }

    public function leftjoin($leftjoin = null) {
        if ($leftjoin != NULL) {

            if (is_array($leftjoin)) {

                foreach ($leftjoin as $key => $value) {

                    $this->join .= " LEFT JOIN $key ON {$value}";
                }
            } else {

                $this->join .= $leftjoin;
            }
        }
        return $this;
    }


    public function getColumn($column_name){

        return $this->col[$column_name];

    }

    /*
        * $tip = 0 kendine hatırlatma bildirimi
     *
        * $tip = 1 başkası duvar yorumu
     *
        * $tip = 2 başkası stok yorumu
     *
        * $tip = 3 başkası cari hesap yorumu
     *
     *  * $tip = 4 takvimden bildirim
     *
     *    $tip = 5 posta etiket
     *
     *    $tip = 6 yoruma etiket
     *
     *    $tip = 7 paylaşımınıza birisi yorum yazdı
     *
     *    $tip = 8 senet ödemesi hatırlatma
     *
     *   $tip = 9 görev tanımlandı
     *
     *   $tip = 10 göreve başlandı
     *
     *   $tip = 11 görev tamamlandı
     *
     *   $tip = 12 görev tamamlanamadı
     *
     *
        */

    public function bildirimEkle($controller, $bildirim_baslik , $bildirim_mesaj, $tip,  $tarih, $saat, $user_id = 0 , $islem_id = 0 , $bildirim_icon = 'keyboard_arrow_right' , $conn = false)
    {


        if($conn == null){
            $conn = false;
        }
        $user_info = $controller->getUserInfo();

        $url = "";

        $zaman= $tarih." ".$saat;

        $owner_id = $user_info["owner_id"];

        $create_user = $user_info["id"];

        $create_date = date("Y-m-d H:i:s");


        if($conn == false){

            $db_conn = $this->getConnection();
        }else{

            $db_conn = $conn;
        }

        if($user_id == 0){

            $bildirim_sql = "";

            if($conn == false){

                $sql = "SELECT id , owner_id  FROM users WHERE  owner_id = ? and web = ? and remove = ? and id != ? ";
                $query = $db_conn->prepare($sql);
                $query->execute([$user_info["owner_id"], 1 ,0,$create_user ]);
                $users = $query->fetchAll(PDO::FETCH_ASSOC);

            }else{

                $sql = "SELECT id , owner_id  FROM users WHERE   web = ? and remove = ?  ";
                $query = $db_conn->prepare($sql);
                $query->execute([1 ,0]);
                $users = $query->fetchAll(PDO::FETCH_ASSOC);
            }



            if($users){

                foreach ($users as $key => $val){

                    $user_id_no = $val["id"];

                    if($conn != false){
                        $owner_id =  $val["owner_id"];
                    }

                    $bildirim_sql.="INSERT INTO bildirimler SET     
                                                bildirim_mesaj = '{$bildirim_mesaj}' , 
                                                bildirim_baslik = '{$bildirim_baslik}',
                                                bildirim_icon = '{$bildirim_icon}',
                                                tip = {$tip} , 
                                                url = '{$url}' , 
                                                tarih = '{$tarih}',
                                                saat = '{$saat}',
                                                zaman = '{$zaman}',
                                                owner_id = {$owner_id},
                                                created_user = {$create_user},
                                                created_date = '{$create_date}',
                                                user_id = {$user_id_no}, 
                                                islem_id = {$islem_id};";
                }

            }

            if($bildirim_sql != ""){


                $query = $db_conn->prepare($bildirim_sql);

                return $query->execute();
            }

            return false;



        }else if($user_id > 0){


            $bildirim_sql="INSERT INTO bildirimler SET     
                                                bildirim_mesaj = '{$bildirim_mesaj}' , 
                                                bildirim_baslik = '{$bildirim_baslik}',
                                                bildirim_icon = '{$bildirim_icon}',
                                                tip = {$tip} , 
                                                url = '{$url}' , 
                                                tarih = '{$tarih}',
                                                saat = '{$saat}',
                                                zaman = '{$zaman}',
                                                owner_id = {$owner_id},
                                                created_user = {$create_user},
                                                created_date = '{$create_date}',
                                                update_date = '{$create_date}',
                                                user_id = {$user_id} , islem_id = {$islem_id}";

            $query = $db_conn->prepare($bildirim_sql);

            $query->execute();

            return $db_conn->lastInsertId();

        }

    }


    public function gorevEkle($controller, $gorev_adi , $gorev_kod , $gorev_aciklamasi = "" ,  $gorev_detay = ""){

        $user_info = $controller->getUserInfo();
        $owner_id = $user_info["owner_id"];
        $create_user = $user_info["id"];
        $create_date = date("Y-m-d H:i:s");
        $uniqid = uniqid();

        $bildirim_sql="INSERT INTO gorev_yoneticisi SET     
gorev_adi = ? ,
gorev_aciklamasi = ? ,
gorev_kod = ? ,
gorev_detay = ? ,
owner_id = ? ,
created_date = ? ,
created_user = ? ,
gorev_uniq_kod = ? 
";

        $query = $this->getConnection()->prepare($bildirim_sql);

        $query->execute([
            $gorev_adi,
            $gorev_aciklamasi,
            $gorev_kod,
            $gorev_detay,
            $owner_id,
            $create_date,
            $create_user,
            $uniqid
        ]);

        return $this->getConnection()->lastInsertId();
    }


    public function bildirimGuncelle($bildirim_id  , $controller, $bildirim_baslik,$bildirim_mesaj, $tip,  $tarih, $saat,  $islem_id = 0, $bildirim_icon = 'keyboard_arrow_right')
    {

        $user_info = $controller->getUserInfo();
        $url = "";
        $zaman= $tarih." ".$saat;
        $owner_id = $user_info["owner_id"];
        $update_user = $user_info["id"];
        $update_date = date("Y-m-d H:i:s");
        $user_id = $user_info["id"];

        $bildirim_sql="UPDATE bildirimler SET     
                                                bildirim_mesaj = '{$bildirim_mesaj}' , 
                                                bildirim_baslik = '{$bildirim_baslik}',
                                                bildirim_icon = '{$bildirim_icon}',
                                                tip = {$tip} , 
                                                url = '{$url}' , 
                                                tarih = '{$tarih}',
                                                saat = '{$saat}',
                                                zaman = '{$zaman}',
                                                owner_id = {$owner_id},
                                                updated_user = {$update_user},
                                                update_date = '{$update_date}',
                                                islem_id = {$islem_id} , goruntuleme = 0 WHERE id = ? and user_id = ? ";

        $query = $this->getConnection()->prepare($bildirim_sql);
        $query->execute([$bildirim_id,$user_id]);
        return $this->getConnection()->lastInsertId();
    }


    public function bildirimIptal($bildirim_id  , $controller)
    {
        $user_info = $controller->getUserInfo();
        $user_id = $user_info["id"];
        $bildirim_sql="UPDATE bildirimler SET  remove = 1 WHERE id = ? and user_id = ? ";
        $query = $this->getConnection()->prepare($bildirim_sql);
        $query->execute([$bildirim_id,$user_id]);
        return $this->getConnection()->lastInsertId();
    }


    public function bildirimlerIptal($controller , $tip , $islem_id )
    {


        $user_info = $controller->getUserInfo();


            $bildirim_sql="UPDATE bildirimler SET  remove = 1 WHERE user_id = ? and tip  = ?  and islem_id = ? and owner_id = ?  ";
            $query = $this->getConnection()->prepare($bildirim_sql);
            $query->execute([$user_info["id"],$tip,$islem_id,$user_info["owner_id"]]);
            return $this->getConnection()->lastInsertId();


    }

}
