<?php

namespace Dipa\Db;

use PDO;
use PDOException;
use stdClass;

/**
 *
 * @author dogus
 */
class Dbsync {
    /*
     * Veritabanları bağlantı adresleri ve şifreleri
     * 
     * $db1  = [
     * 'host'=>'localhost',
     * 'dbname'=> 'dbname',
     * 'username'=> 'root',
     * 'password'=> ''
     * ];
     */

    private $one_db_connection_config;
    private $two_db_connection_config;

    /*
     * PDO Bağlantı nesneleri
     */
    private $one_db_connection;
    private $two_db_connection;
    private $one_db_tables = [];
    private $two_db_tables = [];
    private $one_db_table_columns = [];
    private $two_db_table_columns = [];
    private $create_tables = [];
    private $delete_tables = [];
    private $remove_table;
    private $remove_column;
    private $add_columns = [];
    private $modify_columns = [];
    private $delete_columns = [];
    private $execute_sql = [
        'create_tables' => false,
        'delete_tables' => false,
        'add_columns' => false,
        'modify_columns' => false,
        'delete_columns' => false
    ];
    private $sql_string = "";
    private $necessary = false;
    private $last_sql = "";
    private $api = false;
    private $api_db_two_tables_json = "";

    public function init($one_db_data, $two_db_data, $data_sync = false, $remove_table = false, $remove_column = false) {

        $this->remove_table = $remove_table;
        $this->remove_column = $remove_column;
        $this->one_db_connection_config = $one_db_data;
        $this->two_db_connection_config = $two_db_data;
        $this->connectDbOne();
        $this->connectDbTwo();
    }

    public function apiInit($one_db_data, $db_two_json_data) {

        $this->api = true;
        $this->remove_table = false;
        $this->remove_column = false;
        $this->one_db_connection_config = $one_db_data;


        $this->api_db_two_tables_json = $db_two_json_data;

        $this->connectDbOne();
    }

    private function dbConnect($config) {

        try {

            $database_connection = new \PDO(
                    'mysql:host=' . $config["host"] . ';dbname=' .
                    $config["dbname"] . '', $config["username"], $config["password"]);
            $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $database_connection;
        } catch (PDOException $e) {

            echo "Veritabanı Bağantı Hatası!:" . $e->getMessage();

            die();
        }
    }

    private function connectDbOne() {

        $this->one_db_connection = $this->dbConnect($this->one_db_connection_config);
    }

    private function connectDbTwo() {

        $this->two_db_connection = $this->dbConnect($this->two_db_connection_config);
    }

    private function setOneDbTables() {

        $statement = $this->one_db_connection->prepare("SHOW TABLES");

        $statement->execute();

        $tables = $statement->fetchAll(PDO::FETCH_NUM);

        if ($tables) {

            foreach ($tables as $key) {

                $columns_data = $this->getColumns($this->one_db_connection, $key[0]);

                $this->one_db_tables[$key[0]]['columns'] = $columns_data;

                if ($columns_data) {

                    foreach ($columns_data as $c_key) {

                        $this->one_db_table_columns[$key[0]][$c_key["Field"]] = $c_key;
                    }
                }

                $this->one_db_tables[$key[0]]['create_sql'] = $this->getCreateTableSql($this->one_db_connection, $key[0]);
            }
        }
    }

    private function setTwoDbTables() {


        if ($this->api) {

            $this->setTwoDbApiTables();
        } else {

            $sql = "SHOW TABLES";

            $statement = $this->two_db_connection->prepare($sql);

            $statement->execute();

            $tables = $statement->fetchAll(PDO::FETCH_NUM);

            if ($tables) {

                foreach ($tables as $key) {

                    $columns_data = $this->getColumns($this->two_db_connection, $key[0]);

                    $this->two_db_tables[$key[0]]['columns'] = $columns_data;

                    if ($columns_data) {

                        foreach ($columns_data as $c_key) {

                            $this->two_db_table_columns[$key[0]][$c_key["Field"]] = $c_key;
                        }
                    }

                    $this->two_db_tables[$key[0]]['create_sql'] = $this->getCreateTableSql($this->two_db_connection, $key[0]);
                }
            }
        }
    }

