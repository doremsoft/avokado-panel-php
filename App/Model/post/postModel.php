<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class StdoutLogger extends \Psr\Log\AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        echo $message . "\n";
    }
}


class postModel extends Dimodel
{
    /*
     * Controller::$userInfo
     */
    /*


   */


    public function postSave($request, $kalan_depolama_limiti = 0)
    {

        $folder_path = STORAGE_PATH . DS . Controller::$account_no . DS . "media" . DS . "private" . DS . "images" . DS . "post" . DS . date("Y-m");

        $image_upload = 0;



        if (isset($_FILES["file"])) {


            if ($kalan_depolama_limiti > 0) {

                /*
                 * $kullanimdaki_depolama = byte
                 */

                if($_FILES["file"]['size'] < $kalan_depolama_limiti) {


                    $thumbs_path = STORAGE_PATH . DS . Controller::$account_no . DS . "thumbs" . DS . "private" . DS . "images" . DS . "post" . DS . date("Y-m");

                    if (!file_exists($folder_path)) {

                        mkdir($folder_path, 0777, true);
                    }

                    if (!file_exists($thumbs_path)) {

                        mkdir($thumbs_path, 0777, true);
                    }

                    $temp = explode(".", $_FILES["file"]["name"]);

                    $fileex = '.' . end($temp);

                    $newfilename = round(microtime(true));

                    $fileex = strtolower($fileex);

                    $location = $folder_path . DS . $newfilename . $fileex;

                    $thumbs_file = $thumbs_path . DS . $newfilename . $fileex;

                    $uploadOk = 1;

                    $imageFileType = pathinfo($location, PATHINFO_EXTENSION);

                    $valid_extensions = array("jpg", "jpeg", "png");

                    if (!in_array(strtolower($imageFileType), $valid_extensions)) {
                        $uploadOk = 0;
                    }

                    if ($uploadOk == 0) {

                    } else {
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
                            $image_upload = 1;

                        }
                    }



                }else{
                    $image_upload = 2;

                }



            } else {

                $image_upload = 2;


            }


        }

        $etiketli_uyeler = false;

        $attach_user_list = [];

        $mesaj = $request->input("postdata");

        preg_match_all("/(@\w+)/", $mesaj, $attach_user_list);


        $id_listesi = "";

        if(isset($attach_user_list[0]) && !empty($attach_user_list[0]) ){

            /*
             * <span style='font-weight: bold;' class='usertag' data-user-tag='$2'>$2</span>
             */

            $id_listesi = "'".implode("','",$attach_user_list[0])."'";

            $id_listesi = str_replace("@","",$id_listesi);

            $sql_= "SELECT id , username  FROM users WHERE username IN({$id_listesi})";

        $search_query = $this->getConnection()->prepare($sql_);

         $search_query->execute();

          $etiketli_uyeler =   $search_query->fetchAll(PDO::FETCH_ASSOC);

           if($etiketli_uyeler){

                foreach ($etiketli_uyeler as $key => $value){

                    $replace_string = "<a class='usertag' href='#' data-rep = '0' data-user-tag-id='".$value['id']."'>@".$value['username']."</a>";

                    $mesaj =  str_replace("@".$value['username'],$replace_string,$mesaj);

                }
           }
        }



        $mesaj = htmlentities($mesaj,ENT_QUOTES);


        $insert_sql = "INSERT INTO post SET tip = ? ,mesaj = ? ,stok_id = ? ,hesap_id = ? ,owner_id = ? ,created_user = ? ,dataid = ?  , created_date = ? , image = ? , original_image = ?  ";

        $tip = $request->input("type");

        $id = $request->input("id");

        $owner_id = Controller::$userInfo["owner_id"];

        $created_user = Controller::$userInfo["id"];

        $created_date = date("Y-m-d H:i:s");


        $stok_id = 0;

        $hesap_id = 0;
        $dataid = 0;
        $bildirim_tipi = 0;


        if ($tip == "hesap") {


            $stok_id = $id;
            $hesap_id = $id;
            $dataid = $id;
            $bildirim_tipi = 3;


        } else if ($tip == "stok") {


            $dataid = $id;
            $stok_id = $id;
            $bildirim_tipi = 2;

        } else if ($tip == "duvar") {

            $bildirim_tipi = 1;


        }

