<?php

namespace App\Controller\Import;

use \Dipa\Controller;

class importActionController extends \Dipa\Controller {

    private $importmodel;
    private $xmlurl;
    private $stokkod;
    private $barkod;
    private $stokadi;
    private $barkod_def;
    private $marka_adi;
    private $marka_adi_def;
    private $alis_fiyat;
    private $alis_fiyat_def;
    private $satis_fiyat;
    private $satis_fiyat_def;
    private $vergi_oran;
    private $vergi_oran_def;
    private $para_birim;
    private $para_birim_def;
    private $stok_tip;
    private $stok_tip_def;
    private $stok_detay;
    private $stok_detay_def;
    private $vergi_oran_durum;
    private $vergi_oran_durum_def;
    private $mevcut_durum;
    private $stok_birimi;
    private $stok_resim;
    private $stok_resim_def;
    //VARYANTLAR

    private $varyant_ana_adi;
    private $varyant_alt_adi;
    private $v_stok_kod;
    private $v_barcode;
    private $v_alis_fiyat;
    private $v_alis_fiyat_def;
    private $v_satis_fiyat;
    private $v_satis_fiyat_def;
    private $v_options = false;
    private $v_options_ana_adi;
    private $v_options_alt_adi;
    private $v_adi;
    private $v_adi_def;
    private $v_deger;
    private $v_deger_def;
    private $i = 0;

    public function __construct() {
        parent::__construct(true);

        $this->xmlurl = $this->request->input("xmlurl");
        $this->stokkod = $this->request->input("stokkod");
        $this->barkod = $this->request->input("barkod");
        $this->stokadi = $this->request->input("stokadi");
        $this->barkod_def = $this->request->input("barkod_def");
        $this->marka_adi = $this->request->input("marka_adi");
        $this->marka_adi_def = $this->request->input("marka_adi_def");
        $this->alis_fiyat = $this->request->input("alis_fiyat");
        $this->alis_fiyat_def = $this->request->input("alis_fiyat_def");
        $this->satis_fiyat = $this->request->input("satis_fiyat");
        $this->satis_fiyat_def = $this->request->input("satis_fiyat_def");
        $this->vergi_oran = $this->request->input("vergi_oran");
        $this->vergi_oran_def = $this->request->input("vergi_oran_def");
        $this->para_birim = $this->request->input("para_birim");
        $this->para_birim_def = $this->request->input("para_birim_def");
        $this->stok_birimi = $this->request->input("stok_birimi");
        $this->stok_birimi_def = $this->request->input("stok_birimi_def");
        $this->stok_resim = $this->request->input("stok_resim");
        $this->stok_resim_def = $this->request->input("stok_resim_def");
        $this->stok_tip = $this->request->input("stok_tip");
        $this->stok_tip_def = $this->request->input("stok_tip_def");
        $this->stok_detay = $this->request->input("stok_detay");
        $this->stok_detay_def = $this->request->input("stok_detay_def");
        $this->varyant_ana_adi = $this->request->input("varyant_ana_adi");
        $this->varyant_alt_adi = $this->request->input("varyant_alt_adi");
        $this->vergi_oran_durum = $this->request->input("vergi_oran_durum");
        $this->vergi_oran_durum_def = $this->request->input("vergi_oran_durum_def");
        $this->mevcut_durum = $this->request->input("mevcut_durum");
        //Varyantlar
        $this->v_stok_kod = $this->request->input("v_stok_kod");
        $this->v_barcode = $this->request->input("v_barcode");
        $this->v_alis_fiyat = $this->request->input("v_alis_fiyat");
        $this->v_alis_fiyat_def = $this->request->input("v_alis_fiyat_def");
        $this->v_satis_fiyat = $this->request->input("v_satis_fiyat");
        $this->v_satis_fiyat_def = $this->request->input("v_satis_fiyat_def");
        $this->v_options_ana_adi = $this->request->input("v_options_ana_adi");
        $this->v_options_alt_adi = $this->request->input("v_options_alt_adi");



        $this->v_adi = $this->request->input("v_adi");
        $this->v_adi_def = $this->request->input("v_adi_def");
        $this->v_deger = $this->request->input("v_deger");
        $this->v_deger_def = $this->request->input("v_deger_def");


        if ($this->request->input("v_options") == "on") {
            $this->v_options = true;
            $this->v_adi = $this->request->input("v_adi_2");
            $this->v_adi_def = $this->request->input("v_adi_def_2");
            $this->v_deger = $this->request->input("v_deger_2");
            $this->v_deger_def = $this->request->input("v_deger_def_2");
        }






        $account_no = Controller::$account_no;

        $image_folder = MEDIA_DIR . "/" . $account_no . "/s/stok-foto";

        if (!file_exists($image_folder)) {

            mkdir($image_folder, 0777, true);

            chmod($image_folder, 0777);
        }
    }

