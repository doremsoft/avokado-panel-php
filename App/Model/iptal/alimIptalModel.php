<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class alimIptalModel extends Dimodel
{

    private $owner_id;
    private $ip_adresi;
    private $helper;



    public function __construct() {
        parent::__construct();
        $this->helper = Controller::helper(null,"stokAdetHelper");
    }



    public function alimIptal($evrak_id, $iptal_sebebi)
    {

        $this->owner_id = Controller::$userInfo['owner_id'];

        $this->ip_adresi = "";

        $alim_evrak = $this->getConnection()->prepare("SELECT * FROM alim_evraklari WHERE remove = 0 and owner_id = ? and id = ?");

        $alim_evrak->execute([$this->owner_id, $evrak_id]);

        $alim_evrak_result = $alim_evrak->fetch(PDO::FETCH_ASSOC);

        if ($alim_evrak_result) {

            $this->getConnection()->beginTransaction();

            $hareket_query = $this->getConnection()->prepare("UPDATE stok_haraket_giris SET remove = 1 , updated_user = ? , update_date = ? WHERE alim_evrak_id = ?");

            $hareket_query_result = $hareket_query->execute([Controller::$userInfo['id'], date("Y-m-d h:i:s"), $evrak_id]);

            if ($hareket_query_result) {
                $sql = "UPDATE "
                    . "alim_evraklari "
                    . "SET "
                    . "remove = ? , "
                    . "evrak_detayi = evrak_detayi  + ? , "
                    . "updated_user = ? , "
                    . "update_date = ?  "
                    . "WHERE id = ?";

                $evrak_remove_query = $this->getConnection()->prepare($sql);

                $evrak_sil_sonuc = $evrak_remove_query->execute([1,
                    $iptal_sebebi,
                    Controller::$userInfo['id'],
                    date("Y-m-d h:i:s"),
                    $evrak_id]);


                if ($evrak_sil_sonuc) {

                    $this->getConnection()->commit();

                    return true;

                } else {
                    $this->getConnection()->rollBack();
                    return false;
                }

            } else {
                $this->getConnection()->rollBack();
                return false;
            }


        } else {

            return false;
        }


    }


}
