<?php

namespace App\Controller\Import;

use App\Controller\Work\workViewController;
use Dipa\Controller;
use Dipa\Db\Model;

class xmlActionController extends \Dipa\Controller
{

    public $result = [];
    public $islem_kod;

    public $varb_json;
    public $url_adres;
    public $xml_firma;
    public $xml_not;

    public $eslesme_listesi;
    public $eslesme_listesi_tersten;


    public $kontrol_secenekleri;
    public $kontrol_secenekleri_tersten;

    public $yoksa_eklenecekler;
    public $yoksa_eklenecekler_tersten;

    public $varsa_guncellenecekler;
    public $varsa_guncellenecekler_tersten;

    private $xmlmodel;

    private $xml_dosya_adi;
    private $xml_download = false;


    /*
     * Koşullar
     */

    public $kontrol_durum = false;
    public $yoksa_ekle_durum = false;
    public $varsa_guncelle_durum = false;
    public $varyantlama_durum = false;
    public $varyantlama_alan_adi;

    public $xml_toplam_stok = 0;
    public $eslesen_stok = 0;
    public $guncellenen_stok = 0;
    public $yeni_eklenen_stok = 0;

    public $toplam_varyant = 0;
    public $eslesen_varyant = 0;
    public $yeni_eklenen_varyant = 0;
    public $guncellenen_varyant = 0;

    public $fiyatlara_vergi_dahil = false;

    public $varyant_loop_result;

    private $test_mod = true;

    private $varyant_adi_duzenle = false;
    private $varyant_ad_duzenleme_liste = [];
    private $varyant_grup_adi = "";
    private $grup_resim_adi = null;
    private $grup_resim_ust_grup_adi = null;

    public $eklenemeyen_varyantlar = [];


    public $xml_stok_listesi = [];
    public $xml_stok_eslesme_listesi_sablon = [];


    private $xmlid;


    public function __construct($auth = true, $xml_id = null)
    {
        parent::__construct($auth);

        $this->islem_kod = uniqid();

        $this->xmlid = $xml_id;

        $this->xmlmodel = new \App\Model\xml\xmlModel();

    }


    private function xmlAnalizinArrayKontrol($arrayData = [], $isVariant = false)
    {


        foreach ($arrayData as $key => $value) {


            if (is_array($value)) {

                if (!is_numeric($key)) {

                    $this->result[$key] = $key;
                }


                if ($this->varyant_adi_duzenle) {

                    if ($key == $this->varyant_grup_adi) {

                        $isVariant = true;
                    }

                }


                if ($this->grup_resim_adi != null) {

                    if ((String)$key == $this->grup_resim_adi) {

                        $yeni_grup_resim = [];

                        $sayac = 0;

                        foreach ($value as $rkey => $rval) {

                            if ($sayac < 5) {

                                $yeni_grup_resim["resim" . $sayac] = $rval;

                                $sayac++;

                            }

                        }

                        $value = $yeni_grup_resim;

                    }

                }


                $this->xmlAnalizinArrayKontrol($value, $isVariant);

            } else {


                if ($this->varyant_adi_duzenle) {

                    if ($isVariant == true) {

                        if (isset($this->varyant_ad_duzenleme_liste[$key])) {


                            $key = $this->varyant_ad_duzenleme_liste[$key];

                        }


                    }

                }

                $this->result[$key] = $key;
            }
        }

    }


    public function xmlDownload()
    {

        $this->url_adres = $this->request->input("url");
        $dosyaadi = $this->request->input("dosyaadi");

        $folder_path = STORAGE_PATH . DS . self::$account_no . DS . "media" . DS . "private" . DS . "xmljsondata";

        if (!file_exists($folder_path)) {

            mkdir($folder_path, 0777, true);
        }

        $this->xml_dosya_adi = $this->xmlmodel->seo_url($dosyaadi . "-" . date("Y-m-d")) . ".txt";

        $full_path = $folder_path . DS . $this->xml_dosya_adi;

        if (file_exists($full_path)) {

            echo 3;

        } else {


            $xml_full_path = STORAGE_PATH . DS . self::$account_no . DS . "media" . DS . "private" . DS . "xmldata" . DS . $dosyaadi . ".xml";

            $file_data = file_get_contents($this->url_adres);

            file_put_contents($xml_full_path, $file_data);


            $stok_data = json_encode(simplexml_load_string($file_data, null, LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_PARSEHUGE));


            if ($stok_data == false) {

                $stok_data = json_encode(simplexml_load_string($file_data, null, LIBXML_NOCDATA | LIBXML_NOBLANKS | LIBXML_COMPACT | LIBXML_PARSEHUGE));

            }





            if ($stok_data == false) {



                echo 0;

            } else {

                file_put_contents($full_path, $stok_data);


                $result = $this->xmlmodel->xmlDosyaEkle($this->xml_dosya_adi, $dosyaadi, $this->url_adres);

                if ($result) {

                    echo 1;

                } else {

                    echo 0;
                }

            }


        }


    }