    public function xmlStokEkleGuncelle() {

        set_time_limit(0);
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        header('Content-type: text/html; charset=utf-8');
        header('Surrogate-Control: BigPipe/1.0');
        header("Cache-Control: no-cache, must-revalidate");
        header('X-Accel-Buffering: no');

        $this->importmodel = $this->model("import", "stokImportModel");

        $request = $this->request;



        ob_end_flush();
        ob_start();

        $xmlurldata = file_get_contents($this->xmlurl);

        $stoklar = json_decode(json_encode(simplexml_load_string($xmlurldata, null, LIBXML_NOCDATA)), true);

        echo "Url:" . $this->xmlurl . " Xml Dosyası İndirildi<br>";
        echo "XML Uygulanıyor.... <br>";
        echo "Bu aşamada kesinlikle tarayıcınızı kapatmayın işlem bittiğinde haber vereceğiz uzun sürebilir! <br>";


        echo "<div id='loading'></div>";
        echo "<div id='mesaj'></div>";

        echo '<script>function loader(str){document.getElementById("loading").innerHTML = str;}</script>';
        echo '<script>function msg(str){document.getElementById("mesaj").innerHTML = str;}</script>';

        flush();




        foreach ($stoklar as $keys => $values) {

            if (is_array($values)) {

                foreach ($values as $key => $value) {

                    $this->parseData($value);

                    $this->i++;

                    echo "<div class='writer'><script>loader(\"islenen:" . $this->i . "\");</script></div>";
                    flush();
                }
            }
        }

        echo "Aktarım Tamamlandı.... <br><a href='" . \Dipa\App::getConfig("url") . "'>Anasayfa..</a>";


        \Dipa\Io\Log::write("Stoklar xml dosyasından import edildi", self::$account_no, self::$userInfo["id"]);
    }

    private function getData($name, $def, $data) {

        if ($name == "no") {
            return $def;
        } else if ($data == NULL) {

            return $def;
        } else {


            if (isset($data[$name])) {

                if (is_array($data[$name])) {

                    return $def;
                } else if (empty($data[$name])) {

                    return $def;
                } else if ($data[$name] == NULL) {

                    return $def;
                } else {

                    return $data[$name];
                }
            } else {

                return $def;
            }
        }
    }

    private function seflink($text) {
        $find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
        $replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');
        $text = strtolower(str_replace($find, $replace, $text));
        $text = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $text = str_replace(' ', '-', $text);
        return $text;
    }

