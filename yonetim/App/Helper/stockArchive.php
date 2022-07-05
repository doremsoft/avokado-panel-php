<?php

Class stockArchive {

    private $archive_db_configs;
    private $archive_db_connection;
    private $source_db_configs;
    private $source_db_connection;
    private $archive_table_name = "stok_arsiv";
    private $i = 0;
    private $select_stock_count = 0;
    private $stok_varyant_select = ""
            . "stok.id ,stok.stok_parent_id as s_pid ,stok.stok_barkod_no,
           stok.stok_kod,
           stok.stok_adi,
           stok.stok_birimi,
           stok.stok_doviz,
           stok.stok_kdv_oran,
           stok.stok_satis_fiyati,
           stok.stok_kdv_dahil_satis_fiyati,
           stok.stok_varyant_adi,
           stok.stok_varyant_deger,
           stok.stok_parent_stok_kod,"
            . "IF(stok.stok_parent_id=0,stok.stok_fiyat_vergi_durum,(select stok_fiyat_vergi_durum from stok where id = s_pid LIMIT 1)) AS stok_fiyat_vergi_durum , "
            . "IF(stok.stok_parent_id=0,stok.stok_kdv_oran,(select stok_kdv_oran from stok where id = s_pid LIMIT 1)) AS stok_kdv_oran , "
            . "IF(stok.stok_parent_id=0,stok.stok_doviz,(select stok_doviz from stok where id = s_pid LIMIT 1)) AS stok_doviz , "
            . "IF(stok.stok_parent_id=0,stok.stok_birimi,(select stok_birimi from stok where id = s_pid LIMIT 1)) AS stok_birimi , "
            . "IF(stok.stok_parent_id=0,stok.stok_adi,(select stok_adi from stok where id = s_pid LIMIT 1)) AS stok_adi ,"
            . "IF(stok.stok_parent_id=0,'',(select stok_kod from stok where id = s_pid LIMIT 1)) AS parent_stok_kod ";

    public function setArchive($archive_db) {

        $this->archive_db_configs = $archive_db;
    }

    public function init($archive_db) {

        $this->archive_db_configs = $archive_db;

        $this->archive_db_connection = $this->setArchiveDbConnection();
    }

    private function trbuyut($str) {
        $str = str_replace(array('i', 'ı', 'ü', 'ğ', 'ş', 'ö', 'ç'), array('İ', 'I', 'Ü', 'Ğ', 'Ş', 'Ö', 'Ç'), $str);
        return mb_strtoupper($str, 'utf-8');
    }

    private function trkucult($str) {
        $str = str_replace(array('İ', 'I', 'Ü', 'Ğ', 'Ş', 'Ö', 'Ç'), array('i', 'ı', 'ü', 'ğ', 'ş', 'ö', 'ç'), $str);
        return mb_strtolower($str, 'utf-8');
    }

    private function tr_strtolower($metin) {
        return mb_strtolower($metin, 'utf-8');
    }

    private function tr_strtoupper($metin) {
        return mb_strtoupper($metin, 'utf-8');
    }

    private function tr_ucfirst($metin) {
        $ilk = mb_substr($metin, 0, 1, 'utf-8');
        $kalan = mb_substr($metin, 1, strlen($metin), 'utf-8');
        return $this->trbuyut($ilk) . $this->trkucult($kalan);
    }

    private function setArchiveDbConnection() {

        try {



            $database_connection = new PDO(
                    'mysql:host=' . $this->archive_db_configs["host"] . ';dbname=' .
                    $this->archive_db_configs["dbname"] . '', $this->archive_db_configs["username"], $this->archive_db_configs["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);




            return $database_connection;
        } catch (PDOException $e) {

            echo "Veritabanı Bağantı Hatası!:" . $e->getMessage();

            die();
        }
    }

    private function setSourceDbConnection() {

        try {


            $database_connection = new PDO(
                    'mysql:host=' . $this->source_db_configs["host"] . ';dbname=' .
                    $this->source_db_configs["dbname"] . '', $this->source_db_configs["username"], $this->source_db_configs["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));




            $database_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $database_connection;
        } catch (PDOException $e) {

            echo "Veritabanı Bağantı Hatası!:" . $e->getMessage();

            die();
        }
    }

    private function ifExits($barcode, $stock_code) {

        if (trim($barcode) == "" && trim($stock_code) == "") {

            return true;
        } else if (trim($stock_code) == "" && trim($barcode) != "") {

            $sql = "SELECT id FROM " . $this->archive_table_name . " WHERE barcode = ?  and remove = 0";

            $query = $this->archive_db_connection->prepare($sql);


            $query->execute([$barcode]);
        } else if (trim($barcode) == "" && trim($stock_code) != "") {

            $sql = "SELECT id FROM " . $this->archive_table_name . " WHERE stok_code = ?  and remove = 0";

            $query = $this->archive_db_connection->prepare($sql);


            $query->execute([$stock_code]);
        } else if (trim($barcode) != "" && trim($stock_code) != "") {

            $sql = "SELECT id FROM " . $this->archive_table_name . " WHERE (barcode = ? or stok_code = ?) and remove = 0";

            $query = $this->archive_db_connection->prepare($sql);

            $query->execute([$barcode, $stock_code]);
        }



        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function addStock($data, $hesap_no) {

        $varyant_durum = 0;


        if ($data["s_pid"] > 0) {

            $varyant_durum = 1;
        }

        if (is_numeric($data["stok_birimi"])) {

            $data["stok_birimi"] = "Adet";
        }

        if ($data["stok_varyant_adi"] == NULL) {
            $data["stok_varyant_adi"] = "";
        }

        if ($data["stok_varyant_deger"] == NULL) {
            $data["stok_varyant_deger"] = "";
        }

        if ($data["parent_stok_kod"] == NULL) {
            $data["parent_stok_kod"] = "";
        }

        $data["stok_adi"] = $this->tr_ucfirst($data["stok_adi"]);

        $insert_sql = "INSERT INTO " . $this->archive_table_name . " SET 
                        barcode = ?,
                        stok_code = ?,
                        stok_adi = ?,
                        stok_birimi = ?,
                        para_birimi = ?,
                        vergi_orani = ?,
                        alis_fiyat = ?,
                        satis_fiyat = ?,
                        vergi_dahil_fiyatlar = ?,
                        remove = ?,
                        onay = ?,
                        hesap = ?,
                        guncelleme_tarih = ?,
                        varyant_adi = ?,
                        varyant_deger = ?,
                        varyant_stok_code = ?,
                        varyant_durum = ? 
                        
                        ";

        $query = $this->archive_db_connection->prepare($insert_sql);
        $result = $query->execute([
            $data["stok_barkod_no"],
            $data["stok_kod"],
            $data["stok_adi"],
            $data["stok_birimi"],
            $data["stok_doviz"],
            $data["stok_kdv_oran"],
            0,
            $data["stok_satis_fiyati"],
            $data["stok_fiyat_vergi_durum"],
            0,
            0,
            $hesap_no,
            date("Y-m-d H:i:s"),
            $data["stok_varyant_adi"],
            $data["stok_varyant_deger"],
            $data["parent_stok_kod"],
            $varyant_durum,
        ]);

        return $result;
    }

    public function getSourceStocks($source_db_configs, $account_no = 0) {

        $this->archive_db_connection = $this->setArchiveDbConnection();

        $this->source_db_configs = $source_db_configs;

        $this->source_db_connection = $this->setSourceDbConnection();

        $sql = "SELECT " . $this->stok_varyant_select . " FROM stok WHERE remove = 0";

        $query = $this->source_db_connection->prepare($sql);

        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {

            $error = 0;

            foreach ($results as $key => $value) {

                ++$this->select_stock_count;

                $search_result = $this->ifExits($value["stok_barkod_no"], $value["stok_kod"]);

                if (!$search_result) {

                    if (trim($value["stok_barkod_no"]) == "" && trim($value["stok_kod"]) != "") {

                        $value["stok_barkod_no"] = "varyantparent";
                    }

                    $insert_result = $this->addStock($value, $account_no);

                    if (!$insert_result) {

                        $error = 1;
                    } else {

                        $this->i = $this->i + 1;
                    }
                } else {
                    
                }
            }

            if ($error == 1) {

                return 2;
            } else {

                return 1;
            }
        } else {


            return 0;
        }
    }

    public function getSelectStockCount() {
        return $this->select_stock_count;
    }

    public function getInsertCount() {
        return $this->i;
    }

}
