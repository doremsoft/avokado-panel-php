<?php

use Dipa\App;
use \Dipa\Db\Dimodel;
use \Dipa\Controller;
use PHPMailer\PHPMailer\PHPMailer;

/**
 *
 * @author Doğuş DİCLE
 */
class hesaplarModel extends Dimodel {
    /*
     * Controller::$userInfo
     */



    public function getListFromServer($server_name) {

        return $this->table("account_data")->where([
            "account_server_name" => ["=",$server_name]


        ])->getAll();
    }

    public function getList() {


        return $this->table("account_data")->getAll();
    }

    public function getAccount($account_id) {

        return $this->table("account_data")->find($account_id)->get();
    }

    public function update($request) {


        return $this->table("account_data")
                        ->find($request->input("id"))
                        ->col("server_connection_protocol", $request->input("server_connection_protocol"))
                        ->col("server", $request->input("server"))
                        ->col("api_server_connection_protocol", $request->input("api_server_connection_protocol"))
                        ->col("api_server_url", $request->input("api_server_url"))
                        ->col("db_name", $request->input("db_name"))
                        ->col("account_nick_name", $request->input("account_nick_name"))
                        ->col("account_type", $request->input("account_type"))
                        ->col("server_ip", $request->input("server_ip"))
            ->col("account_server_name",$request->input("account_server_name"))
                        ->update_();
    }

    public function register($request, $account_id, $mail_code) {

        return $this->table("register")
                        ->col("account_no", $account_id)
                        ->col("ad", $request->input("ad"))
                        ->col("soyad", $request->input("soyad"))
                        ->col("gsm", $request->input("gsm"))
                        ->col("mail", $request->input("mail"))
                        ->col("vergi_unvan", $request->input("unvan"))
                        ->col("type", "standart")
                        ->col("mail_code", $mail_code)
                        ->col("last_mail", date("Y-m-d H:i:s"))
                        ->save_();
    }

    public function lastAccountId() {

        return $this->getConnection()->query("SELECT * FROM register ORDER BY id DESC ")->fetch(PDO::FETCH_ASSOC);
    }

    public function activateAccount($request, $register_id, $sifre) {

        $register_data = $this->reset()->table("register")->find($register_id)->get();

        if ($register_data) {

            $account_no = $register_data["account_no"];

            $mail_adresi = $register_data["mail"];

            $owner_id = 1;

            $this->reset()->table("account_data")
                    ->col("account_no", $account_no)
                    ->col("server_connection_protocol", $request->input("server_connection_protocol"))
                    ->col("server", $request->input("server"))
                    ->col("api_server_connection_protocol", $request->input("api_server_connection_protocol"))
                    ->col("api_server_url", $request->input("api_server_url"))
                    ->col("db_name", $request->input("db_name"))
                    ->col("account_nick_name", $request->input("account_nick_name"))
                    ->col("account_type", 2)
                    ->col("server_ip", $request->input("server_ip"))

                ->col("account_server_name",$request->input("account_server_name"))
                    ->save_();


            $this->getConnection()->prepare("INSERT INTO mail_list (mail,account_id,create_date) VALUES (?,?,NOW()) ")->execute([$mail_adresi, $account_no]);

            return true;
        } else {

            return false;
        }
    }

