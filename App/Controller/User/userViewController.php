<?php

namespace App\Controller\User;

class userViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function show($id = NULL) {

        return $this->view("user/edit", ['user' => self::$userInfo]);
    }

    public function add($id = NULL) {
        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();

        return $this->view("user/add", ['kasalar' => $kasalar]);
    }

    public function edit($id = NULL) {
        $model = $this->model("kasa", "kasalarModel");

        $kasalar = $model->kasaListesi();



        if ($id == NULL) {

            return $this->view("user/edit", ['user_data' => self::$userInfo,  'kasalar' => $kasalar]);

        } else {



            $model = $this->model("user", "userActionModel");

            $user_data = $model->getUser($id);



            return $this->view("user/edit", ['user_data' => $user_data,  'kasalar' => $kasalar]);
        }
    }

    public function passwordEdit($id = NULL) {
        return $this->view("user/password-edit", ['user' => self::$userInfo]);
    }

    public function list($id = NULL) {


        $model = $this->model("user", "userActionModel");

        $user_list = $model->getUserList();

        return $this->view("user/list", ['user_list' => $user_list]);
    }

    public function yetki($id) {


        $model = $this->model("user", "userActionModel");

        $user_data = $model->getUser($id);


        $user_auths = unserialize($user_data["auths"]);


        $auth_list = [
            "welcome" => [
                "a" => "Anasayfa",
                "k" => "welcome",
                "c" => []
            ],
            "musteri" => [
                "a" => "Müşteri",
                "k" => "musteri",
                "c" => [
                    "yeni_musteri" => "Yeni Müşteri",
                    "musteri_listesi" => "Müşteri Listesi"
                ]
            ],
            "tedarikci" => [
                "a" => "Tedarikçi",
                "k" => "tedarikci",
                "c" => [
                    "yeni_tedarkci" => "Yeni Tedarikçi",
                    "tedarikci_listesi" => "Tedarikçi Listesi"
                ]
            ],  "stok" => [
                "a" => "Stoklar",
                "k" => "stok",
                "c" => [
                    "yeni_stok" => "Stok Ekleyebilir",
                    "stok_duzenle" => "Stok Düzenleyebilir",
                    "stok_sil" => "Stok Silebilir",
                    "stok_ara" => "Stok Arayabilir",
                    "stok_liste" => "Stok Listeleyebilir",
                    "stok_gor" => "Stok Detayları Görüntüleyebilir"
                ]
            ], "fatura" => [
                "a" => "Fatura",
                "k" => "fatura",
                "c" => [
                    "yeni_satis_fatura" => "Yeni Satış Fatura",
                    "yeni_alim_fatura" => "Yeni Alım Fatura",
                    "alim_fatura" => "Alım Fatura İçeriği Görüntüleme",
                    "satis_fatura" => "Satış Fatura İçeriği Görüntüleme",
                    "alim_fatura_duzenle" => "Alım Fatura Düzenleme",
                    "satis_fatura_duzenle" => "Satış Fatura Düzenleme",
                    "alim_fatura_iptal" => "Alım Fatura İptal",
                    "satis_fatura_iptal" => "Satış Fatura İptal"

                ]
            ],
        ];




        return $this->view("user/auth", ['user_data' => $user_data, 'au' => $user_auths, 'auth_list' => $auth_list]);
    }

}
