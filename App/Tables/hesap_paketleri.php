<?php
/**
 *
 * @author Doğuş DİCLE
 */

class hesap_paketleri {

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
            'paket_adi'=>NULL,
            'paket_tutari'=>NULL,
            'paket_baslama_tarihi'=>NULL,
            'paket_bitis_tarihi'=>NULL,
            'ust_paket_id'=>NULL,
            'paket_tanimlama_1'=>NULL,
            'paket_tanimlama_2'=>NULL,
            'paket_tanimlama_3'=>NULL,
            'paket_ozellikleri'=>NULL,
            'aktif'=>NULL,
            'paket_aciklamasi'=>NULL,
            'hesap_no'=>NULL,
            'paket_key'=>NULL,];

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

