<?php

namespace App\Controller\Download;

class downloadActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }



    public function barkodWindows() {
        \Dipa\Io\Log::write("barkod download", self::$account_no, self::$userInfo["id"]);
        $_file = BASE_PATH."..".DS."..".DS."software".DS.'windows'.DS.'barkod.zip';
        $archive_file_name = "barkod.zip";




        header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$archive_file_name");
header("Content-length: " . filesize($_file));
header("Pragma: no-cache");
header("Expires: 0");
readfile("$_file");



    }

}
