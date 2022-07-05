<?php

namespace Dipa\Io;

class Log {

    public static function get_ip_adress() {

        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

    public function account_read($account, $day) {

        $folder = LOG_PATH . DS . "avoklog" . DS . $account . DS . date("Y").DS.date("m");

        $file_name = $folder . DS . date("Y-m-d")  . ".php";

        if(file_exists($file_name)){

            include $file_name;

            if(isset($log)){

                if(is_array($log)){
                    
                    $log_data = "";
                    
                    foreach ($log as $value) {
                        
                        
                      $log_data.="<tr>";  
                          $log_data.="<td>";  
                          
                           $log_data.="</td>";  
                        
                        $log_data.="</tr>";  
                    }
                    
                    
                    return $log_data;
                }else{
                    
                          return "Log Kaydı Bulunamadı!";
                }
                
            }else{
                
                return "Log Kaydı Bulunamadı!";
            }
            
        }else{
            
            
          return "Log Kaydı Bulunamadı!";
        }
        
    }

    public static function view_write($msg = "", $account = 0, $user = 0, $platform = "panel") {
        
    }

    public static function write($msg = "", $account = "0", $user = 0, $platform = "panel") {


        $folder_prefx = "";


        if ($platform == "mobile") {

            $folder_prefx = "-m";
        } else if ($platform == "software") {

            $folder_prefx = "-s";
        }

        if ($account == "0" || $account == NULL) {

            $folder = LOG_PATH . DS . "avoklog" . DS . "def" . DS .  date("Y").DS.date("m");
        } else {

            $folder = LOG_PATH . DS . $account . DS."media".DS."sistem".DS."log". DS .  date("Y").DS.date("m");
        }

        if (!is_dir($folder)) {

            mkdir($folder, 0750, true);
        }


        $file_name = $folder . DS . date("Y-m-d") . $folder_prefx . ".log";


        $time = date("Y-m-d H:i:s");

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $usera = "";
        if(isset($_SERVER['HTTP_USER_AGENT'])){

            $usera = $_SERVER['HTTP_USER_AGENT'];
        }


        $info = [
            'method' => $_SERVER['REQUEST_METHOD'],
            'ip' => self::get_ip_adress(),
            'usera' => $usera
        ];
        $log_text = [
            'u' => $user,
            't' => $time,
            'a' => $account,
            'm' => $msg,
            'l' => $actual_link,
            'i' => $info
        ];

        if (!file_exists($file_name)) {

            touch($file_name);
            $dosya = fopen($file_name, 'a');


            fwrite($dosya, json_encode($log_text)."\n");

            fclose($dosya);
        } else {
            $dosya = fopen($file_name, 'a');

            fwrite($dosya, json_encode($log_text)."\n");

            fclose($dosya);
        }
    }

}
