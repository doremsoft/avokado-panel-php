<?php

namespace App\Controller\Export;

use \Dipa\App;
use \Dipa\Sys\Cryptor;


class xmlViewController extends \Dipa\Controller
{

    public function __construct()
    {
        parent::__construct(true);
    }


    public function acKapa(){


        $this->paket_kontrol(["web"], "hesap-paketleri/i/web");

        $exportModel = $this->model("export", "xmlModel");

        $durum = $this->request->input("durum");


       if($durum == "on"){
           $durum = 1;
       }else{
           $durum = 0;
       }


        $iptal = $exportModel->acKapa($durum);


        $msg = "Pasif";

        if($durum == "1"){
            $msg = "Aktif";
        }

        if($iptal){

            \Dipa\Io\Log::write("xml Servis Durumu ".$msg." Hale Getirildi ", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "xml Servis Durumu ".$msg." Hale Getirildi")->to("xml-export/index");

        }else{

            \Dipa\Io\Log::write("xml Servis Durumu değişitirilemedi", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "xml Servis Durumu değişitirilemedi")->back();
        }



    }

    public function ipiptal(){


        $this->paket_kontrol(["web"], "hesap-paketleri/i/web");

        $exportModel = $this->model("export", "xmlModel");

        $ip = $this->request->input("ip_adres");
        $id = $this->request->input("id");

        $iptal = $exportModel->ipIptal($id,self::$account_no);


        if($iptal){

            \Dipa\Io\Log::write("ip Kaldırıldı:".$ip, self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "ip Kaldırıldı:".$ip)->to("xml-export/index");

        }else{

            \Dipa\Io\Log::write("ip Kaldırılamadı! :".$ip, self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "ip Kaldırılamadı! ".$ip)->back();
        }




    }

    public function ipEkle()
    {

        $this->paket_kontrol(["web"], "hesap-paketleri/i/web");

        $exportModel = $this->model("export", "xmlModel");

        $ekle = $exportModel->ipEkle($this->request,self::$account_no);

        $ip = $this->request->input("ip_adres");

        if($ekle){

            \Dipa\Io\Log::write("ip Adresi statik ip listesine eklendi:".$ip, self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "ip Adresi statik ip listesine eklendi:".$ip)->to("xml-export/index");

        }else{

            \Dipa\Io\Log::write("ip Adresi statik ip listesine eklenemedi! :".$ip, self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "İp Adresi statik ip listesine eklenemedi!")->back();
        }



    }

    public function index()
    {


        $this->paket_kontrol(["web"], "hesap-paketleri/i/web");

        $exportModel = $this->model("export", "xmlModel");

        $iplist = $exportModel->getExportIpList(self::$account_no, self::$userInfo["owner_id"]);

        $xml_url = App::getConfig("url") . "/servis/xml/" . self::$account_no . "/" . urlencode(App::getConfig("db", "database"));


        return $this->view("xml/index", ['ip_adresleri' => $iplist, 'xml_url' => $xml_url,'servis_durum'=>self::$account_details["xml_servis"]]);
    }


}