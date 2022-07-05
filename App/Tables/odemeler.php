<?php
/**
 *
 * @author Doğuş DİCLE
 */

class odemeler {

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
            'cari_id'=>NULL,
            'odeme_tip'=>NULL,
            'odeme_tutar'=>NULL,
            'odeme_islem_id'=>NULL,
            'odeme_tarih'=>NULL,
            'odeme_mesaj'=>NULL,
            'alim_evrak_id'=>NULL,
            'iptal_mesaji'=>NULL,
            'etkisiz'=>NULL,
            'kiymetli_evrak'=>NULL,];

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