    public function userInsert($database_connection, $request, $sifre ,$account_no,$account_type) {


        $bildirim_session_key = base64_encode(uniqid());

        $username = str_replace("@","",  $request->input("mail"));

        $username = str_replace(".","",  $username);

        $insert = $database_connection->prepare("INSERT INTO users "
                . "(password,name,surname,phone,email,owner_id,image,gender,created_date,admin,template,bildirim_session_key,username) VALUES (?,?,?,?,?,?,'noimage.jpg','male',NOW(),?,?,?,?) ");

        $inresult = $insert->execute([
            \Dipa\Support\Hash::generate("password", $sifre),
            $request->input("ad"),
            $request->input("soyad"),
            $request->input("gsm"),
            $request->input("mail"),
            1,
            1,
            'fuse',
            $bildirim_session_key,
            $username

        ]);


        $media_key = md5(base64_encode(uniqid().uniqid().uniqid().uniqid().uniqid().time()));

        $simdi = date("Y-m-d H:i:s");


        $ucretsiz_paket_bitis_tarihi = date('Y-m-d H:i:s', strtotime("+7 day", strtotime(date("Y-m-d H:i:s"))));

        $paket_bitis_tarihi = date('Y-m-d H:i:s', strtotime("+365 day", strtotime(date("Y-m-d H:i:s"))));

        $account_data_insert = $database_connection->prepare("INSERT INTO hesap_detaylari (account_id,ilk_kayit,media_key) VALUES (?,NOW(),?)");
        $account_data_insert->execute([$account_no, $media_key]);

        $account_info_data_insert_sql = "";

        if($account_type == "1"){

            $account_info_data_insert_sql.= "
INSERT INTO hesap_paketleri 
(paket_adi,paket_tutari,paket_baslama_tarihi,paket_bitis_tarihi,paket_tanimlama_1,paket_tanimlama_2,hesap_no,owner_id,paket_key) VALUES 
('Ücretsiz',0,'{$simdi}','{$ucretsiz_paket_bitis_tarihi}','100','10','{$account_no}','1','ucretsiz');";

            $account_info_data_insert_sql.= "
INSERT INTO hesap_paketleri 
(paket_adi,paket_tutari,paket_baslama_tarihi,paket_bitis_tarihi,paket_tanimlama_1,paket_tanimlama_2,hesap_no,owner_id,paket_key) VALUES 
('Depolama Alanı',0,'{$simdi}','{$ucretsiz_paket_bitis_tarihi}','104857600','','{$account_no}','1','depolama');";

        }else if($account_type == "2"){

            $account_info_data_insert_sql.= "
INSERT INTO hesap_paketleri 
(paket_adi,paket_tutari,paket_baslama_tarihi,paket_bitis_tarihi,paket_tanimlama_1,paket_tanimlama_2,hesap_no,owner_id,paket_key) VALUES 
('Standart',0,'{$simdi}','{$paket_bitis_tarihi}','1000','1000','{$account_no}','1','standart');";
            $account_info_data_insert_sql.= "
INSERT INTO hesap_paketleri 
(paket_adi,paket_tutari,paket_baslama_tarihi,paket_bitis_tarihi,paket_tanimlama_1,paket_tanimlama_2,hesap_no,owner_id,paket_key) VALUES 
('Depolama Alanı',0,'{$simdi}','{$paket_bitis_tarihi}','1073741824','','{$account_no}','1','depolama');";


        }




        $account_info_data_insert = $database_connection->prepare($account_info_data_insert_sql);
        $account_info_data_insert->execute();


        
        return $inresult;
    }

    public function getMailData($mail) {

        return $this->table("register")->where(['mail' => ['=', $mail]])->get();
    }

    public function updateLastMailDate($id) {

        $simdi = date('Y-m-d H:i:s');
        return $this->getConnection()
                        ->prepare("UPDATE register SET last_mail = ? WHERE id = ? ")
                        ->execute([$simdi, $id]);
    }

    public function getMailHash($hash, $mail) {

        return $this->table("register")->where(['mail_code' => ['=', $hash], 'mail' => ['=', $mail]])->get();
    }

    public function getMailHashData($mail) {

        return $this->reset()->table("register")->where(['type' => ['=', "standart"], 'mail' => ['=', $mail]])->get();
    }

    public function mailKontrol($mail) {

        return $this->table("mail_list")
                        ->where(["mail" => ['=', $mail]])
                        ->get();
    }

    public function sendMail($account_no, $mail_ad, $sifreniz) {


        $mail = new PHPMailer();

        try {
            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host = 'mail.avokadoyazilim.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'onay@avokadoyazilim.com';                     // SMTP username
            $mail->Password = 'IXlx07C0';                               // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

// TCP port to connect to

            $oturum_acma_adres ="https://avokadoyazilim.com/abone/";

            //Recipients
            $mail->setFrom("onay@avokadoyazilim.com", 'Avokado Yazilim');
            $mail->addAddress($mail_ad);               // Name is optional
            $mail->addReplyTo('destek@avokadoyazilim.com', 'Avokado Destek');


            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Tebrikler Hesabiniz Aktif Edildi...';
            $mail->Body = 'Hesap Numaraniz: <b>' . $account_no . '</b><br>'
                    . 'Mail Adresiniz: <b>' . $mail_ad . '</b><br> '
                    . 'Sifreniz: <b>' . $sifreniz . '</b><br> '
                    . 'Bu Adresten : <a href="' . $oturum_acma_adres . '">' . $oturum_acma_adres . '</a> Oturum Acabilirsiniz.';

            $mail->AltBody = 'Bu mailin size gelmediğini düşünüyorsanız lütfen önemsemeyin!';
            $mail->send();

            return true;
        } catch (Exception $e) {

            $this->mail_error = $mail->ErrorInfo;

            return false;
        }
    }

    public function sendRegisterMail($account_no, $mail_ad) {


        $mail = new PHPMailer();

        try {
            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host = 'mail.avokadoyazilim.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'onay@avokadoyazilim.com';                     // SMTP username
            $mail->Password = 'IXlx07C0';                               // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );


            $mail->setFrom("onay@avokadoyazilim.com", 'Avokado Yazilim');
            $mail->addAddress("kayit@avokadoyazilim.com");
            $mail->isHTML(true);
            $mail->Subject = 'Yeni Kayit:' . $account_no;
            $mail->Body = 'Hesap Numarasi: <b>' . $account_no . '</b><br>'
                    . 'Mail Adresi: <b>' . $mail_ad . '</b><br> ';

            $mail->AltBody = '';
            $mail->send();

            return true;
        } catch (Exception $e) {

            $this->mail_error = $mail->ErrorInfo;

            return false;
        }
    }

}
