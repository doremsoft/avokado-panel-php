<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class postCommentModel extends Dimodel
{

    public function getComments($request, $post_id)
    {

        $owner_id = Controller::$userInfo["owner_id"];

        $start_id = $request->input("lastcomment");

        $type = $request->input("type");

        if ($type == "news") {


            $sql = "SELECT 
post_comments.* , users.name , users.surname ,users.image as pfimage  
 FROM post_comments 
LEFT JOIN users ON post_comments.created_user = users.id  
WHERE  post_comments.owner_id = ? and post_comments.post_id = ? and post_comments.id > ?    ORDER BY post_comments.id DESC LIMIT 10 ";

            $query = $this->getConnection()->prepare($sql);

            $query->execute([$owner_id, $post_id, $request->input("bigid")]);

            return $query->fetchAll(PDO::FETCH_ASSOC);


        } else {


            if ($start_id == 0) {

                $sql = "SELECT 
post_comments.* , users.name , users.surname ,users.image as pfimage  
 FROM post_comments 
LEFT JOIN users ON post_comments.created_user = users.id  
WHERE  post_comments.owner_id = ? and post_comments.post_id = ? and post_comments.id > ?  ORDER BY post_comments.id DESC LIMIT 10 ";

                $query = $this->getConnection()->prepare($sql);

                $query->execute([$owner_id, $post_id, $start_id]);


                return $query->fetchAll(PDO::FETCH_ASSOC);


            } else if ($start_id > 0) {
                $sql = "SELECT 
post_comments.* , users.name , users.surname ,users.image as pfimage  
 FROM post_comments 
LEFT JOIN users ON post_comments.created_user = users.id  
WHERE  post_comments.owner_id = ? and post_comments.post_id = ? and post_comments.id < ?  ORDER BY post_comments.id DESC LIMIT 10 ";

                $query = $this->getConnection()->prepare($sql);

                $query->execute([$owner_id, $post_id, $start_id]);


                return $query->fetchAll(PDO::FETCH_ASSOC);


            }


        }


    }

    public function getCommentCount($post_id)
    {

        $owner_id = Controller::$userInfo["owner_id"];

        $sql = "SELECT 
count(id) as commentcount ,
MAX(id) as last_id  
 FROM post_comments 
WHERE   post_comments.owner_id = ? and post_comments.post_id = ?  ";

        $query = $this->getConnection()->prepare($sql);

        $query->execute([$owner_id, $post_id]);

        $result = $query->fetch(PDO::FETCH_ASSOC);
        if ($result) {


            return [
                "commentcount" => $result["commentcount"],
                "last_id" => $result["last_id"]
            ];
        } else {
            return 0;
        }


    }

    public function commentAppend($request)
    {


        $comment = $request->input("comment");
        $postid = $request->input("postid");
        $commentid = $request->input("commentid");
        $owner_id = Controller::$userInfo["owner_id"];
        $created_user = Controller::$userInfo["id"];
        $created_date = date("Y-m-d H:i:s");

        $etiketli_uyeler = [];
        $attach_user_list = [];
        preg_match_all("/(@\w+)/", $comment, $attach_user_list);


        $id_listesi = "";

        if (isset($attach_user_list[0]) && !empty($attach_user_list[0])) {

            $id_listesi = "'" . implode("','", $attach_user_list[0]) . "'";

            $id_listesi = str_replace("@", "", $id_listesi);

            $sql_ = "SELECT id , username  FROM users WHERE username IN({$id_listesi})";

            $search_query = $this->getConnection()->prepare($sql_);

            $search_query->execute();

            $etiketli_uyeler = $search_query->fetchAll(PDO::FETCH_ASSOC);

            if ($etiketli_uyeler) {

                foreach ($etiketli_uyeler as $key => $value) {

                    $replace_string = "<a class='usertag' href='#' data-rep = '0' data-user-tag-id='" . $value['id'] . "'>@" . $value['username'] . "</a>";

                    $comment = str_replace("@" . $value['username'], $replace_string, $comment);

                }
            }
        }

        $comment = htmlentities($comment, ENT_QUOTES);


        $comment_save = 0;


        $owner_id = Controller::$userInfo["owner_id"];
        $sql = "
SELECT 
yorum_durum , remove 
FROM post 
WHERE post.id = ? and post.owner_id = ?    ";


        $query22 = $this->getConnection()->prepare($sql);
        $query22->execute([$postid, $owner_id]);

        $post_data = $query22->fetch();

        if ($post_data) {


            if ($post_data["yorum_durum"] == 1 && $post_data["remove"] == 0) {

                $comment_save = 1;

            }

        }


        if ($comment_save == 1) {

            $insert_sql = "INSERT INTO post_comments 
    SET 
    post_id = ? ,
    comment = ? ,
    owner_id = ? ,
    created_user = ? ,
    created_date = ? ,
    comment_id = ? 
     ";

            $insert = $this->getConnection()->prepare($insert_sql)->execute([
                $postid,
                $comment,
                $owner_id,
                $created_user,
                $created_date,
                $commentid

            ]);


            if ($insert) {

                $paylasim_sahibi_id = 0;

                $sql_ = "SELECT created_user FROM post WHERE id = ? ";

                $search_query = $this->getConnection()->prepare($sql_);

                $search_query->execute([$postid]);

                $paylasim_sahibi = $search_query->fetch();


                if ($paylasim_sahibi) {

                    $paylasim_sahibi_id = $paylasim_sahibi["created_user"];
                }


                if ($paylasim_sahibi_id != Controller::$userInfo["id"]) {

                    $this->bildirimEkle(new Controller(), "Yorum Bildirimi",Controller::$userInfo["username"] . " Paylaşımınıza Yorum Yazdı", 7, date("Y-m-d"), date("H:i:s"), $paylasim_sahibi_id, $postid,"comment");


                }


                if ($etiketli_uyeler) {

                    foreach ($etiketli_uyeler as $key => $value) {

                        if (Controller::$userInfo["id"] != $value['id']) {

                            $this->bildirimEkle(new Controller(), "Yorum Bildirimi",Controller::$userInfo["username"] . " Bir Yorumda Sizden Bahsetti...", 6, date("Y-m-d"), date("H:i:s"), $value['id'], $postid,"comment");

                        }

                    }
                }
            }


            return $insert;
        } else {
            return false;
        }

    }


}
