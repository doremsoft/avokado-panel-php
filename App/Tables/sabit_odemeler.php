<?php
/**
 *
 * @author Doğuş DİCLE
 */

class sabit_odemeler {

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
            'tutar'=>NULL,
            'gun'=>NULL,
            'aciklama'=>NULL,
            'ondecen_bildirim'=>NULL,
            'ayni_gun_bildirim'=>NULL,
            'tur'=>NULL,
            'baslama_tarihi'=>NULL,
            'bitis_tarihi'=>NULL,
            'baslik'=>NULL,
            'tutar_doviz'=>NULL,
            'bildirim_durum'=>NULL,
            'ertesi_gun_bildirim'=>NULL,];

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