    public function favoriAl($id)
    {

        $result = $this->xmlmodel->xmlFavoriIslem($id, 1);


        if ($result) {


            $this->header->result("success", "İşlem Tamamlandı")->back();
        } else {

            $this->header->result("fail", "Favori Ekleme  Başarısız!")->back();
        }


    }


    public function favoriCikart($id)
    {

        $result = $this->xmlmodel->xmlFavoriIslem($id, 0);


        if ($result) {


            $this->header->result("success", "İşlem Tamamlandı")->back();
        } else {

            $this->header->result("fail", "İşlem Başarısız!")->back();
        }


    }

    public function xmlReDownload($id)
    {


        $dosya = $this->xmlmodel->xmlDosyasi($id);

        $this->url_adres = $dosya["dosya_url_adresi"];

        $dosyaadi = $dosya["xml_dosya_adi"];

        $folder_path = STORAGE_PATH . DS . self::$account_no . DS . "media" . DS . "private" . DS . "xmljsondata";

        if (!file_exists($folder_path)) {

            mkdir($folder_path, 0777, true);
        }

        $this->xml_dosya_adi = $dosyaadi . ".txt";

        $full_path = $folder_path . DS . $this->xml_dosya_adi;

        $xml_full_path = STORAGE_PATH . DS . self::$account_no . DS . "media" . DS . "private" . DS . "xmldata" . DS . $dosyaadi . ".xml";

        $file_data = file_get_contents($this->url_adres);

        file_put_contents($xml_full_path, $file_data);

        $stok_data = json_encode(simplexml_load_string($file_data, null, LIBXML_NOCDATA));


        if ($stok_data == false) {

            $stok_data = json_encode(simplexml_load_string($file_data, null, LIBXML_NOCDATA | LIBXML_NOBLANKS));

        }

        if ($stok_data) {

            file_put_contents($full_path, $stok_data);

            $result = $this->xmlmodel->xmlDosyaGuncelle($id);

            if ($result) {

                \Dipa\Io\Log::write("Xml Dosyası url adresinden güncellendi", self::$account_no, self::$userInfo["id"]);


                $this->header->result("success", "Xml Dosyası url adresinden güncellendi")->back();
            } else {
                \Dipa\Io\Log::write("Güncelleme Başarısız!", self::$account_no, self::$userInfo["id"]);
                $this->header->result("fail", "Güncelleme Başarısız!")->back();
            }
        } else {

            $this->header->result("fail", "Güncelleme Başarısız!")->back();

        }


    }

    public function xmlAnaliz()
    {


        $file = STORAGE_PATH . DS . self::$account_no . DS . "media" . DS . "private" . DS . "xmljsondata" . DS . $this->request->input("xmldosyaadi");

        if (file_exists($file)) {


        } else {

            $file = $file . ".txt";
        }


        $result = $this->xmlmodel->xmlDosyasiAdIle($this->request->input("xmldosyaadi"));


        if ($result) {

            if ($result["varyant_isimleri_duzenle"] == 1) {


                $this->varyant_adi_duzenle = true;
                $this->varyant_ad_duzenleme_liste = json_decode($result["varyant_isimler_degistirme"], true);
                $this->varyant_grup_adi = $result["varyant_grup_adi"];
                $this->grup_resim_adi = $result["grup_resim_adi"];
                $this->grup_resim_ust_grup_adi = $result["grup_resim_ust_grup_adi"];
            }

        }

        $this->xmlAnalizinArrayKontrol(json_decode(file_get_contents($file), true), false);


        echo json_encode($this->result);
    }


