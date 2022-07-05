<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

//use \PhpOffice\PhpSpreadsheet\Spreadsheet;
//use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 *
 * @author Doğuş DİCLE
 */
class excelStokModel extends Dimodel
{
    /*
     * Controller::$userInfo
     */


    public function secimliStokExcelAktarim($request)
    {

        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $excel_sira_list = [];

        $barkod = $request->input("barkod");
        if ($barkod != "no") {
            $excel_sira_list[$barkod] = "stok_barkod_no";

        }

        $stok_kod = $request->input("stok_kod");

        if ($stok_kod != "no") {

            $excel_sira_list[$stok_kod] = "stok_kod";

        }

        $stok_adi = $request->input("stok_adi");

        if ($stok_adi != "no") {

            $excel_sira_list[$stok_adi] = "stok_adi";
        }

        $mevcut = $request->input("mevcut");

        if ($mevcut != "no") {
            $excel_sira_list[$mevcut] = "mevcut";

        }

        $birim = $request->input("birim");

        if ($birim != "no") {

            $excel_sira_list[$birim] = "stok_birimi";

        }

        $vh_alim = $request->input("vh_alim");

        if ($vh_alim != "no") {
            $excel_sira_list[$vh_alim] = "vh_alim";
        }

        $vh_satis = $request->input("vh_satis");

        if ($vh_satis != "no") {
            $excel_sira_list[$vh_satis] = "vh_satis";
        }

        $vd_alim = $request->input("vd_alim");

        if ($vd_alim != "no") {
            $excel_sira_list[$vd_alim] = "vd_alim";
        }

        $vd_satis = $request->input("vd_satis");

        if ($vd_satis != "no") {
            $excel_sira_list[$vd_satis] = "vd_satis";
        }

        $vergi_oran = $request->input("vergi_oran");

        if ($vergi_oran != "no") {
            $excel_sira_list[$vergi_oran] = "vergi_oran";
        }

        $doviz = $request->input("doviz");

        if ($doviz != "no") {
            $excel_sira_list[$doviz] = "doviz";
        }

        $kur = $request->input("kur");

        if ($kur != "no") {
            $excel_sira_list[$kur] = "kur";
        }


        $vd_son_alim = $request->input("vd_son_alim");

        if ($vd_son_alim != "no") {
            $excel_sira_list[$vd_son_alim] = "vd_son_alim";
        }

        $vh_son_alim = $request->input("vh_son_alim");

        if ($vh_son_alim != "no") {
            $excel_sira_list[$vh_son_alim] = "vh_son_alim";
        }


        $yalnizca_alimlarla = $request->input("yalnizca_alimlarla");


        if (empty($excel_sira_list)) {

            return false;

        } else {

            ksort($excel_sira_list);

            $this->generateExcel($excel_sira_list, $yalnizca_alimlarla);

        }

    }

