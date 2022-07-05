<?php
/**
 *
 * @author Doğuş DİCLE
 */

class cari {

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
            'cari_kod'=>NULL,
            'cari_adi'=>NULL,
            'cari_telefon'=>NULL,
            'cari_gsm'=>NULL,
            'cari_logo'=>NULL,
            'cari_detay'=>NULL,
            'cari_mail'=>NULL,
            'cari_adres'=>NULL,
            'rfid_id'=>NULL,
            'cari_gsm2'=>NULL,
            'cari_yetkili'=>NULL,
            'cari_vade_gun'=>NULL,
            'cari_aktif'=>NULL,
            'cari_kredi_limit'=>NULL,
            'cari_turu'=>NULL,
            'cari_vergi_no'=>NULL,
            'cari_vergi_daire'=>NULL,
            'cari_image'=>NULL,
            'cari_dogum_gunu'=>NULL,
            'cari_hesap_turu'=>NULL,
            'cari_hesap_grubu'=>NULL,
            'web_session_kod'=>NULL,
            'cari_web_aktif'=>NULL,
            'web_kullanici_adi'=>NULL,
            'web_sifre'=>NULL,
            'bayi_user_id'=>NULL,
            'web_avatar'=>NULL,];

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

