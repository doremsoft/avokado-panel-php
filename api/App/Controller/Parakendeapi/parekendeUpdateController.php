<?php

namespace App\Controller\Parakendeapi;

class parekendeUpdateController extends \Dipa\Controller {
    private $api;

    public function updateControl() {

        $old_versiyon =(double) self::$http_request->input("ver");

        $type = self::$http_request->input("type");

        $result = [
            'status' => 0,
            'file_name' => "",
            'url_protokol' => "",
            'file_url' => "",
        ];

        if (file_exists(BASE_PATH . "ver.php")) {

            $versiyon_data = include BASE_PATH . "ver.php";

            if ((double) $versiyon_data[$type]["versiyon"] > $old_versiyon) {

                $result = $versiyon_data[$type];

                $result["oldver"] = $old_versiyon;

                echo json_encode($result);
                
            } else {

                $result["oldver"] = $old_versiyon;

                echo json_encode($result);
            }

        }else{
            echo json_encode(["status"=> 3]);


        }
    }


}
