<?php

namespace Dipa\Db;

use \Dipa\App;
use PDO;
use PDOException;
use stdClass;

/**
 *
 * @author Doğuş DİCLE
 * 
 *   'lib'=>'pdo',
 *   'host' => 'localhost',
 *   'driver' => 'mysql',
 *   'database' => 'test',
 *   'username' => 'dogus',
 *   'password' => '1234',
 *   'charset' => 'utf8',
 *   'collation' => 'utf8_general_ci',
 *   'prefix' => ''
 */
class Model {

    private $dbConnection;
    private $dbEngine;
    protected $dbConfig;
    private $dbdriver;
    private $connLib;

    public function __construct($lib = null, $config = null) {
        $this->init($lib, $config);
    }

    public function init($lib = null, $config = null) {

        $this->connLib = $lib == null ? App::getConfig("db", "lib") : $lib;

        $this->dbdriver = $config == null ? App::getConfig("db", "driver") : $config["driver"];

        $this->dbConfig = $config == null ? App::getConfig("db") : $config;

        switch ($this->connLib) {
            case "pdo":
                $this->pdoConnect();
                break;
            case "mysqli":
                $this->mysqliConnect();
                break;
            case "pdox":
                $this->dbEngine = new \Buki\Pdox($this->dbConfig);
                break;
            case null:
                $this->pdoConnect();
                break;
            default:
                $this->pdoConnect();
                break;
        }

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
            die();
        }
    }

    public function resetConnection($config = null) {
        $this->dbConnection = NULL;

        $this->init(NULL, $config);
    }

    protected function mysqliConnect() {

        $conn = new mysqli($this->dbConfig["host"], $this->dbConfig["username"], $this->dbConfig["password"]);

        if ($conn->connect_error) {

            die("Mysqli Bağlantı Hatası: " . $conn->connect_error);
        } else {

            $this->dbConnection = $conn;
            $conn = null;
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

}
