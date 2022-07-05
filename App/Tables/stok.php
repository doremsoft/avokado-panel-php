<?php
/**
 *
 * @author Doğuş DİCLE
 */

class stok {

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
            'stok_barkod_no'=>NULL,
            'stok_kod'=>NULL,
            'stok_ozel_kod'=>NULL,
            'stok_adi'=>NULL,
            'stok_cinsi'=>NULL,
            'stok_birimi'=>NULL,
            'stok_sinif'=>NULL,
            'stok_grup'=>NULL,
            'stok_resim'=>NULL,
            'stok_adet'=>NULL,
            'stok_ozel_urun_adet'=>NULL,
            'stok_min_seviyesi'=>NULL,
            'stok_max_seviyesi'=>NULL,
            'stok_alis_fiyati'=>NULL,
            'stok_satis_fiyati'=>NULL,
            'stok_max_iskontolu_satis_fiyati'=>NULL,
            'stok_kdv_oran'=>NULL,
            'stok_kdv_detay'=>NULL,
            'stok_detayi'=>NULL,
            'stok_create_id'=>NULL,
            'last_val'=>NULL,
            'stok_kdv_dahil_satis_fiyati'=>NULL,
            'stok_fiyat_vergi_durum'=>NULL,
            'aktif'=>NULL,
            'stok_standart_adet'=>NULL,
            'stok_doviz'=>NULL,
            'stok_satis_iskonto_oran'=>NULL,
            'stok_alim_iskonto_oran'=>NULL,
            'stok_seo_url'=>NULL,
            'stok_parent_id'=>NULL,
            'stok_marka_id'=>NULL,
            'stok_tipi'=>NULL,
            'stok_varyant_adi'=>NULL,
            'stok_perakende_satis'=>NULL,
            'stok_web_satis'=>NULL,
            'stok_portal_satis'=>NULL,
            'stok_varyant_deger'=>NULL,
            'stok_parent_stok_kod'=>NULL,
            'sanal_stok'=>NULL,
            'onemli'=>NULL,
            'paket_stok'=>NULL,
            'bayi_alis_fiyati1'=>NULL,
            'bayi_alis_fiyati2'=>NULL,
            'stok_islem_kod'=>NULL,
            'stok_image_type'=>NULL,
            'stok_web_title'=>NULL,
            'stok_web_description'=>NULL,
            'stok_alim_doviz'=>NULL,];

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

