<?php
/**
 *
 * @author Doğuş DİCLE
 */

class stok_haraket_cikis {

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
            'cikis_tarih'=>NULL,
            'cari_id'=>NULL,
            'cikis_evrak_no'=>NULL,
            'seri_no'=>NULL,
            'stok_id'=>NULL,
            'adet'=>NULL,
            'satis_fiyati'=>NULL,
            'kdv_oran'=>NULL,
            'iskonto'=>NULL,
            'indirim_tutari'=>NULL,
            'depo'=>NULL,
            'raf'=>NULL,
            'goz'=>NULL,
            'ozel_urun_id'=>NULL,
            'ic_transfer'=>NULL,
            'parakende_cikis'=>NULL,
            'parakende_yazilim_serial'=>NULL,
            'satis_evrak_id'=>NULL,
            'doviz'=>NULL,
            'doviz_kur'=>NULL,
            'anapara'=>NULL,
            'stokta_islem'=>NULL,
            'irsaliye_id'=>NULL,
            'irsaliye_no'=>NULL,
            'aciklama'=>NULL,
            'vergi_dahil_gosterim'=>NULL,
            'vergili_satis_fiyat'=>NULL,
            'vergi_durum'=>NULL,
            'paket_kod'=>NULL,
            'adet_etkisiz'=>NULL,
            'siparis_stok'=>NULL,];

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