    private function xmlLoop($loop = [])
    {

        $result = [];


        foreach ($loop as $key => $value) {

            if (is_array($value)) {


                $result[] = $this->xmlLoop($value);

            } else {

                $result[$key] = $value;
            }
        }

        return $result;
    }


    private function xmlVaryantLoop($loop = [])
    {

        if (is_array($loop)) {

            foreach ($loop as $key => $value) {

                if (is_array($value)) {


                    $this->xmlVaryantLoop($value);


                } else {

                    $this->varyant_loop_result[$key] = $value;
                }
            }

        }


    }


    public function xmlImportFromTerminal($id)
    {


        $xml_info = $this->xmlmodel->xmlYuklemeBilgileri($id);


        $this->xmlImportExecute($xml_info["xml_data"]);

    }


    public function xmlImport()
    {


        $varb = $this->request->input("varb");

        $this->varb_json = $varb;

        $varb = json_decode($varb, true);

        $mod = $varb["mod"];

        if ($mod == "uygula") {

            $this->test_mod = false;

        } else {

            $this->test_mod = true;

        }


        if ($varb["stok_vergi_durum"] == 1) {

            $this->fiyatlara_vergi_dahil = true;

        }


        $this->xml_dosya_adi = $varb["xmldosyaadi"];
        $this->xml_firma = $varb["xml_firma"];
        $this->xml_not = $varb["xml_not"];

        $this->eslesme_listesi = $varb["eslesme_listesi"];
        $this->eslesme_listesi_tersten = $varb["eslesme_listesi_tersten"];

        $this->kontrol_secenekleri = $varb["kontrol_secenekleri"];
        $this->kontrol_secenekleri_tersten = $varb["kontrol_secenekleri_tersten"];

        $this->yoksa_eklenecekler = $varb["yoksa_eklenecekler"];
        $this->yoksa_eklenecekler_tersten = $varb["yoksa_eklenecekler_tersten"];

        $this->varsa_guncellenecekler = $varb["varsa_guncellenecekler"];
        $this->varsa_guncellenecekler_tersten = $varb["varsa_guncellenecekler_tersten"];

        if ($varb["varyant_alani"] != "off") {

            $this->varyantlama_durum = true;
            $this->varyantlama_alan_adi = $varb["varyant_alani"];

        }


        $dosyaresult = $this->xmlmodel->xmlDosyasiAdIle($varb["xmldosyaadi"]);

        if ($dosyaresult) {

            $this->grup_resim_adi = $dosyaresult["grup_resim_adi"];

            $this->grup_resim_ust_grup_adi = $dosyaresult["grup_resim_ust_grup_adi"];

        }


        foreach ($this->eslesme_listesi_tersten as $key => $val) {

            $this->xml_stok_eslesme_listesi_sablon[$key] = "";

        }

        $this->xml_stok_eslesme_listesi_sablon["stok_parent_id"] = "0";


        if (!empty($this->kontrol_secenekleri)) {

            $this->kontrol_durum = true;

            if (!empty($this->varsa_guncellenecekler)) {

                $this->varsa_guncelle_durum = true;
            }

            if (!empty($this->yoksa_eklenecekler)) {
                $this->yoksa_ekle_durum = true;
            }
        } else {
            $this->yoksa_ekle_durum = true;
        }


        $folder_path = STORAGE_PATH . DS . self::$account_no . DS . "media" . DS . "private" . DS . "xmljsondata";


        if (file_exists($folder_path . DS . $this->xml_dosya_adi)) {

            $file = $folder_path . DS . $this->xml_dosya_adi;

        } else {

            $file = $folder_path . DS . $this->xml_dosya_adi . ".txt";
        }


        $file_data = file_get_contents($file);

        $stok_data = json_decode($file_data, true);


        foreach ($stok_data as $key => $value) {

            foreach ($value as $key2 => $value2) {

                $sayac = 0;

                if ($this->grup_resim_adi != null) {

                    if ($this->grup_resim_ust_grup_adi == null) {

                        if (isset($value2[$this->grup_resim_adi])) {

                            if (is_array($value2[$this->grup_resim_adi])) {

                                foreach ($value2[$this->grup_resim_adi] as $rkey => $rval) {

                                    if ($sayac < 5) {

                                        $value2["resim" . $sayac] = $rval;

                                        $sayac++;

                                    }

                                }

                            }


                        }


                    } else {

                        if (isset($value2[$this->grup_resim_ust_grup_adi][$this->grup_resim_adi])) {

                            if (is_array($value2[$this->grup_resim_ust_grup_adi][$this->grup_resim_adi])) {

                                foreach ($value2[$this->grup_resim_ust_grup_adi][$this->grup_resim_adi] as $rkey => $rval) {

                                    if ($sayac < 5) {

                                        $value2["resim" . $sayac] = $rval;

                                        $sayac++;

                                    }

                                }


                            }


                        }


                    }


                }


                $this->executeXmlData($value2, $this->xmlLoop($value2));

            }

        }


        $import_result = [
            "xml_toplam_stok" => $this->xml_toplam_stok,
            "eslesen_stok" => $this->eslesen_stok,
            "guncellenen_stok" => $this->guncellenen_stok,
            "yeni_eklenen_stok" => $this->yeni_eklenen_stok,
            "eklenemeyen_varyantlar" => $this->eklenemeyen_varyantlar,
            "varyantlama_durum" => $this->varyantlama_durum,
            "varyantlama_alani" => $this->varyantlama_alan_adi,
            "toplam_varyant" => $this->toplam_varyant,
            "eslesen_varyant" => $this->eslesen_varyant,
            "yeni_eklenen_varyant" => $this->yeni_eklenen_varyant,
            "guncellenen_varyant" => $this->guncellenen_varyant,
            "eslesme_sablon" => $this->xml_stok_eslesme_listesi_sablon
        ];


        $durum = 0;


        $result_data = json_encode($import_result);


        if ($this->test_mod == false) {
            $durum = 1;
        }

        $this->xmlmodel->xmlSonucKaydet($this->xml_dosya_adi, $this->xml_firma, $this->varb_json, $result_data, $this->xml_not, $durum, $this->islem_kod);


        if ($this->xmlmodel->resim_gorevci == true) {

            $this->xmlmodel->gorevEkle(new Controller(), "Url Resimleri Sunucuya İndir", "imagesdownload");

        }

        $xmlparsedfolder = $folder_path . DS . "parse";


        if (!file_exists($xmlparsedfolder)) {

            mkdir($xmlparsedfolder, 0777, true);
        }


        file_put_contents($xmlparsedfolder . DS . $this->islem_kod . ".txt", json_encode($this->xml_stok_listesi));


        echo $result_data;
    }

