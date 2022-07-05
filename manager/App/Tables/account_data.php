<?php
/**
 *
 * @author Doğuş DİCLE
 */

class account_data {

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
            'account_no'=>NULL,
            'server_connection_protocol'=>NULL,
            'server'=>NULL,
            'api_server_connection_protocol'=>NULL,
            'api_server_url'=>NULL,
            'db_name'=>NULL,
            'account_nick_name'=>NULL,
            'create_user'=>NULL,
            'update_user'=>NULL,
            'create_date'=>NULL,
            'account_type'=>NULL,
            'server_ip'=>NULL,
            'cari_id'=>NULL,
            'db_version'=>NULL,
            'referans_id'=>NULL,
            'secure_key'=>NULL,
            'account_server_name'=>NULL,
            'ad'=>NULL,
            'soyad'=>NULL,
            'gsm'=>NULL,
            'mail'=>NULL,
            'adres'=>NULL,
            'unvan'=>NULL,
            'hesap_turu'=>NULL,];

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