    private function setTwoDbApiTables() {

        $table_data = json_decode($this->api_db_two_tables_json, true);

        foreach ($table_data as $key => $val) {



            $this->two_db_tables[$key]['columns'] = $val;


            $columns_data = $val;

            if ($columns_data) {

                $iia = 0;

                foreach ($columns_data as $c_key => $c_val) {


                    foreach ($c_val as $value_c) {

                        $unsigned = false;

                        $value_c["Type"] = strtolower($value_c["Type"]);

                        
                            $field_type_detect = substr($value_c['Type'], 0, 4);
                    

                        if ($value_c["Type"] == "int") {

                          
                        } else if ($value_c["Type"] == "datetime") {
                            
                            $value_c["size"] = 0;
                            
                        } elseif ($value_c["Type"] == "int unsigned") {

                            $unsigned = true;

                            $value_c["Type"] = "int";
                            
                        }elseif ($field_type_detect == "text") {

                            $value_c["Type"] = "text";
                        }
                        
                        


                        if ($value_c["Type"] != "datetime" && $value_c["Type"] != "text") {
                            
                            $value_c["Type"] = $value_c["Type"] . "(" . $value_c["size"] . ")";
                        }


                        if ($unsigned == true) {

                            $value_c["Type"] = $value_c["Type"] . " unsigned";
                        }



                        $this->two_db_table_columns[$key][$value_c["Field"]] = $value_c;

                        unset($this->two_db_table_columns[$key][$value_c["Field"]]["size"]);

                        if ($value_c["Default"] == 'NULL') {



                            $this->two_db_table_columns[$key][$value_c["Field"]]["Default"] = NULL;
                        }
                    }
                }
            }
        }
    }