    private function generateExcel($excel_sira_list, $stokListeTuru)
    {

        $model = Controller::include_model("doviz", "dovizModel");

        $dovizler = $model->dovizleriAl();

        $doviz_listesi = [];

        if ($dovizler) {

            if (is_array($dovizler)) {

                foreach ($dovizler as $key => $value) {

                    $doviz_listesi[$value["doviz_kod"]] = [
                        'ad' => $value["doviz_adi"],
                        'kod' => $value["doviz_kod"],
                        'kur' => $value["doviz_kur"],
                        'column' => $value
                    ];
                }
            }
        }


        $columns = array();

        $data = array();


        $i = 0;

        $first_key = "";
        $last_key = "";

        foreach ($excel_sira_list as $key => $val) {

            $ia = $i + 1;

            if ($i == 0) {
                $first_key = $key;

            } else if (!isset($excel_sira_list[$ia])) {

                $last_key = $key;
            }

            $i++;

        }


        array_unshift($excel_sira_list, "id");


        if ($stokListeTuru == 4) {
            array_push($excel_sira_list, "sayim_adet");
        }

        foreach ($excel_sira_list as $key2 => $val2) {

            array_push($columns, $val2);

            // $sheet->setCellValue($key2 . "1", );
        }


        $x = 2;

        $result = [];

        $varyan_sql_select = Controller::helper(null, "stokModelHelper")->getStokVaryantSelectFull();

        if ($stokListeTuru == 1) {

            $y_query = $this->getConnection()->prepare("SELECT stok_id FROM stok_haraket_giris WHERE remove = 0 and owner_id = ?");

            $y_query->execute([Controller::$userInfo["owner_id"]]);

            $y_result = $y_query->fetchAll(PDO::FETCH_ASSOC);

            if ($y_result) {

                foreach ($y_result as $key => $value) {

                    $alimlar_id[$value["stok_id"]] = $value["stok_id"];

                }

                $id_list_sql_string = implode(",", $alimlar_id);

                $sql = "SELECT stok.* , {$varyan_sql_select} FROM stok  WHERE  id  IN ({$id_list_sql_string})  and  remove = 0 and owner_id = ? ";

            } else {
                $sql = "SELECT stok.* , {$varyan_sql_select}  FROM stok WHERE remove = 0 and owner_id = ? ";

            }
        } else if ($stokListeTuru == 4) {


            $sql = "SELECT   
stok.* , 
stok.stok_parent_id ,
stok.id , 
stok.stok_kod , 
stok.stok_resim , 
stok.stok_alis_fiyati  , 
stok.stok_satis_fiyati , 
stok.stok_max_iskontolu_satis_fiyati ,
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,
 stok.stok_web_title , 
 stok.stok_web_description , 
IF(lst.id IS NULL ,stok.stok_fiyat_vergi_durum,lst.stok_fiyat_vergi_durum) AS stok_fiyat_vergi_durum , 
IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
IF(lst.id IS NULL ,stok.stok_alim_doviz,lst.stok_alim_doviz) AS stok_alim_doviz , 
IF(lst.id IS NULL ,stok.stok_birimi,lst.stok_birimi) AS stok_birimi , 
IF(lst.id IS NULL ,stok.stok_resim,lst.stok_resim) AS stok_resim , 
IF(lst.id IS NULL ,stok.stok_adi,lst.stok_adi) AS stok_adi,
IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_full_ad ,
stok.id as st_id , stok.stok_adet  ,
(SELECT stok_haraket_giris.alis_fiyati FROM stok_haraket_giris WHERE stok_haraket_giris.stok_id = stok.id ORDER BY stok_haraket_giris.id DESC LIMIT 1) as son_alis_fiyati  
 
 
FROM 
 
 stok  
 
 LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id 

 WHERE 
 
 stok.remove = 0 and stok.stok_adet != 0  and stok.owner_id = ?  ORDER BY stok_full_ad ASC ";


        } else if ($stokListeTuru == 5) {

            $sql = "SELECT   
stok.* , 
stok.stok_parent_id ,
stok.id , 
stok.stok_kod , 
stok.stok_resim , 
stok.stok_alis_fiyati  , 
stok.stok_satis_fiyati , 
stok.stok_max_iskontolu_satis_fiyati ,
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,
 stok.stok_web_title , 
 stok.stok_web_description , 
IF(lst.id IS NULL ,stok.stok_fiyat_vergi_durum,lst.stok_fiyat_vergi_durum) AS stok_fiyat_vergi_durum , 
IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
IF(lst.id IS NULL ,stok.stok_alim_doviz,lst.stok_alim_doviz) AS stok_alim_doviz , 
IF(lst.id IS NULL ,stok.stok_birimi,lst.stok_birimi) AS stok_birimi , 
IF(lst.id IS NULL ,stok.stok_resim,lst.stok_resim) AS stok_resim , 
IF(lst.id IS NULL ,stok.stok_adi,lst.stok_adi) AS stok_adi,
IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_full_ad ,
stok.id as st_id , stok.stok_adet  ,
(SELECT stok_haraket_giris.alis_fiyati FROM stok_haraket_giris WHERE stok_haraket_giris.stok_id = stok.id ORDER BY stok_haraket_giris.id DESC LIMIT 1) as son_alis_fiyati  
 
 
FROM 
 
 stok  
 
 LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id 

 WHERE 
 
 stok.remove = 0 and stok.stok_adet = 0  and stok.owner_id = ?  ORDER BY stok_full_ad ASC ";
        }

        $y_result = null;

        $query = $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo["owner_id"]]);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {

            $x = 2;
            $data = [];
            $datakey = 0;
            $mainkey = 0;

            foreach ($result as $k => $get) {

                $data[$mainkey] = [];
                $datakey = 0;

                foreach ($excel_sira_list as $key3 => $key_name) {

                    $filed = "";

                    switch ($key_name) {

                        case "id";
                            $filed = $get["id"];
                            break;

                        case "stok_barkod_no";
                            $filed = (string) "'".$get["stok_barkod_no"];

                            break;

                        case "stok_kod";
                            $filed =  (string) $get["stok_kod"];
                            break;
                        case "stok_adi";

                            $filed =  (string) $get["stok_full_ad"];

                            break;
                        case "mevcut";
                            $filed = $get["stok_adet"];


                            $filed = number_format($filed, 4, ',', '.');

                            break;

                        case "stok_birimi";
                            $filed = $get["stok_birimi"];
                            break;


                        case "vh_son_alim";

                            if ($get["son_alis_fiyati"] == null) {

                                $filed = 0;

                            } else {

                                $filed = number_format($get["son_alis_fiyati"], 4, ',', '.');
                            }

                            break;


                        case "vd_son_alim";

                            $fiyat = $get["son_alis_fiyati"];

                            if ($fiyat != null) {

                                $filed = ($fiyat * $get["stok_kdv_oran"] / 100) + $fiyat;

                                $filed = number_format($filed, 4, ',', '.');

                            } else {

                                $filed = 0;
                            }


                            break;


                        case "vh_alim";

                            $filed = $get["stok_alis_fiyati"];

                            $filed = number_format($filed, 4, ',', '.');
                            break;


                        case "vh_satis";

                            $doviz = $doviz_listesi[$get["stok_doviz"]];

                            $filed = $get["stok_satis_fiyati"] * $doviz["kur"];

                            $filed = number_format($filed, 4, ',', '.');

                            break;

                        case "vd_alim";

                            $fiyat = $get["stok_alis_fiyati"];

                            $filed = ($fiyat * $get["stok_kdv_oran"] / 100) + $fiyat;

                            $filed = number_format($filed, 4, ',', '.');

                            break;

                        case "vd_satis";

                            $doviz = $doviz_listesi[$get["stok_doviz"]];

                            $fiyat = $get["stok_satis_fiyati"] * $doviz["kur"];

                            $filed = ($fiyat * $get["stok_kdv_oran"] / 100) + $fiyat;

                            $filed = number_format($filed, 4, ',', '.');
                            break;

                        case "vergi_oran";
                            $filed = $get["stok_kdv_oran"];
                            break;

                        case "doviz";
                            $filed = $get["stok_doviz"];
                            break;


                        case "kur";
                            $doviz = $doviz_listesi[$get["stok_doviz"]];

                            $filed = $doviz["kur"];

                            $filed = number_format($filed, 4, ',', '.');
                            break;

                        case "sayim_adet";

                            $filed = "";
                            break;

                    }

                    $data[$mainkey][$datakey] = $filed;

                    $datakey++;

                }

                $x++;
                $mainkey++;
            }

        }


        $this->exportExcel('stok-liste-' . date("Y-m-d-h-i-s"), $columns, $data);

    }

    private function exportExcel($filename = 'ExportExcel', $columns = array(), $data = array(), $replaceDotCol = array())
    {
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/plain; charset=utf-8');
        header("Content-disposition: attachment; filename=" . $filename . ".xls");
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $say = count($columns);

        echo '<table border="1"><tr>';
        foreach ($columns as $v) {
            echo '<th style="background-color:#FFA500">' . trim($v) . '</th>';
        }
        echo '</tr>';

        foreach ($data as $val) {
            echo '<tr>';
            for ($i = 0; $i < $say; $i++) {

                if (in_array($i, $replaceDotCol)) {
                    echo '<td>' . str_replace('.', ',', $val[$i]) . '</td>';
                } else {
                    echo '<td>' . $val[$i] . '</td>';
                }
            }
            echo '</tr>';
        }
    }

}
