<?php
namespace App\Controller\Export;

class exportViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }

    public function add() {

        return $this->view("export/export-add");
    }


    public function prepareExcelExport() {

        return $this->view("excel/prepare-excel-export");
    }

}