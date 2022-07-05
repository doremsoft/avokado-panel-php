<?php

namespace App\Controller\User;

use \Dipa\Http\Request;
use \Dipa\Http\Header;
use \Dipa\Io\File;

class userActionController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function update() {

        $request = new Request();

        $header = new Header();

        if ($request->has("name") && $request->has("surname") && $request->has("email")) {

            if (isset($_FILES['image'])) {

                $file = new File();

                $kontroller = ['ex' => ['jpg'], 'max-size' => 241242];

                $session = new Session();

                $account_no = $session->get("account_no");

                if ($file->upload($_FILES['image'], "media/" . $account_no . "/userpicture/", $kontroller, time())) {

                    $request->append("image", $file->get_base_name());
                }
            }

            $userModel = $this->model("user", "userActionModel");

            $result = $userModel->updateUser($request, self::$userInfo['id']);

            if ($result) {

                $header->result("success", "Bilgiler Güncellendi")->to("user/edit");
            } else {
                $header->result("fail", "Bilgiler Güncellenemedi!")->back();
            }
        } else {
            $header->result("fail", "Bütün Zorunlu Alanlar Doldurulmalı!")->back();
        }
    }

    public function passwordUpdate() {

        $request = new Request();

        $header = new Header();

        if ($request->has("eskisifre") && $request->has("yenisifre") && $request->has("yenisifretekrar")) {

            if ($request->has("yenisifre") == $request->has("yenisifretekrar")) {

                $userModel = $this->model("user", "userActionModel");

                $result = $userModel->updateUserPassword($request, self::$userInfo['id']);

                if ($result) {

                    $header->result("success", "Şifre Güncellendi")->to("user/password-edit");
                } else {

                    $header->result("fail", "Şifre Güncellenemedi!")->back();
                }
            } else {
                $header->result("fail", "İki Şifre Birbiri İle Uyuşmuyor!")->back();
            }
        } else {
            $header->result("fail", "Bütün Zorunlu Alanlar Doldurulmalı!")->back();
        }
    }

}