    private function parseData($data) {

        $barkod_no = $this->getData($this->barkod, $this->barkod_def, $data);

        $stok["stok_barkod_no"] = $barkod_no;
        $stok["stok_kod"] = $this->getData($this->stokkod, "", $data);
        $stok["stok_ozel_kod"] = "";
        $stok["stok_adi"] = $this->getData($this->stokadi, "", $data);
        $stok["stok_cinsi"] = "";
        $stok["stok_sinif"] = "";
        $stok["stok_grup"] = "";
        $stok["stok_resim"] = $this->getData($this->stok_resim, $this->stok_resim_def, $data);
        $stok["stok_adet"] = 0;
        $stok["stok_ozel_urun_adet"] = 0;
        $stok["stok_min_seviyesi"] = 0;
        $stok["stok_max_seviyesi"] = 999999;
        $stok["stok_detayi"] = $this->getData($this->stok_detay, $this->stok_detay_def, $data);
        $stok["stok_create_id"] = "";
        $stok["last_val"] = "";
        $stok["aktif"] = 1;
        $stok["stok_standart_adet"] = 1;
        $stok["stok_satis_iskonto_oran"] = 0;
        $stok["stok_alim_iskonto_oran"] = 0;
        $stok["stok_seo_url"] = $this->seflink($stok["stok_adi"]);
        $stok["stok_perakende_satis"] = 1;
        $stok["stok_web_satis"] = 1;
        $stok["stok_portal_satis"] = 1;
        $stok["stok_kdv_oran"] = $this->getData($this->vergi_oran, $this->vergi_oran_def, $data);

        $kdv_duzelt = 0;

        if ($stok["stok_kdv_oran"] > 9) {

            $kdv_duzelt = "1." . $stok["stok_kdv_oran"];
        } else {

            $kdv_duzelt = "1.0" . $stok["stok_kdv_oran"];
        }

        if ($this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data) == 1) {

            //Vergiler hariç gelen fiyat
            $stok["stok_alis_fiyati"] = $this->getData($this->alis_fiyat, $this->alis_fiyat_def, $data);
            $stok["stok_satis_fiyati"] = $this->getData($this->satis_fiyat, $this->satis_fiyat_def, $data);

            if ($stok["stok_kdv_oran"] > 0) {

                $stok["stok_kdv_dahil_satis_fiyati"] = $stok["stok_satis_fiyati"] * $kdv_duzelt;
            } else {
                $stok["stok_kdv_dahil_satis_fiyati"] = $stok["stok_satis_fiyati"];
            }
        } else if ($this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data) == 2) {

            $stok["stok_kdv_dahil_satis_fiyati"] = $this->getData($this->satis_fiyat, $this->satis_fiyat_def, $data);

            if ($stok["stok_kdv_oran"] > 0) {

                if ($stok["stok_alis_fiyati"] > 0) {

                    $stok["stok_alis_fiyati"] = $stok["stok_alis_fiyati"] / $kdv_duzelt;
                }

                if ($stok_satis_fiyat > 0) {

                    $stok["stok_satis_fiyati"] = $stok["stok_satis_fiyati"] / $kdv_duzelt;
                }
            } else {

                $stok["stok_alis_fiyati"] = $this->getData($this->alis_fiyat, $this->alis_fiyat_def, $data);
                $stok["stok_satis_fiyati"] = $this->getData($this->satis_fiyat, $this->satis_fiyat_def, $data);
            }
        }

        $stok["stok_birimi"] = $this->getData($this->stok_birimi, $this->stok_birimi_def, $data);
        $stok["stok_kdv_detay"] = $this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data);
        $stok["stok_fiyat_vergi_durum"] = $this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data);
        $stok["stok_max_iskontolu_satis_fiyati"] = $stok["stok_satis_fiyati"];
        $stok["stok_doviz"] = $this->getData($this->para_birim, $this->para_birim_def, $data);
        $stok["stok_marka_id"] = 0;
        $stok["stok_marka_ad"] = $this->getData($this->marka_adi, $this->marka_adi_def, $data);
        $stok["stok_tipi"] = 0;
        $stok["stok_parent_id"] = 0;
        $stok["stok_varyant_adi"] = "";
        $stok["stok_varyant_deger"] = "";

        $stok_id = $this->importmodel->stokEkleGuncelle($stok);

        if (isset($data[$this->varyant_ana_adi])) {

            $varyant = $data[$this->varyant_ana_adi];

            if (isset($varyant[$this->varyant_alt_adi])) {

                $varyantlar = $varyant[$this->varyant_alt_adi];

                foreach ($varyantlar as $key => $value) {


                    if ($this->v_options == true) {

                        $this->optionsvaryants($stok,$kdv_duzelt, $stok_id, $varyantlar, $key, $value,$data);
                    } else {

                        $this->varyant($stok,$kdv_duzelt, $stok_id, $varyantlar, $key, $value,$data);
                    }


                    $stok["stok_parent_id"] = 0;
                }
            }
        }


        $stok_id = 0;
    }

    private function varyant($stok,$kdv_duzelt, $stok_id, $varyantlar, $key, $value,$data) {


        if (isset($value[$this->v_adi])) {

            $stok["stok_parent_id"] = $stok_id;

            $stok["stok_varyant_adi"] = $this->getData($this->v_adi, $this->v_adi_def, $value);

            $stok["stok_varyant_deger"] = $this->getData($this->v_deger, $this->v_deger_def, $value);

            $stok["stok_barkod_no"] = $this->getData($this->v_barcode, "", $value);

            $stok["stok_kod"] = $this->getData($this->v_stok_kod, "", $value);

            if ($this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data) == 1) {

                $stok["stok_alis_fiyati"] = $this->getData($this->v_alis_fiyat, $this->v_alis_fiyat_def, $value);

                $stok["stok_satis_fiyati"] = $this->getData($this->v_satis_fiyat, $this->v_satis_fiyat_def, $value);

                if ($stok["stok_kdv_oran"] > 0) {

                    $stok["stok_kdv_dahil_satis_fiyati"] = $stok["stok_satis_fiyati"] * $kdv_duzelt;
                } else {
                    $stok["stok_kdv_dahil_satis_fiyati"] = $stok["stok_satis_fiyati"];
                }
            } else if ($this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data) == 2) {

                $stok["stok_kdv_dahil_satis_fiyati"] = $this->getData($this->v_satis_fiyat, $this->v_satis_fiyat_def, $value);

                if ($stok["stok_kdv_oran"] > 0) {

                    if ($stok["stok_alis_fiyati"] > 0) {

                        $stok["stok_alis_fiyati"] = $stok["stok_alis_fiyati"] / $kdv_duzelt;
                    }

                    if ($stok_satis_fiyat > 0) {

                        $stok["stok_satis_fiyati"] = $stok["stok_satis_fiyati"] / $kdv_duzelt;
                    }
                } else {

                    $stok["stok_alis_fiyati"] = $this->getData($this->v_alis_fiyat, $this->v_alis_fiyat_def, $value);
                    $stok["stok_satis_fiyati"] = $this->getData($this->v_satis_fiyat, $this->v_satis_fiyat_def, $value);
                }
            }





            $this->importmodel->varyantEkleGuncelle($stok);
            $this->i++;
            echo "<div class='writer'><script>loader(\"islenen:" . $this->i . "\");</script></div>";
            flush();
        }
    }

    private function optionsvaryants($stok,$kdv_duzelt, $stok_id, $varyantlar, $key, $value,$data) {






        if (isset($value[$this->v_options_ana_adi][$this->v_options_alt_adi])) {

            $stok["stok_parent_id"] = $stok_id;

            $stok["stok_varyant_adi"] = $this->getData($this->v_adi, $this->v_adi_def, $value[$this->v_options_ana_adi][$this->v_options_alt_adi]);
            $stok["stok_varyant_deger"] = $this->getData($this->v_deger, $this->v_deger_def, $value[$this->v_options_ana_adi][$this->v_options_alt_adi]);
            $stok["stok_barkod_no"] = $this->getData($this->v_barcode, "", $value);
            $stok["stok_kod"] = $this->getData($this->v_stok_kod, "", $value);

            if ($this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data) == 1) {

                $stok["stok_alis_fiyati"] = $this->getData($this->v_alis_fiyat, $this->v_alis_fiyat_def, $value);

                $stok["stok_satis_fiyati"] = $this->getData($this->v_satis_fiyat, $this->v_satis_fiyat_def, $value);

                if ($stok["stok_kdv_oran"] > 0) {

                    $stok["stok_kdv_dahil_satis_fiyati"] = $stok["stok_satis_fiyati"] * $kdv_duzelt;
                } else {
                    $stok["stok_kdv_dahil_satis_fiyati"] = $stok["stok_satis_fiyati"];
                }
            } else if ($this->getData($this->vergi_oran_durum, $this->vergi_oran_durum_def, $data) == 2) {

                $stok["stok_kdv_dahil_satis_fiyati"] = $this->getData($this->v_satis_fiyat, $this->v_satis_fiyat_def, $value);

                if ($stok["stok_kdv_oran"] > 0) {

                    if ($stok["stok_alis_fiyati"] > 0) {

                        $stok["stok_alis_fiyati"] = $stok["stok_alis_fiyati"] / $kdv_duzelt;
                    }

                    if ($stok_satis_fiyat > 0) {

                        $stok["stok_satis_fiyati"] = $stok["stok_satis_fiyati"] / $kdv_duzelt;
                    }
                } else {

                    $stok["stok_alis_fiyati"] = $this->getData($this->v_alis_fiyat, $this->v_alis_fiyat_def, $value);
                    $stok["stok_satis_fiyati"] = $this->getData($this->v_satis_fiyat, $this->v_satis_fiyat_def, $value);
                }
            }





            $this->importmodel->varyantEkleGuncelle($stok);
            $this->i++;
            echo "<div class='writer'><script>loader(\"islenen:" . $this->i . "\");</script></div>";
            flush();
        }
    }

}
