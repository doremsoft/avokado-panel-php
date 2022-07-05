<?php

namespace App\Controller\Web;

use Dipa\Controller;

class webViewController extends \Dipa\Controller
{

    private $menuler = [

        "Anasayfa" => "web",
        "Site Ayarları" => "web/site-ayarlar",
        "Site Bilgileri" => "web/site-bilgileri",
        "Üye İşlemleri" => "web/uye",
        "Görünüm Ayarları" => "web/gorunum",
        "Media Yönetimi" => "web/media",
        //"Menüler" => "web/menuler",
        "E-Ticaret Ayarları" => "web/eticaret",
        "Slay Yönetimi" => "web/slider",
        //"Galeriler" => "web/galeriler",
        "Sayfalar" => "web/sayfalar",
        "Tema Editör" => "web/tema-editor",


    ];


    public function __construct()
    {
        parent::__construct(true);
    }

    public function index()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        return $this->view("web/index", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);

    }


    public function siteAyarlar()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/site-ayarlar";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        return $this->view("web/ayarlar", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);


    }

    public function media()
    {

        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/media";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();

        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }

        if (isset($webayarlari["connection_key"])) {

            $webayarlari["connection_key"] = md5(Controller::$account_details["media_key"]);

        }


        return $this->view("web/media", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);


    }


    public function siteBilgiler()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/site-bilgileri";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        return $this->view("web/bilgiler", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);


    }


    public function menuler()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/menuler";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        return $this->view("web/menuler", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);


    }

    public function eticaret()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/eticaret";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        return $this->view("web/eticaret", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);


    }

    public function gorunum()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/gorunum";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }

        $temalar = [

            "Standart" => "default"

        ];


        return $this->view("web/gorunum", [
            'menuler' => $this->menuler,
            'temalar' => $temalar,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);

    }

    public function sayfalar()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/sayfalar";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();

        $sayfalar = $model->getHtmlPages();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        return $this->view("web/sayfalar", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol,
            'sayfalar' => $sayfalar]);

    }


    public function editPage($id)
    {

        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/sayfalar";

        $model = $this->model("web", "webViewModel");

        $sayfa = $model->getHtmlPage($id);


        $webayarlari = $model->ayarlariAl();


        $altmenuler = [

            "web/sayfa-duzenle" => "Ayarlar",
            "web/sayfa-html-govde" => "Gövde İçerik",
            "web/sayfa-tema-ayarlar" => "Görünüm Ayarları",

            "web/sayfa-tasarim-duzenle" => "Kod Editör",
            /*
     "web/sayfa-editor"=>"Tasarım Editör",

     "web/sayfa-onbellek-duzenle"=>"Performans",
     "web/sayfa-seo"=>"Seo",
     "web/sayfa-db"=>"Veritabanı",

*/
        ];

        $aktif_alt_url = "web/sayfa-duzenle";

        $sablonlar = [

            "home/index" => "Anasayfa",
            "extra/page" => "Extra Sayfa",
            "extra/maintenance" => "Bakım Modu Sayfası",
            "extra/blank" => "Boş Sayfa",
        ];

        $kullanicisablonlar = [];


        if (isset($webayarlari["connection_key"])) {

            $webayarlari["connection_key"] = md5(Controller::$account_details["media_key"]);

        }


        return $this->view("web/sayfa_duzenle", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'sablonlar' => $sablonlar,
            'kullanicisablonlar' => $kullanicisablonlar,
            'sayfa' => $sayfa,
            'ayar' => $webayarlari,
            'altmenuler' => $altmenuler,
            'aktif_alt_url' => $aktif_alt_url
        ]);

    }

    public function sayfaTemaAyarlar($id)
    {

        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/sayfalar";

        $model = $this->model("web", "webViewModel");

        $sayfa = $model->getHtmlPage($id);

        $webayarlari = $model->ayarlariAl();


        $altmenuler = [

            "web/sayfa-duzenle" => "Ayarlar",
            "web/sayfa-html-govde" => "Gövde İçerik",
            "web/sayfa-tema-ayarlar" => "Görünüm Ayarları",
            "web/sayfa-tasarim-duzenle" => "Kod Editör",
        ];

        $aktif_alt_url = "web/sayfa-tema-ayarlar";

        $sablonlar = [

            "home/index" => "Anasayfa",
            "extra/page" => "Extra Sayfa",
            "extra/maintenance" => "Bakım Modu Sayfası",
            "extra/blank" => "Boş Sayfa",
        ];

        $kullanicisablonlar = [];

        $webconfig = $model->getConnectorData();

        $page_html_content_query = $model->webConnector($webconfig["web_url"] . "/connect", [
            "operation" => "get_template_file",
            "template_name" => $webconfig["template_name"],
            "file_name" => $sayfa["page_kod"] . "-config",
            "key" => $webconfig["connection_key"]
        ]);


        $config = [];


        if ($page_html_content_query["status"] == 1) {
            $cf = json_decode($page_html_content_query["content"], true);

            if (isset($cf["gorunum"])) {

                $config = $cf["gorunum"];

            }

        }

        $template_files = [];


        $page_html_content_query = $model->webConnector($webconfig["web_url"] . "/connect", [
            "operation" => "tema_only_files",
            "template_name" => $webconfig["template_name"],
            "key" => $webconfig["connection_key"]
        ]);

        if(isset($page_html_content_query["status"])){
            if ($page_html_content_query["status"] == 1) {

                $template_files = $page_html_content_query["file_list"];

            }

        }



        $parent_teplate_files = [];

        $page_html_content_query2 = $model->webConnector($webconfig["web_url"] . "/connect", [
            "operation" => "tema_only_parent_files",
            "template_name" => $webconfig["template_name"],
            "key" => $webconfig["connection_key"]
        ]);


        if (isset($page_html_content_query2["status"])) {


            if ($page_html_content_query2["status"] == 1) {

                $parent_teplate_files = $page_html_content_query2["file_list"];

            }
        }


        return $this->view("web/sayfa_tema_ayarlari", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'sablonlar' => $sablonlar,
            'kullanicisablonlar' => $kullanicisablonlar,
            'sayfa' => $sayfa,
            'ayar' => $webayarlari,
            'altmenuler' => $altmenuler,
            'aktif_alt_url' => $aktif_alt_url,
            'template_config' => $config,
            'teplate_files' => $template_files,
            'parent_teplate_files' => $parent_teplate_files
        ]);


    }

    public function sayfaHtmlGovde($id)
    {

        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/sayfalar";

        $model = $this->model("web", "webViewModel");

        $sayfa = $model->getHtmlPage($id);


        $webayarlari = $model->ayarlariAl();


        $altmenuler = [

            "web/sayfa-duzenle" => "Ayarlar",
            "web/sayfa-html-govde" => "Gövde İçerik",
            "web/sayfa-tema-ayarlar" => "Görünüm Ayarları",
            "web/sayfa-tasarim-duzenle" => "Kod Editör",
        ];

        $aktif_alt_url = "web/sayfa-html-govde";

        $sablonlar = [

            "home/index" => "Anasayfa",
            "extra/page" => "Extra Sayfa",
            "extra/maintenance" => "Bakım Modu Sayfası",
            "extra/blank" => "Boş Sayfa",
        ];

        $kullanicisablonlar = [];


        $sayfa["page_html_content"] = html_entity_decode($sayfa["page_html_content"], ENT_QUOTES);


        if (isset($webayarlari["connection_key"])) {

            $webayarlari["connection_key"] = md5(Controller::$account_details["media_key"]);

        }

        return $this->view("web/sayfa_html_duzenle", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'sablonlar' => $sablonlar,
            'kullanicisablonlar' => $kullanicisablonlar,
            'sayfa' => $sayfa,
            'ayar' => $webayarlari,
            'altmenuler' => $altmenuler,
            'aktif_alt_url' => $aktif_alt_url
        ]);


    }


    public function pageList()
    {


        $pages = [];

        $model = $this->model("web", "webViewModel");

        $pages = $model->getHtmlPages();

        return $this->view("web/page_list", [
            'pages' => $pages
        ]);
    }


    public function galeriler()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/galeriler";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        return $this->view("web/galeriler", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'ayarkontrol' => $ayarkontrol]);
    }


    public function slider()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/slider";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        $slaytlar = $model->slaytlar();

        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }

        if (isset($webayarlari["connection_key"])) {

            $webayarlari["connection_key"] = md5(Controller::$account_details["media_key"]);

        }


        return $this->view("web/slider", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'slaytlar' => $slaytlar,
            'ayarkontrol' => $ayarkontrol]);
    }


    public function slideEdit($id)
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/slider";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();

        $slayt = $model->slayt($id);

        $kalemler = $model->slaytKalemleri($slayt["slide_kod"]);

        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }
        if (isset($webayarlari["connection_key"])) {

            $webayarlari["connection_key"] = md5(Controller::$account_details["media_key"]);

        }


        return $this->view("web/slayt_duzenle", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'slayt' => $slayt,
            'kalemler' => $kalemler,
            'ayarkontrol' => $ayarkontrol]);
    }


    public function editTasarimPage($id)
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/sayfalar";

        $model = $this->model("web", "webViewModel");

        $sayfa = $model->getHtmlPage($id);


        $webayarlari = $model->ayarlariAl();

        $webconfig = $model->getConnectorData();


        $page_html_content_query = $model->webConnector($webconfig["web_url"] . "/connect", [
            "operation" => "get_full_template_file",
            "template_name" => $webconfig["template_name"],
            "file_name" => $sayfa["page_kod"],
            "key" => $webconfig["connection_key"]
        ]);


        $sayfa["page_html_content"] = "";
        $sayfa["page_html_head"] = "";
        $sayfa["page_html_footer"] = "";
        $sayfa["page_assets"] = "";

        if (isset($page_html_content_query["status"])) {

            if ($page_html_content_query["status"] == 1) {
                $sayfa["page_html_content"] = $page_html_content_query["content"];
                $sayfa["page_html_head"] = $page_html_content_query["head"];
                $sayfa["page_html_footer"] = $page_html_content_query["footer"];
                $sayfa["page_assets"] = $page_html_content_query["assets"];

                $sayfa["page_html_content"] = html_entity_decode($sayfa["page_html_content"], ENT_QUOTES);
            }


        } else {

            var_dump($page_html_content_query);
        }


        $altmenuler = [

            "web/sayfa-duzenle" => "Ayarlar",
            "web/sayfa-html-govde" => "Gövde İçerik",
            "web/sayfa-tema-ayarlar" => "Görünüm Ayarları",
            "web/sayfa-tasarim-duzenle" => "Kod Editör",
        ];

        $aktif_alt_url = "web/sayfa-tasarim-duzenle";

        $sablonlar = [

            "home/index" => "Anasayfa",
            "extra/page" => "Extra Sayfa",
            "extra/maintenance" => "Bakım Modu Sayfası",
            "extra/blank" => "Boş Sayfa",
        ];

        $kullanicisablonlar = [];


        return $this->view("web/sayfa_tasarim_duzenle", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'sablonlar' => $sablonlar,
            'kullanicisablonlar' => $kullanicisablonlar,
            'sayfa' => $sayfa,
            'altmenuler' => $altmenuler,
            'aktif_alt_url' => $aktif_alt_url,
            'ayar' => $webayarlari
        ]);


    }


    public function temaEditor()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/tema-editor";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();

        $ayarkontrol = 0;

        if (isset($webayarlari["webyayin"])) {

            $ayarkontrol = 1;
        }


        $template_files = [];

        $webconfig = $model->getConnectorData();

        $page_html_content_query = $model->webConnector($webconfig["web_url"] . "/connect", [
            "operation" => "tema_files",
            "template_name" => $webconfig["template_name"],
            "key" => $webconfig["connection_key"]
        ]);

        if (isset($page_html_content_query["status"])) {

            if ($page_html_content_query["status"] == 1) {

                $template_files = $page_html_content_query["file_list"];

            }

        }


        $secili_sayfa = "0";
        $file_data = "";

        if (isset($_GET["secili_sayfa"])) {

            $secili_sayfa = $_GET["secili_sayfa"];


            $page_html_content_query = $model->webConnector($webconfig["web_url"] . "/connect", [
                "operation" => "get_template_layout_file",
                "file_name" => $secili_sayfa,
                "template_name" => $webconfig["template_name"],
                "key" => $webconfig["connection_key"]
            ]);

            if ($page_html_content_query["status"] == 1) {


                $file_data = $page_html_content_query["file_data"];

            }


        }


        return $this->view("web/tema_editor", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
            'template_files' => $template_files,
            'file_data' => $file_data,
            'secili_sayfa' => $secili_sayfa,
            'ayarkontrol' => $ayarkontrol]);


    }


    public function uyeAyarlari()
    {


        $this->paket_kontrol(["web"], "web");

        $aktif_url = "web/uye";

        $model = $this->model("web", "webViewModel");

        $webayarlari = $model->ayarlariAl();


        return $this->view("web/uye", [
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
            'ayar' => $webayarlari,
        ]);

    }

}
