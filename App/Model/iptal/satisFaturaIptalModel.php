<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class satisFaturaIptalModel extends Dimodel {

    private $owner_id;
    private $ip_adresi;
    private $hata_mesaj = "";
    private  $helper;

    public function __construct() {
    parent::__construct();
    $this->owner_id = Controller::$userInfo['owner_id'];
    $this->ip_adresi = "";
    $this->helper = Controller::helper(null,"stokAdetHelper");
}

    public function getErrorMsj() {
        return $this->hata_mesaj;
    }

    public function satisIptal($evrak_id, $iptal_sebebi) {

        $bugun = date("Y-m-d h:i:s", strtotime("+7 days"));

        $satis_fatura = $this->getConnection()->prepare("SELECT * FROM satis_evraklari WHERE remove = 0 and owner_id = ? and id = ?");

        $satis_fatura->execute([$this->owner_id, $evrak_id]);

        $satis_fatura_result = $satis_fatura->fetch(PDO::FETCH_ASSOC);

        if ($satis_fatura_result) {

            if ($satis_fatura_result["created_date"] < $bugun) {

                $this->getConnection()->beginTransaction();

                $satis_evrak_id = $satis_fatura_result["id"];

                $satis_tahsilat = $this->getConnection()->prepare("SELECT * FROM tahsilatlar WHERE remove = 0 and owner_id = ? and satis_evrak_id = ?");

                $satis_tahsilat->execute([$this->owner_id, $satis_evrak_id]);

                $satis_tahsilat_result = $satis_tahsilat->fetch(PDO::FETCH_ASSOC);

                $tahsilat_sil = true;

                if ($satis_tahsilat_result) {

                    $tahsilat_sil = $this->satisTahsilatIptal_($satis_evrak_id);
                }

                $satis_sil = $this->satisEvrakIptal_($satis_evrak_id, $iptal_sebebi);

                if ($satis_sil && $tahsilat_sil) {

                    $this->getConnection()->commit();

                    return true;
                } else {


                    $this->getConnection()->rollBack();
                    return false;
                }
            } else {
                $this->hata_mesaj .= "İptal Tarihi Geçmiş " . $bugun;

                return false;
            }
        } else {

            $this->hata_mesaj .= "Fatura bulunamadı";
            return false;
        }
    }

    public function satisEvrakIptal_($satis_evrak_id, $iptal_sebebi) {

        $kalemler_iptal = $this->satisEvrakKalemlerIptal_($satis_evrak_id);

        if ($kalemler_iptal) {

            $sql = "UPDATE "
                    . "satis_evraklari "
                    . "SET "
                    . "remove = ? , "
                    . "islem_notu = ? , "
                    . "updated_user = ? , "
                    . "update_date = ? , "
                    . "ip = ? "
                    . "WHERE id = ?";

            $evrak_remove_query = $this->getConnection()->prepare($sql);

            return $evrak_remove_query->execute([
                        1,
                        $iptal_sebebi,
                        Controller::$userInfo['id'],
                        date("Y-m-d h:i:s"),
                        $this->ip_adresi,
                        $satis_evrak_id]);
        } else {

            $this->hata_mesaj .= " Kalemler İptal Edilemedi";
            return false;
        }
    }

    public function satisEvrakKalemlerIptal_($satis_evrak_id) {

        $kalemler_query = $this->getConnection()->prepare("SELECT * FROM stok_haraket_cikis WHERE remove = 0 and owner_id = ? and satis_evrak_id = ?");

        $kalemler_query->execute([$this->owner_id, $satis_evrak_id]);

        $kalemler_query_result = $kalemler_query->fetchAll(PDO::FETCH_ASSOC);

        if ($kalemler_query_result) {

            $status = true;

            foreach ($kalemler_query_result as $key => $value) {

                $stok_id = $value["stok_id"];

                $adet = $value["adet"];

                $hareket_id = $value["id"];

                    $hareket_query = $this->getConnection()->prepare("UPDATE stok_haraket_cikis SET remove = 1 , updated_user = ? , update_date = ? WHERE id = ?");

                    $hareket_query_result = $hareket_query->execute([Controller::$userInfo['id'], date("Y-m-d h:i:s"), $hareket_id]);

                    if (!$hareket_query_result) {

                        $status = false;
                    }


                $this->helper->set($this->getConnection() ,$stok_id)->count(true)->reset(true);

            }

            return $status;
        } else {

            return true;
        }
    }

    public function satisTahsilatIptal_($satis_evrak_id) {

        $tarih = date("Y-m-d h:i:s");

        $tahsilat_query = $this->getConnection()->prepare("SELECT * FROM tahsilatlar WHERE remove = 0 and owner_id = ? and satis_evrak_id = ?");

        $tahsilat_query->execute([$this->owner_id, $satis_evrak_id]);

        $tahsilat_query_result = $tahsilat_query->fetchAll(PDO::FETCH_ASSOC);

        if ($tahsilat_query_result) {

            $return = true;

            foreach ($tahsilat_query_result as $key => $value) {

                $islem_tip = $value["islem_tip"];

                $islem_id = $value["islem_id"];

                if ($islem_tip == "kasanakit") {

                    $nakit_tahsilat_al_query = $this->getConnection()->prepare("SELECT * FROM kasa_haraket WHERE id = ?");
                    $nakit_tahsilat_al_query->execute([$islem_id]);
                    $tahsilat_al_query_result = $nakit_tahsilat_al_query->fetch(PDO::FETCH_ASSOC);

                    if ($tahsilat_al_query_result) {

                        $haraket_id = $tahsilat_al_query_result["id"];
                        $tutar = $tahsilat_al_query_result["kasa_haraket_tutar"];
                        $kasa_id = $tahsilat_al_query_result["kasa_id"];

                        $kasasan_cikar_query_sql = "UPDATE kasalar SET kasa_toplam_tutar = kasa_toplam_tutar-? , update_date = ? WHERE id = ?";
                        $kasasan_cikar_query = $this->getConnection()->prepare($kasasan_cikar_query_sql);
                        $kasa_cikar_result = $kasasan_cikar_query->execute([$tutar, $tarih, $kasa_id]);

                        if ($kasa_cikar_result) {

                            $hareket_sil_query_sql = "UPDATE kasa_haraket SET remove = 1  WHERE id = ?";
                            $hareket_sil_query = $this->getConnection()->prepare($hareket_sil_query_sql);
                            $k_h_sil_result = $hareket_sil_query->execute([$haraket_id]);

                            if (!$k_h_sil_result) {
                                $return = false;
                            }
                        } else {

                            $return = false;
                        }
                    } else {

                        $return = false;
                    }
                } else if ($islem_tip == "banka") {



                    $banka_tahsilat_al_query = $this->getConnection()->prepare("SELECT * FROM banka_hareket WHERE id = ?");
                    $banka_tahsilat_al_query->execute([$islem_id]);
                    $banka_tahsilat_al_query_result = $banka_tahsilat_al_query->fetch(PDO::FETCH_ASSOC);

                    if ($banka_tahsilat_al_query_result) {

                        $bhareket_id = $banka_tahsilat_al_query_result["id"];

                        $banka_hesap_id = $banka_tahsilat_al_query_result["banka_hesap_id"];

                        $tutar = $banka_tahsilat_al_query_result["banka_haraket_tutar"];

                        $bankada_cikar_query_sql = "UPDATE banka_hesaplari SET hesap_bakiyesi = hesap_bakiyesi-? , update_date = ? WHERE id = ?";
                        $bankada_cikar_query = $this->getConnection()->prepare($bankada_cikar_query_sql);
                        $bankada_cikar_query_result = $bankada_cikar_query->execute([$tutar, $tarih, $banka_hesap_id]);

                        if ($bankada_cikar_query_result) {

                            $banka_sil_query_sql = "UPDATE banka_hareket SET remove = ? , update_date = ?  WHERE id = ?";
                            $banka_sil_query = $this->getConnection()->prepare($banka_sil_query_sql);
                            $bk_h_sil_result = $banka_sil_query->execute([1,$tarih,$bhareket_id]);

                            if (!$bk_h_sil_result) {

                                $return = false;
                            }

                        } else {

                            $return = false;
                        }
                    } else {

                        $return = false;
                    }
                }
            }


            if ($return) {

                $tahsilat_sil_query = "UPDATE tahsilatlar SET remove = 1 WHERE remove = 0 and owner_id = ? and satis_evrak_id = ?";
                $tahsilat_sil_query = $this->getConnection()->prepare($tahsilat_sil_query);
                return $tahsilat_sil_query->execute([$this->owner_id, $satis_evrak_id]);
            } else {

                return false;
            }
        } else {

            return true;
        }
    }

}
