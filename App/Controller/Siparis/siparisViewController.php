<?php

namespace App\Controller\Siparis;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class siparisViewController extends \Dipa\Controller
{

    public function __construct()
    {
        parent::__construct(true);
    }


    public function index()
    {

        $siparisModel = $this->model("siparis", "siparisViewModel");

        $siparislist = $siparisModel->getSiparisList();

        return $this->view("siparis/siparis-list", ['siparisler' => $siparislist]);
    }


    public function islem($siparis_id)
    {

        $siparisModel = $this->model("siparis", "siparisViewModel");

        $siparis = $siparisModel->getsiparis($siparis_id);


        $model = $this->model("stok", "stokHaraketModel");

        $kalemler = $model->getsatisEvrakKalemler($siparis_id);


        return $this->view("siparis/goster", ['siparis' => $siparis, 'kalemler' => $kalemler]);
    }

    public function durumDegistir($siparis_id, $durum_no)
    {


        $siparisModel = $this->model("siparis", "siparisViewModel");

        $siparis = $siparisModel->durum_degistir($siparis_id, $durum_no);

        if ($siparis) {

            $siparis_durum_mesaj = "";

            $extramesaj = "";

            $siparis_data = $siparisModel->getsiparis($siparis_id);


            switch ($durum_no) {

                case 0:
                    $siparis_durum_mesaj = "Sipariş Onayı Bekleniyor";
                    break;
                case 1:
                    $siparis_durum_mesaj = "Ödeme Bekleniyor";
                    break;
                case 2:
                    $siparis_durum_mesaj = "Tedarik Bekleniyor";
                    break;
                case 3:
                    $siparis_durum_mesaj = "Teslimat Bekleniyor";
                    break;
                case 4:
                    $siparis_durum_mesaj = "Evrak Bekleniyor";
                    break;
                case 8:
                    $siparis_durum_mesaj = "İşlem Onayı Bekleniyor";
                    break;
                case 5:
                    $siparis_durum_mesaj = "Hazırlanıyor ";
                    break;
                case 6:
                    $siparis_durum_mesaj = "Kargoya Verildi ";

                    $extramesaj = "Kargo Takip Numaranız: " . $siparis_data["kargo_no"];


                    break;
                case 7:
                    $siparis_durum_mesaj = "Teslim Edildi ";
                    break;
            }


            if ($siparis_data) {

                if ($siparis_data["email_adres"] != "" && $durum_no != 7 && $durum_no != 6) {


                    $mail = new PHPMailer(true);

                    try {
                        $mail->SMTPDebug = SMTP::DEBUG_OFF;
                        $mail->isSMTP();
                        $mail->Host = "mail.focaliav.com";
                        $mail->SMTPAuth = true;
                        $mail->Username = "bilgi@focaliav.com";
                        $mail->Password = "h1OY0pqg";
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        $mail->setFrom("bilgi@focaliav.com", "Focali Av Market");

                        $mail->addAddress($siparis_data["email_adres"]);

                        $mail->isHTML(true);

                        $mail->Subject = "Sipariş Durumu Güncellendi";

                        $mail->Body = "
                
                " . $siparis_data["siparis_kod"] . " Sipariş Numaralı Siparişinizin Sipariş Durum : " . $siparis_durum_mesaj . " Olarak Güncellenmiştir." . " <br> " . $extramesaj;

                        $mail->AltBody = '';

                        $mail->send();


                    } catch (Exception $e) {


                    }


                }


            }


            \Dipa\Io\Log::write("Sipariş Durumu Güncellendi: Sipariş id:" . $siparis_id . " Durum:" . $durum_no, self::$account_no, self::$userInfo["id"]);


            $this->header->result("success", "Sipariş Durumu Güncellendi")->back();
        } else {
            \Dipa\Io\Log::write("Sipariş Durumu Güncellenemedi!", self::$account_no, self::$userInfo["id"]);

            $this->header->result("fail", "Sipariş Durumu Güncellenemedi!!")->back();
        }


    }

    public function kargoNoGuncelle()
    {


        $siparisModel = $this->model("siparis", "siparisViewModel");

        $kayit = $siparisModel->kargoNoguncelle($this->request->input("siparisid"), $this->request->input("kargono"));

        if ($kayit) {

            $siparis_data = $siparisModel->getWebsiparis($this->request->input("siparisid"));

            if ($siparis_data) {

                if ($siparis_data["email_adres"] != "") {


                    $mail = new PHPMailer(true);

                    try {
                        $mail->SMTPDebug = SMTP::DEBUG_OFF;
                        $mail->isSMTP();
                        $mail->Host = "mail.focaliav.com";
                        $mail->SMTPAuth = true;
                        $mail->Username = "bilgi@focaliav.com";
                        $mail->Password = "h1OY0pqg";
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        $mail->setFrom("bilgi@focaliav.com", "Focali Av Market");

                        $mail->addAddress($siparis_data["email_adres"]);

                        $mail->isHTML(true);

                        $mail->Subject = "Kargo Takip Numarası Güncellendi";

                        $mail->Body = $siparis_data["siparisno"] . " Sipariş Numaralı Siparişinizin Kargo Takip Numarası : " . $this->request->input("kargono") . " Olarak Güncellenmiştir." . " <br> ";

                        $mail->AltBody = '';

                        $mail->send();




                    } catch (Exception $e) {



                    }


                }


            }else{


            }


            $this->header->result("success", "Kargo No Güncellendi...")->back();

        } else {

            $this->header->result("fail", "Kargo No Güncellenemedi!")->back();
        }


    }

    public function gecmis()
    {

        $siparisModel = $this->model("siparis", "siparisViewModel");

        $siparislist = $siparisModel->getGecmisSiparisList();

        return $this->view("siparis/gecmis", ['siparisler' => $siparislist]);
    }

    public function iptaller()
    {
        $siparisModel = $this->model("siparis", "siparisViewModel");

        $siparislist = $siparisModel->getIptalSiparisList();

        return $this->view("siparis/iptaller", ['siparisler' => $siparislist]);
    }

    public function kargodaki()
    {
        $siparisModel = $this->model("siparis", "siparisViewModel");

        $siparislist = $siparisModel->getKargodakiSiparisList();

        return $this->view("siparis/kargodaki", ['siparisler' => $siparislist]);
    }

}
