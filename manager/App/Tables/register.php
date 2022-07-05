<?php
/**
 *
 * @author Doğuş DİCLE
 */

class register {

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
            'ad'=>NULL,
            'soyad'=>NULL,
            'gsm'=>NULL,
            'mail'=>NULL,
            'adres'=>NULL,
            'vergi_unvan'=>NULL,
            'create_date'=>NULL,
            'create_user'=>NULL,
            'update_user'=>NULL,
            'mail_code'=>NULL,
            'type'=>NULL,
            'last_mail'=>NULL,];

        $this->locked_cols = [];

        $this->selectWhere['remove'] = ['=', 0];
       // $this->selectWhere['owner_id'] = ['=', $args['owner_id']];
      
        $this->insert = [
            //'owner_id' => $args['owner_id'],
            'remove' => 0,
            'create_date' => date("Y-m-d H:i:s")
        ];

        $this->update = [
            //'owner_id' => $args['owner_id'],
            'update_date' => date("Y-m-d H:i:s")
        ];
    }

}

