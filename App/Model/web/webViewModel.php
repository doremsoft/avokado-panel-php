<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class webViewModel extends Dimodel
{
    /*
     * Controller::$userInfo
     */
    /*
   function __construct()
   {
       // parent::__construct("pdo");
       // pdo - mysqli - pdox
   }
   */

    public function slaytlar(){
        $query = $this->getConnection()->prepare("SELECT * FROM web_sliders WHERE remove = 0 and owner_id = ? and account_no = ? ");

        $query->execute([Controller::$userInfo["owner_id"],Controller::$account_no]);


        return $query->fetchAll(PDO::FETCH_ASSOC);

    }

    public function slayt($id){
        $query = $this->getConnection()->prepare("SELECT * FROM web_sliders WHERE remove = 0 and owner_id = ? and account_no = ? and id = ?  ");

        $query->execute([Controller::$userInfo["owner_id"],Controller::$account_no,$id]);

        return $query->fetch();

    }

    public function slaytKalemleri($slider_kod){
        $query = $this->getConnection()->prepare("SELECT * FROM web_slider_items WHERE remove = 0 and owner_id = ? and account_no = ? and slider_kod = ? ");

        $query->execute([Controller::$userInfo["owner_id"],Controller::$account_no,$slider_kod]);


        return $query->fetchAll(PDO::FETCH_ASSOC);

    }

    public function slatyOlustur($request)
    {

        $query = $this->getConnection()->prepare("INSERT INTO  web_sliders
 SET  
 owner_id = ? , 
 account_no = ? ,
 slide_adi = ? , 
 slide_kod = ? ,
 slide_nick_name = ?");

        return  $query->execute([
            Controller::$userInfo["owner_id"],
            Controller::$account_no,
            $request->input("slayt_adi"),
            uniqid(),
            $request->input("slayt_nick")]);

    }

    public function slaytKalemEkle($request)
    {

        $slider_kod = $request->input("slider_kod");

        $query = $this->getConnection()->prepare("INSERT INTO  web_slider_items
 SET  
 owner_id = ? , 
 account_no = ? ,
 slider_kod = ? , 
 image_url = ? ,
 title = ? ,
 url = ? , 
 aciklama = ?  ");

      return  $query->execute([
            Controller::$userInfo["owner_id"],
            Controller::$account_no,
            $slider_kod,
            $request->input("image_url"),
            $request->input("title"),
            $request->input("url"),
            $request->input("aciklama")]);


    }

    public function slaytGuncelle($request){

        $slider_kod = $request->input("slider_kod");

       $delete =  $this->getConnection()->prepare("DELETE FROM  web_slider_items  WHERE  owner_id = ? and account_no = ? and slider_kod = ? ");
       $delete->execute([Controller::$userInfo["owner_id"],Controller::$account_no,$slider_kod]);

        $slide = $request->input("slide");
        if(is_array($slide)){


            foreach ($slide as $key => $value){


                $query = $this->getConnection()->prepare("INSERT INTO  web_slider_items
 SET  
 owner_id = ? , 
 account_no = ? ,
 slider_kod = ? , 
 image_url = ? ,
 title = ? ,
 url = ? , 
 aciklama = ?  ");

                $query->execute([
                    Controller::$userInfo["owner_id"],
                    Controller::$account_no,
                    $slider_kod,
                    $value["image_url"],
                    $value["title"],
                    $value["url"],
                    $value["aciklama"]]);

            }
            }



return true;



    }

    public function getHtmlPages()
    {
        $query = $this->getConnection()->prepare("SELECT * FROM web_sayfalar");

        $query->execute();


        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHtmlPage($id)
    {
        $query = $this->getConnection()->prepare("SELECT * FROM web_sayfalar WHERE id = ? ");

        $query->execute([$id]);


        return $query->fetch();
    }

    public function updateHtmlPage($id, $content_html_data)
    {


        $query = $this->getConnection()->prepare("SELECT page_kod  FROM web_sayfalar WHERE id = ? ");

        $query->execute([$id]);

        $sayfa =  $query->fetch();



        $hesap_no = Controller::$account_no;


        $page_kod = $sayfa["page_kod"];

        $webconfig = $this->getConnectorData();

        $page_html_content_query = $this->webConnector($webconfig["web_url"]."/connect" , [
            "operation" =>"set_template_file",
            "template_name"=> $webconfig["template_name"],
            "file_name"=> $page_kod."-content",
            "key" => $webconfig["connection_key"],
            "content"=>$content_html_data
        ]);



        if($page_html_content_query["status"] == 1){

            return "ok";


        }else{
            return $page_html_content_query["msg"];

        }

    }

    public function newHtmlPage($page_name, $page_url, $html)
    {


        $query = $this->getConnection()->prepare("INSERT INTO  web_sayfalar SET  page_html_content = ? , page_url = ? , page_name = ?  ");

        return $query->execute([$html, $page_url, $page_name]);

    }

    public function sayfaTemaAyarlariGuncelle($request){

        $page_kod = $request->input("kod");

        $configs =json_encode($request->input("config"));

        $webconfig = $this->getConnectorData();

        $page_html_content_query = $this->webConnector($webconfig["web_url"]."/connect" , [
            "operation" =>"set_page_config_file",
            "template_name"=> $webconfig["template_name"],
            "file_name"=> $page_kod,
            "key" => $webconfig["connection_key"],
            "config"=>$configs,
        ]);



        if($page_html_content_query["status"] == 1){

            return true;
        }else{
            return false;

        }


    }

    public function sayfaTasarimlariGuncelle($request){

        $hesap_no = Controller::$account_no;


        $assets = [];

        $assets_data = $request->input("asset");

        $head_i = 0;
        $footer_i = 0;

        if(is_array($assets_data)){

            foreach ($assets_data as $key => $val){

                $val["i"] =  $key;


                if($val["p"] == "head"){

                    $assets["head"][$head_i] = $val;

                    $head_i++;

                }else  if($val["p"] == "footer"){

                    $assets["footer"][$footer_i] = $val;
                    $footer_i++;
                }


            }

        }

        $page_kod = $request->input("page_kod");


        $page_html_head =htmlentities($request->input("page_html_head"), ENT_QUOTES);
        $page_html_content = htmlentities($request->input("page_html_content"), ENT_QUOTES);
        $page_html_footer = htmlentities($request->input("page_html_footer"), ENT_QUOTES);
        $assets = json_encode($assets);

        $webconfig = $this->getConnectorData();


        $page_html_content_query = $this->webConnector($webconfig["web_url"]."/connect" , [
            "operation" =>"set_full_template_file",
            "template_name"=> $webconfig["template_name"],
            "file_name"=> $page_kod,
            "key" => $webconfig["connection_key"],
            "content"=>$page_html_content,
            "head"=>$page_html_head,
            "footer"=>$page_html_footer,
            "assets"=>$assets
        ]);



        if($page_html_content_query["status"] == 1){

            return true;
        }else{
            return false;

        }

    }

    public function sayfaHtmlGuncelle($request){


        $hesap_no = Controller::$account_no;


        $update_date =date("Y-m-d H:i:s");


        $page_html_content = $request->input("page_html_content");

        $page_html_content = htmlentities($page_html_content,ENT_QUOTES);

        $id= $request->input("id");


        $query = $this->getConnection()->prepare("UPDATE  web_sayfalar SET page_html_content = ? , update_date = ?   WHERE id = ? and account_no = ?    ");

        return  $query->execute([
            $page_html_content,
            $update_date,
            $id,
            $hesap_no
        ]);



    }


    public function sablonSayfaEkle($request){


        $sayfa = $request->input("sayfa_adi");


        $webconfig = $this->getConnectorData();

        $page_html_content_query = $this->webConnector($webconfig["web_url"]."/connect" , [
            "operation" =>"new_template_layout_file",
            "template_name"=> $webconfig["template_name"],
            "file_name"=> $sayfa,
            "key" => $webconfig["connection_key"]
        ]);

        if($page_html_content_query["status"] == 1){

            return "ok";


        }else{
            return $page_html_content_query["msg"];

        }



    }

    public function sablonGuncelle($request){



        $sayfa = $request->input("sayfa");
        $twig_code = $request->input("template_layout");


        $webconfig = $this->getConnectorData();

        $page_html_content_query = $this->webConnector($webconfig["web_url"]."/connect" , [
            "operation" =>"set_template_layout_file",
            "template_name"=> $webconfig["template_name"],
            "file_name"=> $sayfa,
            "key" => $webconfig["connection_key"],
            "content"=>$twig_code
        ]);


        if($page_html_content_query["status"] == 1){

            return "ok";


        }else{
            return $page_html_content_query["msg"];

        }



    }



    public function sayfaGuncelle($request){

        $hesap_no = Controller::$account_no;

        $page_url = $request->input("page_url");
        $page_name = $request->input("page_name");
        $meta_title = $request->input("meta_title");
        $meta_description = $request->input("meta_description");
        $activate = $request->input("activate");

        $urun_filtre = $request->input("urun_filtre");

        $cache_block = $request->input("cache_block");

        if($activate == "on"){
            $activate = 1;
        }else{
            $activate = 0;
        }

        if($cache_block == "on"){
            $cache_block = 1;
        }else{
            $cache_block = 0;
        }

        $update_date =date("Y-m-d H:i:s");
        $page_kod = $request->input("page_kod");
        $id= $request->input("id");


        $query = $this->getConnection()->prepare("UPDATE  web_sayfalar SET  page_url = ? , 
page_name = ? , 
meta_title = ? , 
meta_description = ? , 
activate = ? , 
update_date = ? , 
cache_block = ?  ,
urun_filtre = ? 
WHERE id = ? and account_no = ?    ");

      return  $query->execute([ $page_url,
            $page_name,
            $meta_title,
            $meta_description,
            $activate,
            $update_date,
          $cache_block,
            $urun_filtre,
            $id,
            $hesap_no
        ]);







    }


    public function sitebilgileriGuncelle($request)
    {

        $hesap_no = Controller::$account_no;

        $site_mail =  $request->input("site_mail");
        $site_telefon = $request->input("site_telefon");
        $site_adres = $request->input("site_adres");
        $site_title =  $request->input("site_title");
        $site_adi =  $request->input("site_adi");
        $yetkili_ad = $request->input("yetkili_ad");

        $owner_id = Controller::$userInfo["owner_id"];

        $sql = "";
        $sql .= "UPDATE  web_ayarlar SET  config_value = '{$site_adi}'  WHERE owner_id = '{$owner_id}' and  config_name = 'site_adi' and account_no = '{$hesap_no}';";
        $sql .= "UPDATE  web_ayarlar SET   config_value = '{$site_title}' WHERE  owner_id = '{$owner_id}' and   config_name = 'site_title' and  account_no = '{$hesap_no}';";
        $sql .= "UPDATE  web_ayarlar SET    config_value = '{$yetkili_ad}' WHERE  owner_id = '{$owner_id}' and   config_name = 'yetkili_ad' and account_no = '{$hesap_no}';";
        $sql .= "UPDATE  web_ayarlar SET    config_value = '{$site_telefon}'WHERE owner_id = '{$owner_id}' and  config_name = 'site_telefon' and account_no = '{$hesap_no}';";
        $sql .= "UPDATE  web_ayarlar SET    config_value = '{$site_mail}' WHERE  owner_id = '{$owner_id}' and  config_name = 'site_mail' and account_no = '{$hesap_no}';";
        $sql .= "UPDATE  web_ayarlar SET   config_value = '{$site_adres}' WHERE owner_id = '{$owner_id}' and  config_name = 'site_adres' and account_no = '{$hesap_no}';";


        return $this->getConnection()->prepare($sql)->execute();

    }


    public function sayfalarSifirla(){

        $hesap_no = Controller::$account_no;
        $owner_id = Controller::$userInfo["owner_id"];

        $this->getConnection()->prepare("DELETE FROM  web_sayfalar WHERE  account_no = '{$hesap_no}' ")->execute();
        $sql = "";



        $sayfalar =[

            ["page_kod"=>"index" , "page_url"=>"/","page_name"=>"Anasayfa","fix"=>1,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"bakimmod" , "page_url"=>"/bakim-modu","page_name"=>"Bakım Modu Sayfası","fix"=>1,"activate"=>1,"eticaret"=>0],

            //Eticaret
            ["page_kod"=>"index-eticaret" , "page_url"=>"/eticaret","page_name"=>"E-Ticaret Anasayfa","fix"=>1,"activate"=>1,"eticaret"=>1],
            ["page_kod"=>"urunler" , "page_url"=>"urunler","page_name"=>"Ürünler","fix"=>1,"activate"=>1,"eticaret"=>1],
            ["page_kod"=>"urun" , "page_url"=>"urun","page_name"=>"Ürün Sayfası","fix"=>1,"activate"=>1,"eticaret"=>1],
            ["page_kod"=>"kategoriler" , "page_url"=>"kategoriler","page_name"=>"Ürün Kategorileri","fix"=>1,"activate"=>1,"eticaret"=>1],


            /*
             *            ["page_kod"=>"" , "page_url"=>"","page_name"=>"","fix"=>1,"activate"=>1,"eticaret"=>1],
             */

            /*
             * Üye Sistemi
             */

            ["page_kod"=>"uye-oturum-ac" , "page_url"=>"uye/giris","page_name"=>"Üye Oturum Açma Sayfası","fix"=>1,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"uye-kayit" , "page_url"=>"uye/kayit","page_name"=>"Yeni Üye Kayıt Sayfası","fix"=>1,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"uye-hesap" , "page_url"=>"uye/hesap","page_name"=>"Üye Hesabı Sayfası","fix"=>1,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"uye-sifremi-unuttum" , "page_url"=>"uye/sifremi-unuttum","page_name"=>"Üye Şifre Talep Sayfası","fix"=>1,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"uye-sifremi-unuttum-onay" , "page_url"=>"uye/sifremi-unuttum-onay","page_name"=>"Üye Şifre Yenileme Sayfası","fix"=>1,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"uye-sepet" , "page_url"=>"uye/sepet","page_name"=>"Üye Sepet Sayfası","fix"=>1,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"uye-kayit-kapali" , "page_url"=>"uye/kayit-kapali","page_name"=>"Yeni Üye Kayıt Kapalı Mesaj","fix"=>1,"activate"=>1,"eticaret"=>0],



            /*
             * HARİCİ SAYFALAR
             */

            ["page_kod"=>"hakkimizda" , "page_url"=>"hakkimizda","page_name"=>"Hakkımızda Sayfası","fix"=>0,"activate"=>1,"eticaret"=>0],
            ["page_kod"=>"iletisim" , "page_url"=>"iletisim","page_name"=>"İletişim Sayfası","fix"=>0,"activate"=>1,"eticaret"=>0],

        ];





        foreach ($sayfalar as $key => $val){

            $page_kod = $val["page_kod"];
            $page_url = $val["page_url"];
            $page_name = $val["page_name"];

            $fix = $val["fix"];
            $activate = $val["activate"];
            $eticaret = $val["eticaret"];


            $sql .= "INSERT INTO  web_sayfalar SET 
page_kod ='{$page_kod}', 
page_url = '{$page_url}' ,  
page_name = '{$page_name}', 
fix = {$fix} , 
activate = {$activate} , 
eticaret = {$eticaret}, 
owner_id = {$owner_id} ,
account_no = '{$hesap_no}';";


        }




        /*
         * E-Ticaret
         */
/*
    $sql .= "INSERT INTO  web_sayfalar SET page_kod ='markalar', page_url = 'markalar' ,  page_name = 'Markalar' , fix = 1 , activate = 1 , owner_id = {$owner_id} , page_layout = 'layout', template_page ='brand/list' , eticaret = 1, account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_sayfalar SET page_kod ='marka', page_url = 'marka' ,  page_name = 'Marka' , fix = 1 , activate = 1 , owner_id = {$owner_id} , page_layout = 'layout', template_page ='brand/show' , eticaret = 1, account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_sayfalar SET page_kod ='etiketler', page_url = 'etiketler' ,  page_name = 'Etiketler' , fix = 1 , activate = 1 , owner_id = {$owner_id} , page_layout = 'layout',template_page ='tags/list' , eticaret = 1, account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_sayfalar SET page_kod ='etiket', page_url = 'etiket' ,  page_name = 'Etiket' , fix = 1 , activate = 1 , owner_id = {$owner_id} ,  eticaret = 1,  account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_sayfalar SET page_kod ='urun-arama', page_url = 'urun-arama' ,  page_name = 'Ürün Arama Sayfası' , fix = 1 , activate = 1 , owner_id = {$owner_id} ,  eticaret = 1,account_no = '{$hesap_no}';";

*/



        if ($sql != "") {
            return $this->getConnection()->prepare($sql)->execute();
        } else {

            return false;
        }


    }

    public function getConnectorData(){

        $query = $this->getConnection()->prepare("SELECT config_name ,config_value  FROM   web_ayarlar WHERE owner_id = ? and  account_no = ? and config_name IN('connection_key','web_url','template_name') ");

        $query->execute([Controller::$userInfo["owner_id"],Controller::$account_no]);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {

            foreach ($result as $key => $val) {

                $ayarlar[$val["config_name"]] = $val["config_value"];

            }
        }


        return $ayarlar;
    }

    public function webConnector($url , $params){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);



        if($server_output){


            $result_data =  json_decode($server_output,true);

            if($result_data == null){
                return  $server_output;
            }else{

                return  $result_data;
            }


        }else{

            return ["status"=> 0 ,"msg"=>$server_output];
        }

    }

    public function ayarlariSifirla()
    {

        $hesap_no = Controller::$account_no;

        $length = 100;
        $owner_id = Controller::$userInfo["owner_id"];
        $chars = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $clen   = strlen( $chars )-1;
        $key  = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[mt_rand(0,$clen)];
        }

        $key = base64_encode($key);

        $url = "http://localhost/avokado/web/avokadoweb";

        $this->getConnection()->prepare("DELETE FROM  web_ayarlar WHERE  account_no = '{$hesap_no}' ")->execute();

        $mail = Controller::$userInfo["email"];
        $telefon = Controller::$userInfo["phone"];
        $ad = Controller::$userInfo["name"] . " " . Controller::$userInfo["surname"];

        $sql = "";

        $sql .= "INSERT INTO  web_ayarlar SET owner_id = '{$owner_id}',    config_group ='standart' , auto_load = 1 ,  config_name = 'webyayin', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET owner_id = '{$owner_id}',   config_group ='standart' ,  auto_load = 1 , config_name = 'bakimmod', config_value = '0' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',    config_group ='standart' , auto_load = 1 , config_name = 'onbellek', config_value = '0' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',   config_group ='standart' , auto_load = 1 , config_name = 'compress', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',    config_group ='standart' , auto_load = 1 ,config_name = 'eticaret', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' , auto_load = 1 ,  config_name = 'bootblock', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET owner_id = '{$owner_id}',   config_group ='standart' , auto_load = 1 , config_name = 'webmesaj', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',   config_group ='standart' , auto_load = 1 , config_name = 'site_adi', config_value = 'Site Adınız Yazın' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' ,  auto_load = 1 , config_name = 'site_title', config_value = 'Kısa Site Açıklaması' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' ,  auto_load = 1 , config_name = 'yetkili_ad', config_value = '{$ad}' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' ,  auto_load = 1 , config_name = 'site_telefon', config_value = '{$telefon}' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' ,  auto_load = 1 , config_name = 'site_mail', config_value = '{$mail}' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' ,  auto_load = 1 , config_name = 'site_adres', config_value = '' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' ,  auto_load = 1 , config_name = 'template_name', config_value = 'default' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET owner_id = '{$owner_id}',   config_group ='standart' ,  auto_load = 1 , config_name = 'active_template_name', config_value = 'default' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',  config_group ='standart' ,  auto_load = 1 , config_name = 'web_url', config_value = '{$url}' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET owner_id = '{$owner_id}',   config_group ='standart' ,  auto_load = 0 , config_name = 'connection_key', config_value = '{$key}' , account_no = '{$hesap_no}';";


        $sql .= "INSERT INTO  web_ayarlar SET owner_id = '{$owner_id}',   config_group ='standart' , auto_load = 1 , config_name = 'abonelik', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',   config_group ='standart' , auto_load = 1 , config_name = 'yeniuye', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',   config_group ='standart' , auto_load = 1 , config_name = 'uyesifresifirla', config_value = '1' , account_no = '{$hesap_no}';";
        $sql .= "INSERT INTO  web_ayarlar SET  owner_id = '{$owner_id}',   config_group ='standart' , auto_load = 1 , config_name = 'yeniuyeonay', config_value = '1' , account_no = '{$hesap_no}';";



        if ($sql != "") {

            return $this->getConnection()->prepare($sql)->execute();

        } else {

            return false;
        }


    }

    public function ayarlariAl()
    {
        $owner_id = Controller::$userInfo["owner_id"];
        $ayarlar = [];

        $query = $this->getConnection()->prepare("SELECT * FROM   web_ayarlar WHERE owner_id = '{$owner_id}' and   account_no = ? ");

        $query->execute([Controller::$account_no]);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {

            foreach ($result as $key => $val) {

                $ayarlar[$val["config_name"]] = $val["config_value"];

            }
        }


        return $ayarlar;

    }


    public function ayarGuncelle($ayar, $durum)
    {
        $owner_id = Controller::$userInfo["owner_id"];

        $query = $this->getConnection()->prepare("UPDATE  web_ayarlar SET   config_value = ? WHERE    config_name = ? and owner_id = ? and  account_no = ? ");

        return $query->execute([$durum, $ayar, $owner_id,Controller::$account_no]);


    }




    public function yeniSayfaEkle($request)
    {

        $account_no = Controller::$account_no;
        $owner_id = Controller::$userInfo["owner_id"];

        $page_kod = $request->input("sayfa_kodu");
        $page_url = $request->input("sayfa_url");
        $page_name = $request->input("sayfa_adi");


        $sql= "INSERT INTO  web_sayfalar SET
account_no = ? ,
  owner_id = ? , 
 page_kod = ? ,  
 page_url = ?  , 
  page_name = ? , 
  fix = 0 , 
  activate = 0 , 
  page_layout = 'layout',
   template_page ='extra/page' ,  
   eticaret = 0 
    ;";



     $insert =  $this->getConnection()->prepare($sql);

        $insert_result =  $insert->execute([
            $account_no,
            $owner_id,
            $page_kod,
            $page_url,
            $page_name
        ]);



        $webconfig = $this->getConnectorData();


        $page_html_content_query = $this->webConnector($webconfig["web_url"]."/connect" , [
            "operation" =>"get_full_template_file",
            "template_name"=> $webconfig["template_name"],
            "file_name"=> $page_kod,
            "key" => $webconfig["connection_key"]
        ]);



        if($page_html_content_query["status"] == 1){

            return true;

        }else{

            return false;
        }



    }

}
