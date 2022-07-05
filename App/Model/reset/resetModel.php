<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class resetModel extends Dimodel {
    /*
     * Controller::$userInfo
     */
    /*
      function __construct()
      {
      // parent::__construct("pdo");
      // pdo - mysqli - pdox
      }
     */

    public function resetAllData() {

        $reset_sql = "TRUNCATE TABLE alim_evraklari;
TRUNCATE TABLE banka_hareket;
TRUNCATE TABLE banka_hesaplari;
TRUNCATE TABLE bankalar;
TRUNCATE TABLE cari;
TRUNCATE TABLE doviz;
TRUNCATE TABLE duyurular;
TRUNCATE TABLE indirilen_resimler;
TRUNCATE TABLE kasa_haraket;
TRUNCATE TABLE kasalar;
TRUNCATE TABLE kiymetli_evraklar;
TRUNCATE TABLE markalar;
TRUNCATE TABLE masa_kategorileri;
TRUNCATE TABLE masalar;
TRUNCATE TABLE odemeler;
TRUNCATE TABLE satis_evraklari;
TRUNCATE TABLE stok;
TRUNCATE TABLE stok_birimler;
TRUNCATE TABLE stok_change_listener;
TRUNCATE TABLE stok_depolar;
TRUNCATE TABLE stok_etiketler;
TRUNCATE TABLE stok_galeri;
TRUNCATE TABLE stok_gruplar;
TRUNCATE TABLE stok_haraket_cikis;
TRUNCATE TABLE stok_haraket_giris;
TRUNCATE TABLE stok_kategorileri;
TRUNCATE TABLE stok_raflar;
TRUNCATE TABLE stok_resimler;
TRUNCATE TABLE stok_siniflar;
TRUNCATE TABLE tagler;
TRUNCATE TABLE tahsilatlar;
TRUNCATE TABLE web_stok_detay;TRUNCATE TABLE yedekler;";

        $reset_query = $this->getConnection()->prepare($reset_sql);

        return $reset_query->execute();
    }

}
