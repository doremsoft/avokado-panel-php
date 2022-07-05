<?php
namespace App\Controller\Work;

class workViewController extends \Dipa\Controller
{


    private $menuler = [

        "Anasayfa" => "work",
        "Yeni İş Kayıt" => "work/yeni-kayit",
        "Aktif İş Listesi" => "work/aktif-servisler",
        "Proje Kayıt" => "work/yeni-proje",



    ];


    public function __construct() {
        parent::__construct(true);
    }

    public function index() {

        $aktif_url = "work";

        return $this->view("work/index",[
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,
        ]);
    }


    public function yeniKayit(){

        $aktif_url = "work/yeni-kayit";


        $bugun=date("Y-m-d");
        $model = $this->model("stok", "stokHaraketModel");
        $cari = $model->getCari(0);
        $depolar = $model->depolariGetir();

        $dovizmodel = $this->model("doviz", "dovizModel");
        $dovizkurlari =  $dovizmodel->getDovizList();

        return $this->view("work/yeni-kayit", [
            'depolar'=>json_encode($depolar),
            'bugun'=>$bugun,
            'cari' => $cari,
            'hesap_id'=>0,
            'type'=> 0,
            'dovizkurlari'=>json_encode($dovizkurlari),
            'menuler' => $this->menuler,
            'aktif_url' => $aktif_url,]);




    }


}