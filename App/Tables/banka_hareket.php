<?php
/**
 *
 * @author Doğuş DİCLE
 */

class banka_hareket {

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
            'banka_id'=>NULL,
            'banka_hesap_id'=>NULL,
            'banka_haraket_tip'=>NULL,
            'banka_haraket_cari_id'=>NULL,
            'banka_haraket_tutar'=>NULL,
            'banka_haraket_tarih'=>NULL,
            'banka_haraket_baslik'=>NULL,
            'satis_evrak_id'=>NULL,
            'sabit_gider_id'=>NULL,
            'p_hatali_id'=>NULL,
            'alim_evrak_id'=>NULL,];

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

