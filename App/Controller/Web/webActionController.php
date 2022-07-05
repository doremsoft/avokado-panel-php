<?php
namespace App\Controller\Web;

use DOMDocument;

class webActionController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }



    
      public function save() {

          $model = $this->model("web","webViewModel");

          $file_name = $_POST["fileName"];

          $ex = explode("/",$file_name);

          $id = end($ex);

          $html = $_POST["html"];

          echo $html;


          $d = new DOMDocument;

          $mock = new DOMDocument;

          $d->loadHTML($html);

          /* get the element to be deleted */
          $div=$d->getElementById('footer-block-div');


          if( $div && $div->nodeType==XML_ELEMENT_NODE ){
              $div->parentNode->removeChild( $div );
          }


          $body = $d->getElementsByTagName('body')->item(0);


          $head_style = "";

          if ($body->hasAttributes()) {

              $head_style.= "body{". $body->getAttribute('style')."}";
          }


          foreach ($body->childNodes as $child){

              $mock->appendChild($mock->importNode($child, true));

          }

          $html =  $mock->saveHTML();


          $html = htmlentities($html, ENT_QUOTES);

          $update = $model->updateHtmlPage($id ,trim($html));

          if($update == "ok"){

              echo "Sayfa Güncellendi";

          }else{
              echo $update;
          }


    }

    private function seflink($text)
    {
        $find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
        $replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');
        $text = strtolower(str_replace($find, $replace, $text));
        $text = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $text = str_replace(' ', '-', $text);
        return $text;
    }


    public function add() {


        $demo_path = PUBLIC_DIR.DS.'assets'.DS.'fuse/plugins/htmleditor/';



        $model = $this->model("web","webViewModel");

        $page_name = $_POST["fileName"];

        $page_url = $this->seflink($page_name);

        $startTemplateUrl = $_POST["startTemplateUrl"];

        $html = file_get_contents($demo_path.$startTemplateUrl);

        $html = htmlentities($html, ENT_QUOTES);

        $update = $model->newHtmlPage($page_name,$page_url,$html);

        if($update){

            echo "Sayfa Güncellendi";

        }else{
            echo "Sayfa Güncellenemedi!";
        }


    }





    public function sayfalariSifirla(){

        $model = $this->model("web","webViewModel");

        $sifirla = $model->sayfalarSifirla();

        if($sifirla){

            \Dipa\Io\Log::write("websitesi sayfaları oluşturuldu", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi sayfaları oluşturuldu")->to("web/sayfalar");

        }else{

            \Dipa\Io\Log::write("Websitesi sayfaları oluşturulamadı!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi sayfaları oluşturulamadı!")->back();
        }

    }

    public function ayarSifirla(){

        $model = $this->model("web","webViewModel");

        $sifirla = $model->ayarlariSifirla();

        if($sifirla){

            \Dipa\Io\Log::write("websitesi ayarları sıfırlandı", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi ayarları sıfırlandı")->to("web");

        }else{

            \Dipa\Io\Log::write("Websitesi ayarları sıfırlanamadı!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi ayarları sıfırlanamadı!")->back();
        }

    }
    public function ayarGuncelle(){

        $model = $this->model("web","webViewModel");

        $ayar_adi = $this->request->input("ayar_adi");

        $durum = $this->request->input("durum");

        if($durum == "on"){

            $durum = 1;
        }else{
            $durum = 0;
        }

        $guncelle = $model->ayarGuncelle($ayar_adi,$durum);

        if($guncelle){

                \Dipa\Io\Log::write("websitesi ayarı güncellendi", self::$account_no, self::$userInfo["id"]);


                $this->header->result("success", "Websitesi ayarları güncellendi")->back();

            }else{

                \Dipa\Io\Log::write("Websitesi ayarları güncellenemedi!", self::$account_no, self::$userInfo["id"]);

                $this->header->result("fail", "Websitesi ayarları güncellenemedi!")->back();
            }

    }

    public function bilgileriGuncelle(){

        $model = $this->model("web","webViewModel");

        $guncelle = $model->sitebilgileriGuncelle($this->request);

        if($guncelle){

            \Dipa\Io\Log::write("websitesi bilgileri güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi bilgileri güncellendi")->to("web/site-bilgileri");

        }else{

            \Dipa\Io\Log::write("Websitesi bilgileri güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi bilgileri güncellenemedi!")->back();
        }

    }


    public function sayfaTemaAyarlariGuncelle(){

        $model = $this->model("web","webViewModel");

        $guncelle = $model->sayfaTemaAyarlariGuncelle($this->request);


        if($guncelle){

            \Dipa\Io\Log::write("websitesi sayfa tasarımı güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi sayfa tasarımı güncellendi")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi sayfa tasarımı güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi sayfa tasarımı güncellenemedi!")->back();
        }



    }

    public function sayfaTasarimGuncelle(){

        $model = $this->model("web","webViewModel");

        $guncelle = $model->sayfaTasarimlariGuncelle($this->request);

        if($guncelle){

            \Dipa\Io\Log::write("websitesi sayfa tasarımı güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi sayfa tasarımı güncellendi")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi sayfa tasarımı güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi sayfa tasarımı güncellenemedi!")->back();
        }


    }




    public function temaSablonOlustur()   {

        $model = $this->model("web","webViewModel");

        $guncelle = $model->sablonSayfaEkle($this->request);

        if($guncelle){

            \Dipa\Io\Log::write("websitesi tema şablon sayfası eklendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi tema şablon sayfası eklendi")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi tema şablon sayfası eklenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi tema şablon sayfası eklenemedi!")->back();
        }


    }



    public function temaSablonGuncelle()   {

        $model = $this->model("web","webViewModel");

        $guncelle = $model->sablonGuncelle($this->request);

        if($guncelle){

            \Dipa\Io\Log::write("websitesi sayfası güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi sayfası güncellendi")->back();

        }else{

    \Dipa\Io\Log::write("Websitesi sayfası güncellenemedi!", self::$account_no, self::$userInfo["id"]);

    $this->header->result("fail", "Websitesi sayfası güncellenemedi!")->back();
}


}


public function sayfaGuncelle(){

        $model = $this->model("web","webViewModel");

        $guncelle = $model->sayfaGuncelle($this->request);

        if($guncelle){

            \Dipa\Io\Log::write("websitesi sayfası güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi sayfası güncellendi")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi sayfası güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi sayfası güncellenemedi!")->back();
        }


    }

    public function sayfaHtmlGuncelle(){

        $model = $this->model("web","webViewModel");

        $guncelle = $model->sayfaHtmlGuncelle($this->request);

        if($guncelle){

            \Dipa\Io\Log::write("websitesi sayfası güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi sayfası güncellendi")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi sayfası güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi sayfası güncellenemedi!")->back();
        }


    }


    public function slaytGuncelle(){


        $model = $this->model("web","webViewModel");

        $guncelle = $model->slaytGuncelle($this->request);


        if($guncelle){

            \Dipa\Io\Log::write("websitesi slayt güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi slayt güncellendi")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi slayt güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi slayt güncellenemedi!")->back();
        }



    }

    public function slaytKalemEkle(){


        $model = $this->model("web","webViewModel");

        $guncelle = $model->slaytKalemEkle($this->request);


        if($guncelle){

            \Dipa\Io\Log::write("websitesi slayt güncellendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi slayt güncellendi")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi slayt güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi slayt güncellenemedi!")->back();
        }

    }


    public function slaytOlustur(){


        $model = $this->model("web","webViewModel");

        $guncelle = $model->slatyOlustur($this->request);


        if($guncelle){

            \Dipa\Io\Log::write("websitesi slayt oluşturuldu", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Websitesi slayt oluşturuldu")->back();

        }else{

            \Dipa\Io\Log::write("Websitesi slayt oluşturulamadı!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi slayt oluşturulamadı!")->back();
        }

    }


    public function yeniSayfaEkle(){

        $model = $this->model("web","webViewModel");


        $guncelle = $model->yeniSayfaEkle($this->request);

        if($guncelle){

            \Dipa\Io\Log::write("websitesi sayfası eklendi", self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "websitesi sayfası eklendi.. \n Sayfayı Aktif Etmeniz Gerekmektedir!")->to("web/sayfalar");

        }else{

            \Dipa\Io\Log::write("Websitesi sayfası eklenemedi ", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Websitesi sayfası eklenemedi ")->back();
        }

    }


}