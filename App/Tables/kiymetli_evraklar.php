<?php
/**
 *
 * @author Doğuş DİCLE
 */

class kiymetli_evraklar {

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
            'evrak_tur'=>NULL,
            'evrak_son_odeme_tarihi'=>NULL,
            'evrak_tip'=>NULL,
            'evrak_olusturma_tarihi'=>NULL,
            'evrak_bedeli'=>NULL,
            'evrak_detay'=>NULL,
            'evrak_cari_id'=>NULL,
            'evrak_arsiv_no'=>NULL,
            'evrak_muhattap_banka'=>NULL,
            'odeme_durum'=>NULL,
            'kadise_yeri'=>NULL,
            'evrak_not'=>NULL,
            'iptal_mesaj'=>NULL,];

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

