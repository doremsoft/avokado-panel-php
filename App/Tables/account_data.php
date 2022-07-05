<?php
/**
 * Description of cari
 *
 * @author Doğuş Dicle
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

    public function __construct($args) {

        $this->index = "id";

        $this->cols = [
            'id' => NULL,
            'stok_sinif_adi' => NULL,

        ];

        $this->locked_cols = [];

        $this->selectWhere['remove'] = ['=', 0];
        $this->selectWhere['owner_id'] = ['=', $args['owner_id']];
      

        $this->insert = [
            'owner_id' => $args['owner_id'],
            'remove' => 0,
            'created_date' => date("Y-m-d H:i:s"),
            'created_user'=>$args['id']
        ];

        $this->update = [
            'owner_id' => $args['owner_id'],
            'update_date' => date("Y-m-d H:i:s"),
            'updated_user'=>$args['id']
        ];
    }

}