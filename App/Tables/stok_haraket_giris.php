<?php
/**
 *
 * @author Doğuş DİCLE
 */

class stok_haraket_giris {

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
            'giris_tarih'=>NULL,
            'cari_id'=>NULL,
            'giris_evrak_no'=>NULL,
            'seri_no'=>NULL,
            'stok_id'=>NULL,
            'adet'=>NULL,
            'alis_fiyati'=>NULL,
            'kdv_oran'=>NULL,
            'iskonto'=>NULL,
            'depo'=>NULL,
            'raf'=>NULL,
            'goz'=>NULL,
            'ozel_urun'=>NULL,
            'ozel_urun_durum'=>NULL,
            'ic_transfer'=>NULL,
            'alim_evrak_id'=>NULL,
            'doviz'=>NULL,
            'doviz_kur'=>NULL,
            'indirim_tutari'=>NULL,
            'vergi_tutari'=>NULL,];

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