    private function executeXmlData($data = [], $loop_data = [])
    {

        $this->xml_toplam_stok = $this->xml_toplam_stok + 1;

        $urun_mevcut_durum = false;

        $islem_yapma = false;

        $kontrol_result = [
            "stok_id" => 0,
            "durum" => false,
            "varyant" => false
        ];

        $stok_id = 0;


        if ($this->kontrol_secenekleri == true) {

            $kontrol_data = [];

            foreach ($this->kontrol_secenekleri_tersten as $key => $val) {

                if (isset($loop_data[$val])) {

                    $kontrol_data[$key] = $loop_data[$val];

                }
            }


            $xml_stok = $this->xml_stok_eslesme_listesi_sablon;

            foreach ($this->eslesme_listesi_tersten as $key => $val) {

                if (isset($loop_data[$val])) {

                    $xml_stok[$key] = $loop_data[$val];

                }

            }

            $this->xml_stok_listesi[] = $xml_stok;


            $kontrol_result = $this->xmlmodel->stokVarmi($kontrol_data);

            $urun_mevcut_durum = $kontrol_result["durum"];

            if ($urun_mevcut_durum) {

                $stok_id = $kontrol_result["stok_id"];

                $this->eslesen_stok = $this->eslesen_stok + 1;
            }

            if ($kontrol_result["varyant"] == true) {

                $islem_yapma = true;

            }

        }

        if ($islem_yapma == false && $urun_mevcut_durum == false && $this->yoksa_ekle_durum == true) {

            $yoksa_ekle_data = [];

            $yoksa_ekle_data["stok_islem_kod"] = $this->islem_kod;

            $yoksa_ekle_data["stok_kayit_kod"] = $this->islem_kod;

            $yoksa_ekle_data["son_xml_kod"] = $this->islem_kod;


            foreach ($this->yoksa_eklenecekler_tersten as $key => $val) {

                if (isset($loop_data[$val])) {

                    $yoksa_ekle_data[$key] = $loop_data[$val];

                }

            }


            $islem_durum = false;

            if (isset($yoksa_ekle_data["stok_barkod_no"])) {

                if (trim($yoksa_ekle_data["stok_barkod_no"]) != "") {

                    $islem_durum = true;
                }
            }

            if (isset($yoksa_ekle_data["stok_kod"])) {

                if (trim($yoksa_ekle_data["stok_kod"]) != "") {
                    $islem_durum = true;

                }
            }


            $ekleme_sonuc = 0;


            if ($islem_durum == true && $this->test_mod == false) {


                $ekleme_sonuc = $this->xmlmodel->stokEkle($yoksa_ekle_data, $this->fiyatlara_vergi_dahil);

            }


            if ($ekleme_sonuc > 0) {

                $stok_id = $ekleme_sonuc;

                $this->yeni_eklenen_stok = $this->yeni_eklenen_stok + 1;

            }

        } else if ($islem_yapma == false && $urun_mevcut_durum == true && $this->varsa_guncelle_durum == true) {

            $varsa_guncelle_data = [];

            $varsa_guncelle_data["stok_islem_kod"] = $this->islem_kod;

            $varsa_guncelle_data["son_xml_kod"] = $this->islem_kod;


            foreach ($this->varsa_guncellenecekler_tersten as $key => $val) {

                if (isset($loop_data[$val])) {

                    $varsa_guncelle_data[$key] = $loop_data[$val];

                }
            }


            $guncelleme_onay_durum = false;

            if (
                isset($varsa_guncelle_data["stok_barkod_no"]) ||
                isset($varsa_guncelle_data["stok_kod"]) ||
                isset($varsa_guncelle_data["stok_adi"]) ||
                isset($varsa_guncelle_data["stok_satis_fiyati"]) ||
                isset($varsa_guncelle_data["stok_satis_fiyati2"]) ||
                isset($varsa_guncelle_data["stok_satis_fiyati3"]) ||
                isset($varsa_guncelle_data["stok_kdv_oran"]) ||
                isset($varsa_guncelle_data["stok_doviz"]) ||
                isset($varsa_guncelle_data["stok_resim"]) ||
                isset($varsa_guncelle_data["stok_resim2"]) ||
                isset($varsa_guncelle_data["stok_resim3"]) ||
                isset($varsa_guncelle_data["stok_resim4"]) ||
                isset($varsa_guncelle_data["stok_resim5"]) ||
                isset($varsa_guncelle_data["stok_detayi"]) ||
                isset($varsa_guncelle_data["stok_marka"])
            ) {

                $guncelleme_onay_durum = true;
            }


            $stok_guncelle = false;

            if ($guncelleme_onay_durum == true && $this->test_mod == false) {
                $stok_guncelle = $this->xmlmodel->stokGuncelle($varsa_guncelle_data, $kontrol_result["stok_id"], $this->fiyatlara_vergi_dahil);

            }


            if ($stok_guncelle) {

                $this->guncellenen_stok = $this->guncellenen_stok + 1;
            }

        }


        if ($this->varyantlama_durum == true && $stok_id != 0) {

            if (isset($data[$this->varyantlama_alan_adi])) {

                $varyant_array = $data[$this->varyantlama_alan_adi];

                $status = $this->executeXmlVariantData($stok_id, $data, $varyant_array, $loop_data);

            }

        }


    }


