<?php
namespace App\Controller\Image;

class ImageController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(false);
    }



    private function imageNotFound(){

        header('Pragma: public');
        header('Cache-Control: max-age=86400');
        header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        header("Content-type: image/png");
        $data = "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";
        echo base64_decode($data);


    }


    public function getStorage($image_name_array , $noimage = false , $private = true){

        $folder = "public";

        if($private == true){

            $this->auth(true,true);

            $folder = "private";
        }

        $image_name = $image_name_array[0];

        $path =  STORAGE_PATH.DS.self::$account_no.DS."media".DS.$folder.DS.$image_name;

        if(!file_exists($path)){
            if($noimage == true){

                $this->imageNotFound();
                die();
            }
        }

        $size=filesize($path);

        $fm=@fopen($path,'rb');
        if(!$fm) {



                header ("HTTP/1.0 404 Not Found");
                die();



        }

        $begin=0;
        $end=$size;

        if(isset($_SERVER['HTTP_RANGE'])) {
            if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin=intval($matches[0]);
                if(!empty($matches[1])) {
                    $end=intval($matches[1]);
                }
            }
        }

        if($begin>0||$end<$size)
            header('HTTP/1.0 206 Partial Content');
        else
            header('HTTP/1.0 200 OK');

        $typ = mime_content_type($path);

        header("Content-Type: ".$typ);
        header('Accept-Ranges: bytes');
        header('Content-Length:'.($end-$begin));
        header("Content-Disposition: inline;");
        header("Content-Range: bytes $begin-$end/$size");
        header('Pragma: public');
        header('Cache-Control: max-age=86400');
        header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        header("Content-Transfer-Encoding: binary\n");
        header('Connection: close');

        $cur=$begin;
        fseek($fm,$begin,0);

        while(!feof($fm)&&$cur<$end&&(connection_status()==0))
        { print fread($fm,min(1024*16,$end-$cur));
            $cur+=1024*16;
            usleep(1000);
        }
        die();



    }


    public function getStorageThumb($image_name_array , $noimage = false , $private = true){


        $folder = "public";

        if($private == true){

            $this->auth(true,true);

            $folder = "private";
        }

        $image_name = $image_name_array[0];

        $path =  STORAGE_PATH.DS.self::$account_no.DS."thumbs".DS.$folder.DS.$image_name;



        if(!file_exists($path)){
            if($noimage == true){

                $this->imageNotFound();
                die();
            }

        }

        $size=filesize($path);

        $fm=@fopen($path,'rb');
        if(!$fm) {



                header ("HTTP/1.0 404 Not Found");
                die();



        }

        $begin=0;
        $end=$size;

        if(isset($_SERVER['HTTP_RANGE'])) {
            if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin=intval($matches[0]);
                if(!empty($matches[1])) {
                    $end=intval($matches[1]);
                }
            }
        }

        if($begin>0||$end<$size)
            header('HTTP/1.0 206 Partial Content');
        else
            header('HTTP/1.0 200 OK');

        $typ = mime_content_type($path);

        header("Content-Type: ".$typ);
        header('Accept-Ranges: bytes');
        header('Content-Length:'.($end-$begin));
        header("Content-Disposition: inline;");
        header("Content-Range: bytes $begin-$end/$size");
        header("Content-Transfer-Encoding: binary\n");
        header('Connection: close');

        $cur=$begin;
        fseek($fm,$begin,0);

        while(!feof($fm)&&$cur<$end&&(connection_status()==0))
        { print fread($fm,min(1024*16,$end-$cur));
            $cur+=1024*16;
            usleep(1000);
        }
        die();
    }










}