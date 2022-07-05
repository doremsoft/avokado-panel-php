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

    public function __construct($args=NULL) {

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
            'api_session_key'=>NULL,
            'h_nav'=>NULL,
            'offline_code'=>NULL,
            'offline_activate'=>NULL,
            'web'=>NULL,
            'mobile_api'=>NULL,
            'username'=>NULL,
            'mobile_api_session_key'=>NULL,
            'mobile_api_last_connection'=>NULL,];

        $this->locked_cols = [];

        $this->selectWhere['remove'] = ['=', 0];
       // $this->selectWhere['owner_id'] = ['=', $args['owner_id']];
      
        $this->insert = [
            //'owner_id' => $args['owner_id'],
            'remove' => 0,
            'created_date' => date("Y-m-d H:i:s")
        ];

        $this->update = [
            //'owner_id' => $args['owner_id'],
            'update_date' => date("Y-m-d H:i:s")
        ];
    }

}

