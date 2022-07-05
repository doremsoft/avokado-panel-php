<?php
namespace App\Controller\Export;

class excelActionController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    
     
      public function stokGuncellemeListesi() {

   
        $exportModel = $this->model("export", "excelModel");
        
        
        $export = $exportModel->stokDuzenlemeListesi($this->request);

        \Dipa\Io\Log::write("Stok düzenleme listesi excel olarak aktarıldı", self::$account_no, self::$userInfo["id"]);

    }


    public function seciliStoklar(){



        $exportModel = $this->model("export", "excelStokModel");


        $export = $exportModel->secimliStokExcelAktarim($this->request);

        \Dipa\Io\Log::write("Seçili Stoklar excel olarak aktarıldı", self::$account_no, self::$userInfo["id"]);



    }
}