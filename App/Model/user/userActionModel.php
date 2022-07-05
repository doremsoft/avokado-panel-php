<?php

use \Dipa\Db\Dimodel;
use \Dipa\Support\Hash;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class userActionModel extends Dimodel {

    public function updateUser($request, $id) {



        $user = $this->table("users")->find($id);

        $user->col["name"] = $request->input("name");

        if(trim($request->input("password")) != ""){


            $user->col["password"] = Hash::generate("password", $request->input("password"));

        }

        $user->col["surname"] = $request->input("surname");

        $user->col["email"] = $request->input("email");

        $user->col["gender"] = $request->input("gender");

        $user->col["phone"] = $request->input("phone");

        $user->col["admin"] = $request->input("admin");

        $user->col["offline_activate"] = $request->input("offline_activate");
        $user->col["offline_code"] = $request->input("offline_code");
        $user->col["api_permission"] = $request->input("api_permission");

        $user->col["kasa_id"] = $request->input("kasa_id");

        $user->col["web"] = $request->input("web");
        $user->col["mobile_api"] = $request->input("mobile_api");



        if ($request->input("image")) {

            $user->col["image"] = $request->input("image");
        }

        return $user->update_();
    }

    public function addUser($request) {

        $user = $this->table("users");

        $user->col["name"] = $request->input("name");

        $user->col["surname"] = $request->input("surname");


        $user->col["password"] = Hash::generate("password", $request->input("password"));


        $user->col["email"] = $request->input("email");

        $user->col["gender"] = $request->input("gender");

        $user->col["phone"] = $request->input("phone");

        $user->col["admin"] = $request->input("admin");

        $user->col["offline_activate"] = $request->input("offline_activate");

        $user->col["offline_code"] = uniqid();

        $user->col["api_permission"] = $request->input("api_permission");

        $user->col["web"] = $request->input("web");

        $user->col["kasa_id"] = $request->input("kasa_id");

        $user->col["owner_id"] = Controller::$userInfo["owner_id"];

        $user->col["mobile_api"] = $request->input("mobile_api");

        if ($request->input("image")) {

            $user->col["image"] = $request->input("image");
        } else {

            $user->col["image"] = "noimage.jpg";
        }

        return $user->save_();
    }

    public function updateUserPassword($request, $id) {

        $user = $this->table("users")->find($id);

        if ($user->col["password"] == Hash::generate("password", $request->input("eskisifre"))) {

            $user->col["password"] = Hash::generate("password", $request->input("yenisifre"));

            return $user->update_();
        } else {
            return false;
        }
    }

    public function getUserList() {

        return $this->table("users")->where(["owner_id" => ["=", Controller::$userInfo["owner_id"]]])->getAll();
    }

    public function getUser($id) {


        return $this->table("users")->find($id)->get();
    }

    public function updateAuth($id, $auth) {

        $sql = "UPDATE users SET auths = :au WHERE id = :id";
        
        $query = $this->getConnection()->prepare($sql);
        
        $query->bindParam("au", $auth, PDO::PARAM_STR);
        
        $query->bindParam("id", $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function faturaGorunumDuzenle($request,$id){


        $status = $request->input("status");

        $sql = "UPDATE users SET fatura_detayli_gorunum = :au WHERE id = :id";

        $query = $this->getConnection()->prepare($sql);

        $query->bindParam("au", $status,  PDO::PARAM_INT);

        $query->bindParam("id", $id, PDO::PARAM_INT);

        return $query->execute();

    }

}
