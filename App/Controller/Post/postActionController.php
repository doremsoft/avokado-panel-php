<?php
namespace App\Controller\Post;

use Dipa\Controller;
use Dipa\Http\Header;

class postActionController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }


    public function sendPost() {


        $postModel = $this->model("post", "postModel");

        $save = $postModel->postSave($this->request,$this->getEmptyStorageSize());


            if($save == 1){

                echo "ok";
            }else  if($save == 2){

                echo "ok-noimage";
            }else  if($save == 0){

            echo "non";
        }

    }

    public function getPostsWithID() {


        $postModel = $this->model("post", "postModel");

        $data = $postModel->getPostsWithID($this->request);

        if($data){

            foreach ($data as $key => $val){

                $data[$key]["mesaj"] = html_entity_decode($val["mesaj"],ENT_QUOTES);

            }

            echo json_encode([
              'posts'=>$data,
               'status'=>1

           ]);
        }else{


            echo json_encode([

                'status'=>0

            ]);
        }

    }

    public function commentsLoad($post_id){


        $postModel = $this->model("post", "postCommentModel");

        $data = $postModel->getComments($this->request,$post_id);

        $commentcount = $postModel->getCommentCount($post_id);



        if($data){

            foreach ($data as $key => $val){

                $data[$key]["comment"] = html_entity_decode($val["comment"],ENT_QUOTES);

            }

            echo json_encode([
                'comments'=>$data,
                'status'=>1,
                'commentcount'=>$commentcount["commentcount"],
                "lastid" => $commentcount["last_id"],
                "startid"=>$this->request->input("lastcomment"),
                "more"=>1

            ]);
        }else{


            echo json_encode([

                'status'=>0,
                "startid"=>$this->request->input("lastcomment"),
                "more"=> 0

            ]);
        }

    }

    public function commentAppend(){
        $postModel = $this->model("post", "postCommentModel");

        $save = $postModel->commentAppend($this->request);

        if($save){
            echo "ok";
        }else{

            echo "non";
        }


    }


    public function show($post_id){



        $postModel = $this->model("post", "postModel");
        $data = $postModel->getPost($post_id);

        $paylasim_durum = 0;
        $sahibi = 0;


        if($data){
            $paylasim_durum = 1;


            if($data["created_user"] == Controller::$userInfo["id"]){

                $sahibi = 1;
            }

            $data["mesaj"] = html_entity_decode($data["mesaj"],ENT_QUOTES);
        }








        return $this->view("post/show",[
            'post'=>$data,
            'paylasim_durum'=>$paylasim_durum,
            'sahibi'=>$sahibi
        ]);

    }

    public function commentStatus($post_id){


        $postModel = $this->model("post", "postModel");
        $data = $postModel->commentStatus($post_id);

        $header = new Header();

        $header->back();

    }



    public function remove($post_id){


        $postModel = $this->model("post", "postModel");
        $data = $postModel->remove($post_id);

        $header = new Header();

        $header->back();

    }


}