    private function getColumns($conn, $table_name) {

        $q = $conn->prepare("DESCRIBE {$table_name}");

        $q->execute();

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCreateTableSql($conn, $table_name) {

        $q = $conn->query("SHOW CREATE TABLE $table_name");

        $row = $q->fetch(PDO::FETCH_NUM);

        $ifnotexists = str_replace('CREATE TABLE', "\n".'CREATE TABLE IF NOT EXISTS', $row[1]);

        return $ifnotexists . ";";
    }

    private function compareData() {
        /*
         * ikinci db de olup 1. db de olmayan tablolar
         */
        if ($this->remove_table) {

            foreach ($this->two_db_tables as $two_table_name => $val2) {

                if (!array_key_exists($two_table_name, $this->one_db_tables)) {

                    array_push($this->delete_tables, $two_table_name);
                }
            }
        }

        /*
         * Birini db de olup 2. db de olmayan tablolar
         */

        foreach ($this->one_db_tables as $table_name => $val) {

            if (array_key_exists($table_name, $this->two_db_tables)) {

                if (!array_key_exists($table_name, $this->delete_tables)) {

                    $this->compareColumns($table_name, $this->one_db_table_columns[$table_name], $this->two_db_table_columns[$table_name]);
                }
            } else {

                array_push($this->create_tables, $table_name);
            }
        }



        return $this;
    }

    private function compareColumns($table_name, $table1_col, $table2_col) {

        // echo "<h2>" . $table_name . "</h2>";

        foreach ($table1_col as $key => $val) {

            if (isset($table2_col)) {



                if (array_key_exists($key, $table2_col)) {

                    /// echo "Sütun Var:" . $key . "<br>";

                    $this->twoColumnsDiff($table_name, $key);
                } else {

                    $this->add_columns[$table_name][] = $key;

                    //echo "Sütun Yok:" . $key . "<br>";
                }
            } else {

                $this->add_columns[$table_name][] = $key;
            }
        }

        if ($this->remove_column) {

            foreach ($table2_col as $key2 => $val2) {

                if (!array_key_exists($key2, $table1_col)) {

                    //echo "<span style='color:red;'>Fazla Sütun Var:" . $key2 . "</span><br>";

                    $this->delete_columns[$table_name][] = $key2;
                }
            }
        }
    }

    private function twoColumnsDiff($table, $columun_name) {


        $result = array_diff($this->one_db_table_columns[$table][$columun_name], $this->two_db_table_columns[$table][$columun_name]);




        if (!empty($result)) {

/*
            echo $table . "<br>";

            echo '<pre>' . var_export($this->one_db_table_columns[$table][$columun_name], true) . '</pre>';

            echo '<pre>' . var_export($this->two_db_table_columns[$table][$columun_name], true) . '</pre>';


            echo '<pre>' . var_export($result, true) . '</pre>';
            echo "<hr>";
*/
            $this->modify_columns[$table][] = $columun_name;
        }
    }

    /*
     *  Sql Sorguları
     */

    private function prepareSql() {

        $this->setCreateTableSql();
        //$this->setDeleteTableSql();
        $this->setAddColumnSql();
        $this->setModifyColumnSql();
    }

    private function setCreateTableSql() {

        if (!empty($this->create_tables)) {

            foreach ($this->create_tables as $key => $value) {

                $this->execute_sql["create_tables"][] = $this->one_db_tables[$value]["create_sql"];
            }
        }
    }

    private function setDeleteTableSql() {

        if (!empty($this->delete_tables)) {

            foreach ($this->delete_tables as $key => $value) {

                $this->execute_sql["delete_tables"][] = "DROP TABLE {$value};";
            }
        }
    }

    /*
     *     
     * 'add_columns' => false,
      'modify_columns' => false,
      'delete_columns' => false
     */

    private function setAddColumnSql() {

        if (!empty($this->add_columns)) {

            foreach ($this->add_columns as $key => $value) {

                foreach ($value as $keys) {

                    $column_data = $this->one_db_table_columns[$key][$keys];



                    $add_colum_params = "";
                    $default_is_string = false;
                    $null_string = "";
                    $default_string = "";




                    //Sütun tipi
                    $add_colum_params .= " " . $column_data['Type'] . " ";


                    if ($column_data['Null'] == "NO") {

                        $null_string = "NOT NULL";
                    } else if ($column_data['Null'] == "YES") {

                        $null_string = "NULL";
                    }



                    $field_type_detect = substr($column_data['Type'], 0, 4);

                    if (
                            $field_type_detect == "varc" ||
                            $field_type_detect == "text" ||
                            $field_type_detect == "date") {

                        $default_is_string = true;
                    }



                    if ($column_data['Default'] != "" || !empty($column_data['Default']) || $column_data['Default'] != NULL) {


                        $default_string = " DEFAULT ";


                        if ($default_is_string) {


                            $default_string .= " '" . $column_data['Default'] . "' ";
                        } else {

                            $default_string .= " " . $column_data['Default'] . " ";
                        }
                    }



                    $add_colum_params .= $null_string . $default_string;

                    $writesql = <<< EOT
ALTER TABLE {$key} ADD COLUMN  {$column_data['Field']} {$add_colum_params};
EOT;

                    $this->execute_sql["add_columns"][] = $writesql;
                }
            }
        }
    }

    private function setModifyColumnSql() {

        if (!empty($this->modify_columns)) {

            foreach ($this->modify_columns as $key => $value) {

                foreach ($value as $keys) {

                    $column_data = $this->one_db_table_columns[$key][$keys];

                    $add_colum_params = "";
                    $default_is_string = false;
                    $null_string = "";
                    $default_string = "";


                    //Sütun tipi
                    $add_colum_params .= " " . $column_data['Type'] . " ";


                    if ($column_data['Null'] == "NO") {

                        $null_string = "NOT NULL";
                    } else if ($column_data['Null'] == "YES") {

                        $null_string = "NULL";
                    }



                    $field_type_detect = substr($column_data['Type'], 0, 4);

                    if (
                            $field_type_detect == "varc" ||
                            $field_type_detect == "text" ||
                            $field_type_detect == "date") {

                        $default_is_string = true;
                    }



                    if ($column_data['Default'] != "" || !empty($column_data['Default']) || $column_data['Default'] != NULL) {


                        $default_string = " DEFAULT ";


                        if ($default_is_string) {


                            $default_string .= " '" . $column_data['Default'] . "' ";
                        } else {

                            $default_string .= " " . $column_data['Default'] . " ";
                        }
                    }


                    $add_colum_params .= $null_string . $default_string;

                    $writesql = <<< EOT
ALTER TABLE {$key} MODIFY COLUMN  {$column_data['Field']} {$add_colum_params};
EOT;

                    $this->execute_sql["add_columns"][] = $writesql;
                }
            }
        }
    }

    private function setDeleteColumnSql() {



        foreach ($this->modify_columns as $key => $value) {

            foreach ($value as $keys) {

                $column_data = $this->two_db_table_columns[$key][$keys];

                $writesql = <<< EOT
ALTER TABLE {$key} DROP COLUMN  {$column_data['Field']};
EOT;

                $this->execute_sql["delete_columns"][] = $writesql;
            }
        }
    }

    /*
     * Getter
     */

    public function getOneDbTables() {

        return $this->one_db_tables;
    }

    public function getOneDbTableColumns() {


        return $this->one_db_table_columns;
    }

    public function getTwoDbTables() {

        return $this->two_db_tables;
    }

    public function getTwoDbTableColumns() {


        return $this->two_db_table_columns;
    }

    public function getNotExitsTable() {


        return $this->create_tables;
    }

    public function getCompareReport() {

        echo '<h2>Eklenecek Tablolar</h2>';

        echo '<pre>' . var_export($this->create_tables, true) . '</pre>';

        echo '<h2>Silinecek Tablolar</h2>';

        echo '<pre>' . var_export($this->delete_tables, true) . '</pre>';

        echo '<h2>Eklenecek Sütunlar</h2>';

        echo '<pre>' . var_export($this->add_columns, true) . '</pre>';

        echo '<h2>Silinecek Sütunlar</h2>';

        echo '<pre>' . var_export($this->delete_columns, true) . '</pre>';
        echo '<h2>Düzenlenecek Sütunlar</h2>';


        echo '<pre>' . var_export($this->modify_columns, true) . '</pre>';
    }

    public function getSql() {

        return $this->sql_string;
    }

    public function getSqlArray() {

        return $this->execute_sql;
    }

    public function printSql() {


        $sql = "";


        foreach ($this->execute_sql as $key => $val) {


            if (is_array($val)) {

                foreach ($val as $s) {

                    $sql .= $s . " <br><br>";
                }
            }
        }





        return $sql;
    }

    private function sqlToString() {

        $sql = "";


        foreach ($this->execute_sql as $key => $val) {


            if (is_array($val)) {

                foreach ($val as $s) {

                    $sql .= $s;
                }
            }
        }


        $this->sql_string = $sql;
    }

    private function diffControl() {


        $control = 0;

        foreach ($this->execute_sql as $key => $value) {

            if ($value != false) {
                $control = 1;
            }
        }

        $this->necessary = $control;
    }

    private function isDiff() {

        return $this->necessary;
    }

    public function compare() {

        $this->setOneDbTables();

        $this->setTwoDbTables();
        /*
         * İki db arasında tabloları kıyaslıyoruz
         * birinci db de olup 2 db de yoksa create table
         * birinci db de yok 2 db de var ise 
         */
        $this->compareData();

        $this->prepareSql();

        $this->diffControl();

        $this->sqlToString();

        return $this->isDiff();
    }

    public function execute() {


        $result = true;

        foreach ($this->execute_sql as $key => $val) {

            if (is_array($val)) {

                foreach ($val as $s) {

                    $this->last_sql = $s;

                    try {

                        $update_query = $this->two_db_connection->query($s);

                        if (!$update_query) {

                            $result = false;
                        }
                    } catch (Exception $ex) {
                        
                    } finally {

                        if ($result == false) {

                            return false;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function getLastSql() {

        return $this->last_sql;
    }

    public function __destruct() {

        $this->one_db_connection_config = NULL;
        $this->two_db_connection_config = NULL;
        $this->one_db_connection = NULL;
        $this->two_db_connection = NULL;
        $this->one_db_tables = NULL;
        $this->two_db_tables = NULL;
        $this->one_db_table_columns = NULL;
        $this->two_db_table_columns = NULL;
        $this->create_tables = NULL;
        $this->delete_tables = NULL;
        $this->remove_table = NULL;
        $this->remove_column = NULL;
        $this->add_columns = NULL;
        $this->modify_columns = NULL;
        $this->delete_columns = NULL;
    }

}
