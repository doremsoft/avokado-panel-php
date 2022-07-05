<?php
/**
 *
 * @author Doğuş DİCLE
 */

class users {

    public $index;
    public $cols;
    public $locked_cols;
    public $insert;
    public $update;
    public $select;
    //Tablo ile ilgili veriler
    public $table_create_sql;
    public $table_create_time;
    public $table_last_update_time;

    public function __construct() {

        $this->index = "id";

        $this->cols = [
            'id'=>NULL,
            'password'=>NULL,
            'name'=>NULL,
            'surname'=>NULL,
            'phone'=>NULL,
            'email'=>NULL,
            'hash'=>NULL,
            'auths'=>NULL,
            'image'=>NULL,
            'admin'=>NULL,
            'gender'=>NULL,
            'api_permission'=>NULL,
            'api_last_connection'=>NULL,
            'api_session_key'=>NULL,];

        $this->locked_cols = [];

        $this->selectWhere['remove'] = ['=', 0];
       
      
        $this->insert = [
      
            'remove' => 0,
            'created_date' => date("Y-m-d H:i:s")
        ];

        $this->update = [
   
            'update_date' => date("Y-m-d H:i:s")
        ];
    }

}

