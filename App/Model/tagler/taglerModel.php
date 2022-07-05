<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class taglerModel extends Dimodel {
    /*
     * Controller::$userInfo
     */
    /*
      function __construct()
      {
      // parent::__construct("pdo");
      // pdo - mysqli - pdox
      }
     */

    public function addTag($request) {
        return $this->table("tagler", Controller::$userInfo)
                        ->col("tag_name", $request->input("tag_name"))
                        ->col("tag_create_id", uniqid())
                        ->save_();
    }

    public function getTagler() {

        return $this->table("tagler", Controller::$userInfo)->getAll();
    }

    public function tagGuncelle($request) {
        return $this->table("tagler", Controller::$userInfo)->find($request->input("id"))
                        ->col("tag_name", $request->input("value"))
                        ->update_();
    }

}
