<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class dovizModel extends Dimodel {
    /*
     * Controller::$userInfo
     */

    public function kurlariGuncelle($gunluk_kontrol = false, $user_info = NULL) {

        $bugun_mevcut = false;


        if ($user_info == NULL) {

            $user_info = Controller::$userInfo;
        }

        if ($gunluk_kontrol) {


            $today = date("Y-m-d");

            $bugun = $this->table("doviz", $user_info)->select("id")->where([
                        "last_date" => ["=", $today]
                    ])->get();

            if ($bugun) {

                $bugun_mevcut = true;
            }
        }

        if (!$bugun_mevcut) {

            $today = date("Y-m-d");

            $mevcut_dovizler = $this->table("doviz", $user_info)->getAll();

            $dovizler = [];

            $doviz_kurlari = file_get_contents("https://www.tcmb.gov.tr/kurlar/today.xml");

            $dovizler = json_decode(json_encode(simplexml_load_string($doviz_kurlari)), true);

            $dovizler["Currency"][] = [
                "@attributes" => ["Kod" => "TL"],
                "Isim" => "TÜRK LİRASI",
                "ForexBuying" => "1.0000",
                "ForexSelling" => "1.0000",
                "BanknoteBuying" => "1.0000",
                "BanknoteSelling" => "1.0000"
            ];

            $mevcut_liste = [];


            if (is_array($mevcut_dovizler)) {

                foreach ($mevcut_dovizler as $key => $value) {

                    $mevcut_liste[$value["doviz_kod"]] = $value["id"];
                }
            }

            $update_date = (String) date("Y-m-d H:i:s");

            if (is_array($dovizler)) {

                foreach ($dovizler["Currency"] as $key => $value) {

                    $doviz_kod = $value["@attributes"]["Kod"];

                    $doviz_kod = trim($doviz_kod);
                    if ($doviz_kod == "TL" || $doviz_kod == "EUR" || $doviz_kod == "USD") {

                        $doviz_adi = $value["Isim"];

                        $ForexBuying = $value["ForexBuying"];

                        $ForexSelling = $value["ForexSelling"];

                        $BanknoteBuying = $value["BanknoteBuying"];

                        $BanknoteSelling = $value["BanknoteSelling"];


                        if (is_array($ForexSelling)) {

                            if (isset($ForexSelling[0])) {
                                $ForexSelling = $ForexSelling[0];
                            } else {

                                $ForexSelling = "0.0000";
                            }
                        }


                        if (is_array($BanknoteBuying)) {

                            if (isset($BanknoteBuying[0])) {

                                $BanknoteBuying = $BanknoteBuying[0];
                            } else {
                                $BanknoteBuying = "0.0000";
                            }
                        }



                        if (is_array($BanknoteSelling)) {

                            if (isset($BanknoteSelling[0])) {
                                $BanknoteSelling = $BanknoteSelling[0];
                            } else {
                                $BanknoteSelling = "0.0000";
                            }
                        }


                        $doviz_tl_kur = $ForexBuying;

                        $doviz_id = 0;

                        if (isset($mevcut_liste[$doviz_kod])) {

                            $doviz_id = $mevcut_liste[$doviz_kod];
                        }



                        if ($doviz_id > 0) {


                            $updatequery = $this->getConnection()->prepare(
                                    "UPDATE doviz "
                                    . "SET 
                                doviz_adi = ?,
                                doviz_kur = ?,
                                doviz_kod = ?,
                                ForexBuying = ?,
                                ForexSelling = ?,
                                BanknoteBuying = ?,
                                BanknoteSelling = ? ,
                                update_date = ? ,
                                last_date = ? 
                                WHERE id = ? ");

                            $updatequery->bindParam(1, $doviz_adi, PDO::PARAM_STR);
                            $updatequery->bindParam(2, $doviz_tl_kur, PDO::PARAM_STR);
                            $updatequery->bindParam(3, $doviz_kod, PDO::PARAM_STR);
                            $updatequery->bindParam(4, $ForexBuying, PDO::PARAM_STR);
                            $updatequery->bindParam(5, $ForexSelling, PDO::PARAM_STR);
                            $updatequery->bindParam(6, $BanknoteBuying, PDO::PARAM_STR);
                            $updatequery->bindParam(7, $BanknoteSelling, PDO::PARAM_STR);
                            $updatequery->bindParam(8, $update_date, PDO::PARAM_STR);

                            $updatequery->bindParam(9, $today, PDO::PARAM_STR);

                            $updatequery->bindParam(10, $doviz_id, PDO::PARAM_INT);



                            $updatequery->execute();
                        } else {


                            $query = $this->getConnection()->prepare(
                                    "INSERT INTO doviz "
                                    . "(doviz_adi,doviz_kur,doviz_kod,ForexBuying,ForexSelling,BanknoteBuying,BanknoteSelling,created_date,owner_id,last_date) "
                                    . "VALUES (?,?,?,?,?,?,?,?,?,?)");

                            $query->bindParam(1, $doviz_adi, PDO::PARAM_STR);
                            $query->bindParam(2, $doviz_tl_kur, PDO::PARAM_STR);
                            $query->bindParam(3, $doviz_kod, PDO::PARAM_STR);
                            $query->bindParam(4, $ForexBuying, PDO::PARAM_STR);
                            $query->bindParam(5, $ForexSelling, PDO::PARAM_STR);
                            $query->bindParam(6, $BanknoteBuying, PDO::PARAM_STR);
                            $query->bindParam(7, $BanknoteSelling, PDO::PARAM_STR);
                            $query->bindParam(8, $update_date, PDO::PARAM_STR);
                            $query->bindParam(9, $user_info["owner_id"], PDO::PARAM_STR);

                            $query->bindParam(10, $today, PDO::PARAM_STR);

                            $query->execute();
                        }

                        $query = NULL;
                        $doviz_id = 0;
                    }
                }
            }
        }

        return true;
    }

    public function dovizleriAl() {

        return $this->table("doviz", Controller::$userInfo)->getAll();
    }


    public function getDovizList() {


        $sql =  "SELECT doviz_kod , doviz_kur   FROM doviz WHERE owner_id = ? and remove = 0";

        $query = $this->getConnection()->prepare($sql);
        $query->execute([Controller::$userInfo["owner_id"]]);

        return $query->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

    }

    public function kurGuncelle($id, $kur) {

        $today = date("Y-m-d");
        return $this->table("doviz", Controller::$userInfo)->find($id)
                        ->col("doviz_kur", $kur)
                        ->col("last_date", $today)
                        ->update_();
    }

}
