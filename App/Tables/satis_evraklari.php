<?php
/**
 *
 * @author Doğuş DİCLE
 */

class satis_evraklari {

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
            'evrak_detayi'=>NULL,
            'vade_gun'=>NULL,
            'parakende_satis'=>NULL,
            'vade_tarih'=>NULL,
            'unvan'=>NULL,
            'vergino'=>NULL,
            'vergidaire'=>NULL,
            'vergiadres'=>NULL,
            'tahsil_durum'=>NULL,
            'uniq_id'=>NULL,
            'yazilim_serial'=>NULL,
            'islem_notu'=>NULL,
            'ip'=>NULL,
            'evrak_zamani'=>NULL,
            'paket_kod'=>NULL,
            'evrak_tutar'=>NULL,
            'indirim_toplam'=>NULL,
            'kdv_1'=>NULL,
            'kdv_8'=>NULL,
            'kdv_18'=>NULL,
            'kdv_toplam'=>NULL,
            'genel_toplam'=>NULL,
            'siparis_durumu'=>NULL,
            'siparis_kod'=>NULL,
            'siparis_fatura_kod'=>NULL,
            'kargo_kod'=>NULL,
            'kargo_takip_no'=>NULL,];

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

