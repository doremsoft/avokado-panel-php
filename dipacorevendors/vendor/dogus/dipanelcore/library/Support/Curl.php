<?php
namespace Dipa\Support;
use Dipa\Controller;

/**
 *
 * @author Dogus
 */
class Curl
{

    public static function post($url , $post_params = [] ,  $account_no = "" , $secure_key = "",  $add_header = true , $extra_header = []){

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);

        if($add_header == true){


            $header_data = [];

            $header_params = [];

            if($add_header == true){

                $header_data = [
                    "Account-No: {$account_no}",
                    "Account-Hash: {$secure_key}"
                ];

            }

            if(!empty($extra_header)){

                $header_params = array_merge($header_data, $extra_header);

            }else{

                $header_params = $header_data;
            }

            if(!empty($header_params)){

                curl_setopt($ch, CURLOPT_HTTPHEADER, $header_params);
            }

        }


        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }


}
