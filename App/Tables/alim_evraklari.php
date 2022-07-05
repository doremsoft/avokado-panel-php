<?php
/**
 *
 * @author Doğuş DİCLE
 */

class alim_evraklari {

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
            'evrak_no'=>NULL,
            'tarih'=>NULL,
            'cari_id'=>NULL,
            'evrak_tur'=>NULL,
            'ara_toplam'=>NULL,
            'vergi_toplam'=>NULL,
            'indirim_toplam'=>NULL,
            'genel_toplam'=>NULL,
            'evrak_detayi'=>NULL,
            'fatura_bilgileri'=>NULL,
            'vade_gun'=>NULL,
            'vade_tarih'=>NULL,
            'unvan'=>NULL,
            'vergino'=>NULL,
            'vergidaire'=>NULL,
            'vergiadres'=>NULL,];

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

