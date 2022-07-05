<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 *
 * @author Doğuş DİCLE
 */
class excelModel extends Dimodel {
    /*
     * Controller::$userInfo
     */

    public function stokDuzenlemeListesi($request) {


     set_time_limit(750);
     ini_set('memory_limit','250M');


        $alimlar_id = [];

        $varyan_sql_select = Controller::helper(null,"stokModelHelper")->getStokVaryantSelectFull();



        if ($request->input("yalnizca_alimlarla") == 1) {

            $y_query = $this->getConnection()->prepare("SELECT stok_id FROM stok_haraket_giris WHERE remove = 0 and owner_id = ?");

            $y_query->execute([Controller::$userInfo["owner_id"]]);

            $y_result = $y_query->fetchAll(PDO::FETCH_ASSOC);
            
            
            if($y_result){
                
                
                foreach ($y_result as $key => $value) {
                    
                    $alimlar_id[$value["stok_id"]] = $value["stok_id"];
        
                }
                
                
               $id_list_sql_string= implode(",", $alimlar_id);
               
         
               
            $sql = "SELECT stok.* , {$varyan_sql_select} FROM stok  WHERE  id  IN ({$id_list_sql_string})  and  remove = 0 and owner_id = ? ";

                
                
            }else{
                  $sql = "SELECT stok.* , {$varyan_sql_select}  FROM stok WHERE remove = 0 and owner_id = ? ";
                
            }
        }else{
            
               $sql = "SELECT stok.* , {$varyan_sql_select}  FROM stok WHERE remove = 0 and owner_id = ? ";

            
        }


        $y_result = null;


        $query = $this->getConnection()->prepare($sql);

        $query->execute([Controller::$userInfo["owner_id"]]);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($request) {

            $this->generateExcel($result, $request->input("vergi_oran_durum"));
        }
    }

    private function generateExcel($result, $vergili_fiyat_durum) {


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getProperties()->setCreator('avokado')
                ->setLastModifiedBy('Cholcool')
                ->setTitle('Avokado Yazılım Excelden Stok Güncelleme Sistemi')
                ->setSubject('Excel Güncelleme')
                ->setDescription('Excelden Güncelleme');

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'bottom' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('rgb' => '333333'),
                ),
            ),
            'fill' => array(
                'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array('rgb' => '0d0d0d'),
                'endColor' => array('rgb' => 'f2f2f2'),
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);

        foreach (range('A', 'I') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Urun Adı');
        $sheet->setCellValue('C1', 'Alim Fiyat');
        $sheet->setCellValue('D1', 'Satis Fiyat');
        $sheet->setCellValue('E1', 'Vergi Oran');
        $sheet->setCellValue('F1', 'Doviz');
        $sheet->setCellValue('G1', 'PID');
        $sheet->setCellValue('H1', 'Varyant Adı');
        $sheet->setCellValue('I1', 'Varyant Deger');

        $x = 2;

        foreach ($result as $k => $get) {
            $sheet->setCellValue('A' . $x, $get["id"]);


            if($get["s_pid"] > 0){
                $sheet->setCellValue('B' . $x, $get["stok_adi"]." ".$get["stok_varyant_adi"]." ".$get["stok_varyant_deger"]);

            }else{
                $sheet->setCellValue('B' . $x, $get["stok_adi"]);
            }



            if ($vergili_fiyat_durum == 1) {

                $sheet->setCellValue('C' . $x, $get["stok_alis_fiyati"]);
                $sheet->setCellValue('D' . $x, $get["stok_satis_fiyati"]);

            } else if ($vergili_fiyat_durum == 2) {

                $vergi_orani = $get["stok_kdv_oran"];

                if ($vergi_orani > 9) {

                    $kdv_duzelt = "1." . $vergi_orani;
                } else {

                    $kdv_duzelt = "1.0" . $vergi_orani;
                }
                $kdv_dahil_alis = $get["stok_alis_fiyati"] * $kdv_duzelt;
                $sheet->setCellValue('C' . $x, number_format($kdv_dahil_alis, 2, '.', ''));
                $sheet->setCellValue('D' . $x, $get["stok_kdv_dahil_satis_fiyati"]);
            }

            $sheet->setCellValue('E' . $x, $get["stok_kdv_oran"]);
            $sheet->setCellValue('F' . $x, $get["stok_doviz"]);
            $sheet->setCellValue('G' . $x, $get["s_pid"]);
            $sheet->setCellValue('H' . $x, $get["stok_varyant_adi"]);
            $sheet->setCellValue('I' . $x, $get["stok_varyant_deger"]);


            $x++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'stok-liste-' . date("Y-m-d-h-i-s");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        /*
          $writer = new Xlsx($spreadsheet);
          $writer->save('recruitment_form.xlsx');
         * 
         */
    }

}
