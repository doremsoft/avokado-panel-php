<?php
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
/**
 *
 * @author Doğuş DİCLE
 */

class markaModel extends Dimodel {
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
    public function addMarka($request) {
        return $this->table("markalar", Controller::$userInfo)
                        ->col("marka_adi", $request->input("marka_adi"))
                        ->save_();
    }

    public function getMarkalar() {

        return $this->table("markalar", Controller::$userInfo)->orderBy(" ORDER BY marka_adi")->getAll();
    }

    public function markaGuncelle($request) {
        return $this->table("markalar", Controller::$userInfo)->find($request->input("id"))
                        ->col("marka_adi", $request->input("value"))
                        ->update_();
    }

    public function markaResimlerGuncelle($request){

        $markalar = $request->input("marka");

        foreach ($markalar as $key => $val){

                $this->table("markalar", Controller::$userInfo)->find($key)
                    ->col("logo", $val)
                    ->update_();




        }

    }


}
