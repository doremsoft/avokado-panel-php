<?php
/**
 *
 * @author Doğuş DİCLE
 */

class yazilimlar {

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
            'serial_code'=>NULL,
            'takma_adi'=>NULL,
            'master_parakende_cari_hesap_id'=>NULL,
            'cikis_yapacagi_depo_id'=>NULL,
            'cihaz_uniq_id'=>NULL,
            'kasa_id'=>NULL,
            'device_status'=>NULL,
            'device_type'=>NULL,
            'device_uniq_code'=>NULL,
            'reload_date'=>NULL,
            'pos_hesap_id'=>NULL,
            'pos_hesap_banka_id'=>NULL,];

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