    private function executeXmlVariantData($ust_stok_id, $data = [], $varyant_array = [], $loop_data = [])
    {

        if (empty($varyant_array)) {

            return false;
        }

        foreach ($varyant_array as $key => $value) {

            foreach ($value as $key2 => $value2) {

                $this->varyant_loop_result = [];

                $kontrol_data = [];

                $this->toplam_varyant = $this->toplam_varyant + 1;

                $this->xmlVaryantLoop($value2);

                foreach ($this->kontrol_secenekleri_tersten as $key3 => $val3) {

                    if (isset($this->varyant_loop_result[$val3])) {

                        $kontrol_data[$key3] = $this->varyant_loop_result[$val3];

                    }
                }


                $xml_stok = $this->xml_stok_eslesme_listesi_sablon;

                foreach ($this->eslesme_listesi_tersten as $key => $val) {

                    if (isset($this->varyant_loop_resul[$val])) {

                        $xml_stok[$key] = $this->varyant_loop_result[$val];

                    }
                }

                $xml_stok["stok_parent_id"] = $ust_stok_id;

                $this->xml_stok_listesi[] = $xml_stok;

                $islem_durum = false;


                if (isset($kontrol_data["varyant_barkod"])) {

                    if (trim($kontrol_data["varyant_barkod"]) != "") {

                        $islem_durum = true;
                    }
                }

                if (isset($kontrol_data["varyant_stok_kod"])) {

                    if (trim($kontrol_data["varyant_stok_kod"]) != "") {
                        $islem_durum = true;

                    }
                }


                $kontrol_result = $this->xmlmodel->stokVarmi($kontrol_data, true);

                if ($kontrol_result["durum"] == true) {

                    $this->eslesen_varyant = $this->eslesen_varyant + 1;
                }


                if ($kontrol_result["durum"] == false && $this->yoksa_ekle_durum == true && $islem_durum == true) {

                    $yoksa_ekle_data = [];

                    $yoksa_ekle_data["stok_islem_kod"] = $this->islem_kod;

                    $yoksa_ekle_data["stok_kayit_kod"] = $this->islem_kod;

                    $yoksa_ekle_data["son_xml_kod"] = $this->islem_kod;

                    foreach ($this->yoksa_eklenecekler_tersten as $key => $val) {

                        if (isset($this->varyant_loop_result[$val])) {

                            $yoksa_ekle_data[$key] = $this->varyant_loop_result[$val];

                        }
                    }


                    $ekleme_sonuc = 0;

                    $islem_yap = false;

                    if (isset($yoksa_ekle_data["varyant_barkod"])) {

                        if (trim($yoksa_ekle_data["varyant_barkod"]) != "") {

                            $islem_yap = true;
                        }
                    }

                    if (isset($yoksa_ekle_data["varyant_stok_kod"])) {

                        if (trim($yoksa_ekle_data["varyant_stok_kod"]) != "") {

                            $islem_yap = true;

                        }
                    }

                    if ($islem_yap == true && $this->test_mod == false) {

                        $ekleme_sonuc = $this->xmlmodel->varyantStokEkle($yoksa_ekle_data, $ust_stok_id, $this->fiyatlara_vergi_dahil);

                    }

                    if ($ekleme_sonuc > 0) {

                        $this->yeni_eklenen_varyant = $this->yeni_eklenen_varyant + 1;

                    }


                } else if ($kontrol_result["durum"] == true && $this->varsa_guncelle_durum == true && $islem_durum == true) {


                    $varsa_guncelle_data = [];

                    $varsa_guncelle_data["stok_islem_kod"] = $this->islem_kod;

                    $varsa_guncelle_data["son_xml_kod"] = $this->islem_kod;


                    foreach ($this->varsa_guncellenecekler_tersten as $key => $val) {

                        if (isset($this->varyant_loop_result[$val])) {

                            $varsa_guncelle_data[$key] = $this->varyant_loop_result[$val];

                        }
                    }


                    $guncelleme_onay_durum = false;

                    if (
                        isset($varsa_guncelle_data["varyant_barkod"]) ||
                        isset($varsa_guncelle_data["varyant_stok_kod"]) ||
                        isset($varsa_guncelle_data["varyant_satis_fiyat1"]) ||
                        isset($varsa_guncelle_data["varyant_satis_fiyat2"]) ||
                        isset($varsa_guncelle_data["varyant_satis_fiyat3"]) ||
                        isset($varsa_guncelle_data["stok_varyant_adi"]) ||
                        isset($varsa_guncelle_data["stok_varyant_deger"])) {

                        $guncelleme_onay_durum = true;
                    }

                    $stok_guncelle = false;

                    if ($guncelleme_onay_durum == true && $this->test_mod == false) {

                        $stok_guncelle = $this->xmlmodel->varyantStokGuncelle($varsa_guncelle_data, $kontrol_result["stok_id"], $ust_stok_id, $this->fiyatlara_vergi_dahil);

                    }

                    if ($stok_guncelle) {

                        $this->guncellenen_varyant = $this->guncellenen_varyant + 1;
                    }

                }
            }

        }

        return true;
    }


}
