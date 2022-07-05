<?php
/**
 *
 * @author Doğuş DİCLE
 */

class kasa_haraket {

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
            'id'=>NULL,
            'kasa_id'=>NULL,
            'kasa_haraket_tip'=>NULL,
            'kasa_haraket_cari_id'=>NULL,
            'kasa_haraket_tutar'=>NULL,
            'kasa_haraket_tarih'=>NULL,
            'kasa_haraket_not'=>NULL,
            'satis_evrak_id'=>NULL,
            'sabit_gider_id'=>NULL,
            'iptal_mesaji'=>NULL,];

        $this->locked_cols = [];

        $this->selectWhere['remove'] = ['=', 0];
        $this->selectWhere['owner_id'] = ['=', $args['owner_id']];
      
        $this->insert = [
            'owner_id' => $args['owner_id'],
            'remove' => 0,
            'created_date' => date("Y-m-d H:i:s")
        ];

        $this->update = [
            'owner_id' => $args['owner_id'],
            'update_date' => date("Y-m-d H:i:s")
        ];
    }

}

