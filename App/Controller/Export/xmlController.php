<?php
namespace App\Controller\Export;
use DOMDocument;
use PDO;
use \Dipa\App;
use \Dipa\Sys\Cryptor;
use XMLWriter;



class xmlController extends \Dipa\Controller
{


    public function __construct() {
        parent::__construct(false);
    }
    private function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }

    public function stokAktar($hesapno , $database , $indir = null) {

        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', '3000');
        ignore_user_abort(true);



        $database = urldecode($database);

        $url = App::getConfig("url");

        $ipadress =  $this->getUserIP();


        try {

            $conn = new PDO(
                'mysql:host=localhost;dbname=' .$database. '', App::getConfig("db","username"),  App::getConfig("db","password"), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            echo "Bağlantı Hatası";

            die();
        }

        $stokquery = $conn->prepare("SELECT id  FROM hesap_detaylari  WHERE xml_servis = 1   and account_id = ? ");
        $stokquery->execute([$hesapno]);
        $hesap_kontrol =  $stokquery->fetch();

        if(!$hesap_kontrol){

            echo "Geçersiz Hesap";
                die();

         }else {


            $paket_kontrolquery = $conn->prepare("SELECT id  FROM hesap_paketleri  WHERE paket_key = ? and  hesap_no = ? ");
            $paket_kontrolquery->execute(["web", $hesapno]);
            $paket_kontrol = $paket_kontrolquery->fetch();


            if (!$paket_kontrol) {

                echo "Paket Yetersiz: " . $ipadress;
                die();

            } else {



            $ipquery = $conn->prepare("SELECT id FROM statik_ip_listesi  WHERE account_no = ? and ip_adress = ? and  remove = 0 ");
            $ipquery->execute([$hesapno, trim($ipadress)]);
            $ip_kontrol = $ipquery->fetch();

            if (!$ip_kontrol) {

                echo "Hatalı İp Adresi: " . $ipadress;
                die();

            } else {

                $analist = [];
                $vayantlist = [];


                $stokquery = $conn->prepare("SELECT id,stok_detayi,stok_resim,stok_kod,stok_adi,stok_barkod_no,stok_satis_fiyati,stok_kdv_oran,stok_doviz,stok_tipi   FROM stok WHERE remove =  0 and stok_parent_id = 0 ORDER BY id ASC");
                $stokquery->execute();
                $anastokalar = $stokquery->fetchAll(PDO::FETCH_ASSOC);


                if ($anastokalar) {

                    foreach ($anastokalar as $key => $val) {

                        $analist[$val["id"]] = $val;

                    }
                }

                $anastokalar = null;


                $variantsstmt = $conn->prepare("SELECT stok_kod,stok_barkod_no,stok_varyant_adi,stok_varyant_deger,stok_satis_fiyati,stok_parent_id FROM stok WHERE remove =  0 and stok_parent_id != 0 ORDER BY id ASC");
                $variantsstmt->execute();
                $varyantstoklar = $variantsstmt->fetchAll(PDO::FETCH_ASSOC);

                if ($varyantstoklar) {
                    foreach ($varyantstoklar as $key => $val) {

                        $vayantlist[$val["stok_parent_id"]][] = $val;
                    }
                }

                $varyantstoklar = null;
                $conn = null;


                if ($indir == null) {
                    header('Content-type: text/xml; charset=UTF-8');
                } else {

                    $stoklistname = "stoklist-" . time() . ".xml";
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary");
                    header('Content-disposition: attachment; filename="' . $stoklistname . '"');


                }


                $xmlWriter = new XMLWriter();
                $xmlWriter->openMemory();
                $xmlWriter->openURI('php://output');
                $xmlWriter->startDocument('1.0', 'UTF-8');
                $xmlWriter->setIndent(true);
                $xmlWriter->startElement('root');

                foreach ($analist as $key => $row) {

                    $xmlWriter->startElement('item');

                    $xmlWriter->writeElement('stockCode', $row['stok_kod']);


                    $xmlWriter->startElement('label');
                    $xmlWriter->writeCData($row['stok_adi']);
                    $xmlWriter->endElement();


                    $xmlWriter->writeElement('barcode', $row['stok_barkod_no']);
                    $xmlWriter->writeElement('price1', number_format($row['stok_satis_fiyati'], 4, '.', ''));
                    $xmlWriter->writeElement('tax', $row['stok_kdv_oran']);
                    $xmlWriter->writeElement('currencyAbbr', $row['stok_doviz']);
                    $xmlWriter->writeElement('stockType', $row['stok_tipi']);

                    if ($row['stok_resim'] == "noimage.jpg") {

                        $xmlWriter->writeElement('picture1Path');

                    } else {
                        $xmlWriter->writeElement('picture1Path', $url . "/media/" . $hesapno . "/s/stok-foto/" . $row['stok_resim']);

                    }


                    $xmlWriter->startElement('details');
                    $xmlWriter->writeCData($row['stok_detayi']);
                    $xmlWriter->endElement();

                    $variantsstmtresult = false;

                    if (isset($vayantlist[$row["id"]])) {

                        $variantsstmtresult = true;

                    }


                    if ($variantsstmtresult) {


                        $xmlWriter->startElement('variants');

                        foreach ($vayantlist[$row["id"]] as $variantsstmtrow => $value) {

                            $xmlWriter->startElement('variant');

                            $xmlWriter->writeElement('vStockCode', $value['stok_kod']);
                            $xmlWriter->writeElement('vBarcode', $value['stok_barkod_no']);

                            $xmlWriter->startElement('variantName');
                            $xmlWriter->writeCData($value['stok_varyant_adi']);
                            $xmlWriter->endElement();


                            $xmlWriter->startElement('variantValue');
                            $xmlWriter->writeCData($value['stok_varyant_deger']);
                            $xmlWriter->endElement();


                            $xmlWriter->writeElement('vPrice1', number_format($value['stok_satis_fiyati'], 4, '.', ''));

                            $xmlWriter->endElement();

                        }

                        $xmlWriter->endElement();


                    } else {
                        $xmlWriter->startElement('variants');
                        $xmlWriter->endElement();
                    }


                    $xmlWriter->endElement();

                    $xmlWriter->flush();

                }


                $analist = null;
                $vayantlist = null;


                $xmlWriter->endElement();
                $xmlWriter->endDocument();
                echo $xmlWriter->outputMemory(true);


            }


        }

            }

    }




}