        $image_name = "noimage.jpg";
        $compimage_name = "noimage.jpg";

        if ($image_upload == 1) {

            $image_name = "post/" . date("Y-m") . "/" . $newfilename . $fileex;

            $compimage_name = "post/" . date("Y-m") . "/" . $newfilename . $fileex;


            $this->compress($location, $location, 50);

            try {

                $image = new \claviska\SimpleImage($location);

                $Width = $image->getWidth();

                if ($Width > 1400) {

                    $image->resize(1400, null)->toFile($location);

                }

                $image = null;

            } catch (Exception $err) {

                echo $err->getMessage();
            }


            try {

                $image = new \claviska\SimpleImage($location);

                $image->resize(122, 91)->toFile($thumbs_file);

                $image = null;

            } catch (Exception $err) {

                echo $err->getMessage();
            }


        }


        $insert = $this->getConnection()->prepare($insert_sql)->execute([
            $tip,
            $mesaj,
            $stok_id,
            $hesap_id,
            $owner_id,
            $created_user,
            $dataid,
            $created_date,
            $compimage_name,
            $image_name

        ]);

        $post_id =  $this->getConnection()->lastInsertId();

        if ($insert) {



            $this->bildirimEkle(new Controller(), "Duvar Bildirimi",$mesaj, $bildirim_tipi, date("Y-m-d"), date("H:i:s"), 0,$post_id,"share");

            if($etiketli_uyeler){

                foreach ($etiketli_uyeler as $key => $value){

                    if(Controller::$userInfo["id"] != $value['id']){

                        $this->bildirimEkle(new Controller(),  "Duvar Bildirimi",Controller::$userInfo["username"]." Sizi Paylaşıma Etiketledi...", 5, date("Y-m-d"), date("H:i:s"), $value['id'],$post_id,"share");

                    }

                }
            }



            if ($image_upload == 1) {

                return 1;

            }else  if ($image_upload == 2) {

                return 2;

             }else{

                return 1;

            }

        }else{

            return 0;
        }


    }


    private function compress($source, $destination, $quality)
    {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/jpg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);

        return $destination;
    }


    public function getPostsWithID($request)
    {


        $tip = $request->input("type");
        $id = $request->input("id");
        $islem = $request->input("islem");
        $lastid = $request->input("lastid");
        $firstid = $request->input("firstid");
        $owner_id = Controller::$userInfo["owner_id"];

        $limit = 10;


        if ($islem == "more") {

            if ($tip == "duvar") {

                $sql = "SELECT post.* , users.id as userid ,  users.image as pfimage , (SELECT COUNT(id) as top FROM post_comments WHERE post_id = post.id and remove = 0 ) as commentcount , users.name , users.surname  , cari.cari_adi , cari.id as cari_id , stok.id as stok_id ,stok.stok_adi , stok.stok_varyant_adi , stok.stok_varyant_deger  FROM post 
LEFT JOIN users ON post.created_user = users.id  
LEFT JOIN cari ON post.dataid = cari.id   
LEFT JOIN stok ON post.dataid = stok.id   
WHERE   post.owner_id = ? and users.remove = 0  and post.remove = 0 and post.id < ? ORDER BY post.id DESC LIMIT {$limit} ";
                $query = $this->getConnection()->prepare($sql);
                $query->execute([$owner_id, $firstid]);

            } else {
                $sql = "SELECT post.* ,  users.id as userid ,
(SELECT COUNT(id) as top FROM post_comments WHERE post_id = post.id and remove = 0 ) as commentcount , users.image as pfimage , users.name , users.surname  , cari.cari_adi , cari.id as cari_id , stok.id as stok_id ,stok.stok_adi , stok.stok_varyant_adi , stok.stok_varyant_deger  FROM post 
LEFT JOIN users ON post.created_user = users.id  
LEFT JOIN cari ON post.dataid = cari.id   
LEFT JOIN stok ON post.dataid = stok.id   
WHERE post.tip = ? and post.dataid = ? and post.remove = 0 and post.owner_id = ? and users.remove = 0 and post.id < ? ORDER BY post.id DESC LIMIT {$limit} ";
                $query = $this->getConnection()->prepare($sql);
                $query->execute([$tip, $id, $owner_id, $firstid]);

            }


        } else {

            if ($tip == "duvar") {

                $sql = "SELECT 
post.* , users.name , users.surname  , users.id as userid ,users.image as pfimage ,  cari.cari_adi , cari.id as cari_id , stok.id as stok_id , stok.stok_adi , 
stok.stok_varyant_adi , stok.stok_varyant_deger  ,
(SELECT COUNT(id) as top FROM post_comments WHERE post_id = post.id and remove = 0 ) as commentcount 
 FROM post 
LEFT JOIN users ON post.created_user = users.id  
LEFT JOIN cari ON post.dataid = cari.id   
LEFT JOIN stok ON post.dataid = stok.id   
WHERE   post.owner_id = ? and post.remove = 0 and users.remove = 0 and post.id > ? ORDER BY post.id DESC LIMIT {$limit} ";
                $query = $this->getConnection()->prepare($sql);
                $query->execute([$owner_id, $lastid]);

            } else {
                $sql = "
SELECT 
post.* , 
users.name , 
 users.id as userid ,
users.surname  , 
users.image as pfimage , 
cari.cari_adi , 
cari.id as cari_id , 
stok.id as stok_id ,
stok.stok_adi , 
stok.stok_varyant_adi , 
stok.stok_varyant_deger ,
(SELECT COUNT(id) as top FROM post_comments WHERE post_id = post.id and remove = 0 ) as commentcount 
FROM post 

LEFT JOIN users ON post.created_user = users.id  
LEFT JOIN cari ON post.dataid = cari.id   
LEFT JOIN stok ON post.dataid = stok.id   
WHERE post.tip = ? and post.dataid = ? and post.remove = 0 and post.owner_id = ? and users.remove = 0 and post.id > ? ORDER BY post.id DESC LIMIT {$limit} ";
                $query = $this->getConnection()->prepare($sql);
                $query->execute([$tip, $id, $owner_id, $lastid]);

            }
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getPost($id)
    {

        $owner_id = Controller::$userInfo["owner_id"];
        $sql = "
SELECT 
post.* , 
users.name , 
 users.id as userid ,
users.surname  , 
users.image as pfimage , 
cari.cari_adi , 
cari.id as cari_id , 
stok.id as stok_id ,
stok.stok_adi , 
stok.stok_varyant_adi , 
stok.stok_varyant_deger ,
(SELECT COUNT(id) as top FROM post_comments WHERE post_id = post.id and remove = 0 ) as commentcount 
FROM post 

LEFT JOIN users ON post.created_user = users.id  
LEFT JOIN cari ON post.dataid = cari.id   
LEFT JOIN stok ON post.dataid = stok.id   
WHERE post.id = ? and post.remove = 0 and post.owner_id = ? and users.remove = 0  ";
        $query = $this->getConnection()->prepare($sql);
        $query->execute([$id, $owner_id]);

        return $query->fetch();
    }


    public function commentStatus($id){

        $owner_id = Controller::$userInfo["owner_id"];
        $user_id = Controller::$userInfo["id"];
        $sql = "
SELECT 
post.*  
FROM post 
WHERE post.id = ? and post.owner_id = ? and created_user = ?   ";
        $query = $this->getConnection()->prepare($sql);
        $query->execute([$id, $owner_id,$user_id]);

        $post_data =  $query->fetch();

        if($post_data){

            $status = $post_data["yorum_durum"];

            if($status == 1){

                return $this->getConnection()->prepare("UPDATE post SET yorum_durum = 0 WHERE id = ? ")->execute([$id]);

            }else{

                return $this->getConnection()->prepare("UPDATE post SET yorum_durum = 1 WHERE id = ? ")->execute([$id]);

            }

        }else{

            return false;
        }


    }

    public function remove($id){

        $owner_id = Controller::$userInfo["owner_id"];
        $user_id = Controller::$userInfo["id"];
        $sql = "
SELECT 
post.*  
FROM post 
WHERE post.id = ? and post.owner_id = ? and created_user = ?   ";
        $query = $this->getConnection()->prepare($sql);
        $query->execute([$id, $owner_id,$user_id]);

        $post_data =  $query->fetch();

        if($post_data){



                return $this->getConnection()->prepare("UPDATE post SET remove = 1 WHERE id = ? ")->execute([$id]);



        }else{

            return false;
        }


    }

}
