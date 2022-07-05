<?php

namespace Dipa\Http;

/**
 *
 * @author Doğuş DİCLE
 */
class Header extends \Dipa\Sys\Session{

    public $http_status_code;
  

    public function set_code($code) {

        $this->http_status_code = $code;

        return $this;
    }

    public function result($type, $message) {
        
        self::set("session_message", ['type'=>$type,'message'=>$message]);

        return $this;
    }

    public function to($url="") {

        header('Location: ' . \Dipa\App::getConfig("url") . "/" . $url);
    }

        public function toUrl($url="",$type=null, $message="") {
            
            if($type == null){
                   header('Location: ' . $url);
                   
            }else{
                 header('Location: ' . $url."?restotype=".urldecode(base64_encode($type))."&restomsg=".urldecode(base64_encode($message)));
                
            }

     
    }
    public function back() {

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

}
