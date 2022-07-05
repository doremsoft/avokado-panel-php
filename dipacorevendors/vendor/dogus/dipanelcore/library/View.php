<?php

namespace Dipa;

use \Dipa\App;
use \Dipa\Sys\Session;
use Melbahja\Http2\Pusher;

/**
 *
 * @author Doğuş DİCLE
 * 
 */
class View {

    public static $account_no;
    public static $doviz_listesi = NULL;
    public static $http2;
    public static  $asset_size = 0;

    public static function render($view, $params = [], $templateName = null, $noCache = false, $token = "", $user = NULL, $account_no = 0, $hesap_detaylari = NULL) {

       // self::$http2 = Pusher::getInstance();


        self::$account_no = $account_no;


        if ($noCache) {

            $params['noCache'] = "?r=" . uniqid();
        } else {

            $params['noCache'] = "";
        }
        
        
        
        
        if(isset($_POST["noframe"]) ||isset($_GET["noframe"])){
            
            $params['noframe'] = "ok";
        }else{
            
              $params['noframe'] = "no";
        }


        if (!isset($params['title'])) {
            $params['title'] = App::getConfig("title");
        }
        $params['url'] = App::getConfig("url");
        $params['media_url'] = App::getConfig("mediaUrl");
        $params['login_attack_url'] = App::getConfig("login_attack_url");
        $params['asset_url']= App::getConfig("assetsUrl") . "/" . App::getConfig("activeTemplate")  ;


        $params['sonbildirimler']  = [];




        if (App::getConfig("form", "csrfToken")) {

            if (!isset($_SESSION["csrftoken"])) {

                $token = \Dipa\Http\Request::set_csrf();
            } else {
                $token = $_SESSION["csrftoken"];
            }
        }

        if (Session::has("session_message")) {

            $params['session']['session_message'] = Session::get("session_message");

            Session::reset("session_message");
        }

        $params['csrf_token'] = $token;
        $params['csrf'] = "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\" />";
        $params['user'] = $user;
        $params['hesap_detay'] = $hesap_detaylari;
        $params['darktheme'] = 0;
        $params['mobile'] = 0;

        if(isset($user["h_nav"])){
            $params['h_nav'] = $user["h_nav"];
        }


        if(isset($hesap_detaylari["media_key"])){

            $params['media_secure'] = "akey=".md5($hesap_detaylari["media_key"])."&hesap_no=".$account_no;

        }


        $params['socket_url'] = App::getConfig("manager_url");




        if (isset($_SESSION["extra_config"]["mobile"])) {

            if($_SESSION["extra_config"]["mobile"] == true){

                $params['darktheme']  = 1;
                $params['h_nav'] = 0;
                $params['mobile'] = 1;
            }
        }


        $templateName = $templateName == null ? App::getConfig("activeTemplate") : $templateName;




        if($templateName == "fuse"){

            $model = Controller::include_model("bildirim", "bildirimModel");

            $params['sonbildirimler'] = $model->sonOkunanBildirimler();


        }





        $twig_folder = APP_DIR . DS . 'View' . DS . $templateName;

        $loader = new \Twig\Loader\FilesystemLoader($twig_folder);
        
        $twig = new \Twig\Environment($loader);

        /*
        $twig = new \Twig\Environment($loader, [
            'cache' => CACHE_DIR,
        ]);
        */


        $urlFilter = new \Twig\TwigFilter('url', function ($url) {
            return App::getConfig("url") . $url;
        });

        $routeFilter = new \Twig\TwigFilter('route', function ($route) {
            return App::getConfig("url") . "/" . app('route')->getRoute($route, []);
        });


        $html_filter = new \Twig\TwigFunction('get_html', function ($html_string) {
            return new \Twig\Markup(html_entity_decode($html_string, ENT_QUOTES), "utf-8");
        });

        $mediaFunction = new \Twig\TwigFunction('media', function ($mediaName) {

            if ((!(substr($mediaName, 0, 7) == 'http://')) && (!(substr($mediaName, 0, 8) == 'https://'))) {

                $http_media_name =  App::getConfig("url") ."/storage-noi/". $mediaName;


               // self::$http2->img($http_media_name);
                if(self::$asset_size == 0){
                    self::$asset_size++;
                }
                return $http_media_name;

            }else{
                return  $mediaName;
            }

        });


        $public_mediaFunction = new \Twig\TwigFunction('public_media', function ($mediaName) {
            if ((!(substr($mediaName, 0, 7) == 'http://')) && (!(substr($mediaName, 0, 8) == 'https://'))) {

                $http_media_name =  App::getConfig("mediaUrl") ."/".self::$account_no."/s/". $mediaName;

              //  self::$http2->img($http_media_name);
                if(self::$asset_size == 0){
                    self::$asset_size++;
                }
                return $http_media_name;


            }else{
                return  $mediaName;
            }

        });


        $get_config_function = new \Twig\TwigFunction('get_config', function ($configKey, $val = NULL) {
            return App::getConfig($configKey, $val);
        });



        $asset_url_function = new \Twig\TwigFunction('asset', function ($asset, $defaultcacheid = True, $noncache = False, $type = NULL) {






            $assetUrl = App::getConfig("assetsUrl") . "/" . App::getConfig("activeTemplate") . "/" . $asset;

            if ($noncache) {

                $assetUrl = $assetUrl . "?v=" . time();
            } else {

                if ($defaultcacheid) {

                    $assetUrl = $assetUrl . "?v=" . App::getConfig("mediacacheid");
                }
            }


            if(self::$asset_size == 0){
                self::$asset_size++;
            }



           // self::$http2->link($assetUrl);



            return $assetUrl;
        });



        $kdv_hesapla_function = new \Twig\TwigFunction('kdv_hesapla', function ($birim_fiyat, $adet, $kdv_oran, $islem, $format = null , $decimal = 4) {

            if ($kdv_oran > 0) {


                if ($kdv_oran > 9) {

                    $kdv_duzelt = "1." . $kdv_oran;
                } else {

                    $kdv_duzelt = "1.0" . $kdv_oran;
                }

                if ($islem == "ekle") {

                    $fiyat = $birim_fiyat * $kdv_duzelt;
                } else if ($islem == "cikart") {

                    $fiyat = $birim_fiyat / $kdv_duzelt;
                } else {

                    $fiyat = $birim_fiyat;
                }
            } else {
                $fiyat = $birim_fiyat;
            }






            if ($format == null) {
                return   number_format($fiyat * $adet, $decimal, ',', '.');
            } else if ($format == "duz") {
                return  number_format($fiyat * $adet, $decimal, '.', '');
            } else {


                return   $fiyat * $adet;
            }
        });


        $doviz_function = new \Twig\TwigFunction('doviz', function ($doviz) {
            if ($doviz == "TL") {
                return "₺";
            } else if ($doviz == "USD") {
                return "USD";
            } else if ($doviz == "EUR") {
                return "EURO";
            }
        });


        $tarih_function = new \Twig\TwigFunction('tarih', function ($date , $date_format = "d-m-Y") {

            return date($date_format , strtotime($date));

        });




        $dovizcevir_function = new \Twig\TwigFunction('exchange', function ($fiyat, $doviz) {


            if (self::$doviz_listesi == NULL) {

                $model = Controller::include_model("doviz", "dovizModel");

                $dovizler = $model->dovizleriAl();

                if ($dovizler) {

                    if (is_array($dovizler)) {

                        foreach ($dovizler as $key => $value) {

                            self::$doviz_listesi[$value["doviz_kod"]] = [
                                'ad' => $value["doviz_adi"],
                                'kod'=>$value["doviz_kod"],
                                'kur' => $value["doviz_kur"],
                                'column' => $value
                            ];
                        }
                    }
                }
            }


            $kur = self::$doviz_listesi[$doviz]['kur'];

            return [
                'kur' => $kur,
                'tutar'=> $fiyat * $kur
            ];
        });



        $fiyat_function = new \Twig\TwigFunction('Fiyat', function ($fiyat, $doviz, $uzanti = NULL  ,$decimal  =2) {


            if (self::$doviz_listesi == NULL) {

                $model = Controller::include_model("doviz", "dovizModel");

                $dovizler = $model->dovizleriAl();

                if ($dovizler) {

                    if (is_array($dovizler)) {

                        foreach ($dovizler as $key => $value) {

                            self::$doviz_listesi[$value["doviz_kod"]] = [
                                'ad' => $value["doviz_adi"],
                                'kod'=>$value["doviz_kod"],
                                'kur' => $value["doviz_kur"],
                                'column' => $value
                            ];
                        }
                    }
                }
            }


            $result = "";

            if ($doviz == "TL") {

                $result .= number_format($fiyat, $decimal, ',', '.');
            } else {
                $result .= number_format($fiyat, $decimal, '.', '');
            }


            if ($uzanti != NULL) {

                if (isset(self::$doviz_listesi[$doviz])) {


                    if(self::$doviz_listesi[$doviz]["kod"] == "TL"){

                        $result.="<label style='font-weight: bold;'>₺</label>";
                    }else  if(self::$doviz_listesi[$doviz]["kod"] == "USD"){

                        $result.="<label style='font-weight: bold;'>$</label>";
                    }else  if(self::$doviz_listesi[$doviz]["kod"] == "EUR"){

                        $result.="<label style='font-weight: bold;'>€</label>";
                    }else{

                        $result.=" " . self::$doviz_listesi[$doviz]["kod"];

                    }
                    
                   
                }
            }


            return $result;
        });



        $tl_funcition = new \Twig\TwigFunction('Tl', function ($fiyat , $decimals = 4 , $doviz = "TL") {

            if ($doviz == "TL") {

           return  number_format($fiyat, $decimals, ',', '.');

            } else {

                return  number_format($fiyat, $decimals, '.', '');
            }

        });


        $adet_function = new \Twig\TwigFunction('adet', function ($adet) {


            return  (float) $adet;
        });


        $pozitif_function = new \Twig\TwigFunction('pozitif', function ($fiyat, $tl = null) {

            if ($tl == null) {
                return abs($fiyat);
            } else {

                return  number_format(abs($fiyat), 4, ',', '.');
            }
        });


        $paginate_func = new \Twig\TwigFunction('paginate', function ($paginate_data, $url = null, $external_data = null, $option = false) {

            $paginate = "";
            $page_count = 0;

            if ($paginate_data != NULL) {

                $ex_url = "csrftoken={$_SESSION["csrftoken"]}&";

                if ($external_data != NULL) {

                    if (is_array($external_data)) {

                        foreach ($external_data as $key => $value) {

                            $ex_url .= $key . "=" . $value . "&";
                        }
                    }
                }

                $ex_url = rtrim($ex_url, "&");

                if ($url == null) {
                    $domain = App::getConfig("url") . "/";
                } else {
                    $domain = App::getConfig("url") . "/" . $url . "/";
                }

                $lt = 15;
                $ii=0 ;


                if($option == true) {


                    for ($i = 0; $i < $paginate_data["total_page"]; $i++) {

                        $page_count++;

                        $iyaz = $i + 1;

                            if ($iyaz == $paginate_data["now_page"]) {

                                $activate = " selected";
                            } else {
                                $activate = "";
                            }

                            $paginate .= "<option class=\"page-link\" value=\"{$domain}?page={$iyaz}&{$ex_url}\" {$activate}>{$iyaz}.Sayfa</option>";
                    }

                }else{




                    $listeleme_sayisi = 10;

                    $baslama = $paginate_data["now_page"] - ($listeleme_sayisi / 2);

                    $bitis =  $paginate_data["now_page"] + ($listeleme_sayisi / 2);

                    if($baslama < 0){

                        $baslama = 0;

                        $bitis = $paginate_data["now_page"] + ($listeleme_sayisi - 1);

                    }

                    if($bitis > $paginate_data["total_page"] ){

                        $bitis = $paginate_data["total_page"];
                    }



                    for (; $baslama < $bitis; $baslama++) {


                        $page_count++;
                        $iyaz = $baslama + 1;



                        if ($iyaz == $paginate_data["now_page"]) {

                            $activate = " active";
                        } else {
                            $activate = "";
                        }

                        $paginate .= "<li class=\"page-item{$activate}\"><a class=\"page-link\" href=\"{$domain}?page={$iyaz}&{$ex_url}\">{$iyaz}</a></li>";
                    }





                    if($paginate_data["now_page"] - 1 > 0 ){

                        $geri = $paginate_data["now_page"]  -1 ;

                        $paginate =   '    <li class="page-item">
      <a class="page-link" href="'.$domain.'?page='.$geri.'&'.$ex_url.'" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
        <span class="sr-only">Previous</span>
      </a>
    </li>'.$paginate;

                    }




                    if($paginate_data["now_page"] + 1 < $paginate_data["total_page"]){

                        $ileri = $paginate_data["now_page"]  +1 ;
                        $paginate .= ' <a class="page-link" href="'.$domain.'?page='.$ileri.'&'.$ex_url.'" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                  </a>';

                    }

                }

            }










            if($page_count <= 1){
                $paginate = "";
            }


            return new \Twig\Markup($paginate, "utf-8");

        });

        /*
               $popup_url_function = new \Twig\TwigFunction('popup_url', function ($field = "" , $just_url = false) {

            if (isset($_SESSION["user_media_code"])) {
                $user_code = $_SESSION["user_media_code"];
            } else {
                $user_code = "non";
            }

            if($just_url){
                       return App::getConfig("fileMenagerUrl") . "/dialog.php?akey=" . App::getConfig("mediakey") . $user_code."&relative_url=0";
       
            }else{
                       return App::getConfig("fileMenagerUrl") . "/dialog.php?type=2&popup=1&akey=" . App::getConfig("mediakey") . $user_code . "&relative_url=1&field_id=" . $field;
       
            }
      });




        $image_select_function = new \Twig\TwigFunction('image_select', function ($field = "" , $input_name = null, $value = "" , $relative_url = 1 ,$button_text = "Resim Seç" , $input_class = "form-control", $btn_class = "btn") {

            if (isset($_SESSION["user_media_code"])) {
                $user_code = $_SESSION["user_media_code"];
            } else {
                $user_code = "non";
            }

          $url =  App::getConfig("fileMenagerUrl") . "/dialog.php?type=2&popup=1&akey=" . App::getConfig("mediakey") . $user_code . "&relative_url=".$relative_url."&field_id=" . $field;
        
            
          if($input_name == NULL){
              $input_name = $field;
          }
          
          return " <input id=\"".$field."\" type=\"text\" name=\"".$input_name."\"  class=\"".$input_class."\" value=\"".$value."\"> "
                  . "<a href=\"javascript:open_popup('".$url."')\" class=\"".$btn_class."\" type=\"button\">".$button_text."</a>";
            
            
            
            });

        
        
            
            */


        $twig->addFunction($dovizcevir_function);
        $twig->addFunction($public_mediaFunction);
        $twig->addFunction($html_filter);
        $twig->addFunction($pozitif_function);
        $twig->addFunction($tarih_function);
        $twig->addFunction($fiyat_function);
        $twig->addFunction($tl_funcition);
        $twig->addFunction($adet_function);
        $twig->addFunction($kdv_hesapla_function);
        $twig->addFunction($paginate_func);
        $twig->addFunction($get_config_function);
        $twig->addFunction($asset_url_function);
        $twig->addFunction($mediaFunction);
        $twig->addFilter($routeFilter);
        $twig->addFilter($urlFilter);



        $result_html = $twig->load($view . ".twig")->render($params);

        if( self::$asset_size > 0){

           // self::$http2->push();


        }

        echo $result_html;

        $result_html = "";
    }

}
