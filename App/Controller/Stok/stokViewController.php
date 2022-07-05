<?php

namespace App\Controller\Stok;

use \Dipa\Controller;

class stokViewController extends \Dipa\Controller {

    public function __construct() {
        parent::__construct(true);
    }

    public function add() {

        $result = $this->paket_kontrol(["standart","gold","bronz","platinium","ucretsiz"],"hesap");

        $stok_limiti = 0;

        if(isset($result["paket_tanimlama_1"])){

            $stok_limiti = $result["paket_tanimlama_1"];

        }

        parent::get_auth("stok","yeni_stok");

        $model = $this->model("stok", "stokModel");

       $toplam_stok_sayisi =  $model->toplamStokSayisi();

       if($toplam_stok_sayisi > $stok_limiti){

            $this->header->to(self::$redirect_url["hesap"]);

            die();
       }


        $birimler = $model->stokBirimleriAl();
        $siniflar = $model->stokSiniflariAl();
        $gruplar = $model->stokGruplariAl();
        $dovizler = $model->dovizleriAl();
        $tags = $model->etiketleriAl();
        $markalar = $model->markalariAl();

        $arguments = [
            'tags' => $tags,
            'birimler' => $birimler,
            'siniflar' => $siniflar,
            'gruplar' => $gruplar,
            'dovizler' => $dovizler,
            'markalar'=>$markalar
        ];

        return $this->view("stok/stok-add", $arguments);



    }

    public function fastAdd() {
        parent::get_auth("stok","yeni_stok");
        $model = $this->model("stok", "stokModel");

        $birimler = $model->stokBirimleriAl();
        $siniflar = $model->stokSiniflariAl();
        $gruplar = $model->stokGruplariAl();
        $dovizler = $model->dovizleriAl();
        $arguments = [
            'birimler' => $birimler,
            'siniflar' => $siniflar,
            'gruplar' => $gruplar,
            'dovizler' => $dovizler
        ];

        return $this->view("stok/stok-hizli-kayit", $arguments);
    }

    public function stokList($page = "" ) {

        parent::get_auth("stok","stok_liste");

        $stokModel = $this->model("stok", "stokModel");

        $stoklist = $stokModel->getStokList($page);

        $page_url = "stok/list";
        if($page != ""){
            $page_url.="/".$page;
        }


        return $this->view("stok/stok-list", ['stoklar' => $stoklist , 'filtre' => $page , 'page_url'=> $page_url]);
    }



    public function stokWebList($page = "" ) {

        parent::get_auth("stok","stok_liste");

        $stokModel = $this->model("stok", "stokModel");

        $harf = $page;

        $external_data = [];
        $external_data["tip"] =  "hepsi";
        $external_data["harf"] = $harf;

        if(isset($_GET["tip"])){

            $external_data["tip"] = $_GET["tip"];
        }




        $stoklist = $stokModel->getWebStokList($page,$external_data);

        $page_url = "stok/weblist";

        if($page != ""){
            $page_url.="/".$page;
        }

        $alp = range("A","Z");

        return $this->view("stok/stok-web-list", [
            'stoklar' => $stoklist ,
            'filtre' => $page ,
            'page_url'=> $page_url ,
            'harf'=>$harf,
            'alphbt'=> $alp,
            'ex'=>$external_data]);
    }


    public function stokKritiktekiler($page = null) {

        $stokModel = $this->model("stok", "stokModel");

        $stoklist = $stokModel->getKiritikStokList();


        return $this->view("stok/stok-kritiktekiler", ['stoklar' => $stoklist]);
    }

    public function show($id) {
        parent::get_auth("stok","stok_gor");





        $stokModel = $this->model("stok", "stokModel");
        $stok = $stokModel->getStok($id);
        
        if($stok["stok_parent_id"] == 0){
            
              $varyantlar = $stokModel->varyantlariAl($stok["id"]);
              
        }else{
               $varyantlar = $stokModel->varyantlariAl($stok["stok_parent_id"]);
            
        }

        $stok_oy = $stokModel->stokOyu($stok["id"]);


        $model2 = $this->model("stok", "stokHaraketModel");
        $depolar = $model2->depolariGetir();
        $grup_stoklar = $stokModel->grupStoklariAl($id);
        $siniflar = $stokModel->stokSiniflariAl();
        $gruplar = $stokModel->stokGruplariAl();


        $stok_haftalik = $model2->haftalikRapor($id);



        return $this->view("stok/stok-show", [
                    'varyantlar'=>$varyantlar,
                    'siniflar' => $siniflar,
                    'gruplar' => $gruplar,
                    'depolar' => json_encode($depolar),
                   'grup_stoklar'=>$grup_stoklar,
                    'haftalik_rapor'=>json_encode($stok_haftalik),
                    'oy' =>$stok_oy,
                    'stok' => $stok]);
    }

    public function edit($id , $noframe = "none") {
        parent::get_auth("stok","stok_duzenle");
        $stokModel = $this->model("stok", "stokModel");

        $stok = $stokModel->getStok($id);

        $tags = $stokModel->etiketleriAl();
        $birimler = $stokModel->stokBirimleriAl();
        $siniflar = $stokModel->stokSiniflariAl();
        $gruplar = $stokModel->stokGruplariAl();
        $dovizler = $stokModel->dovizleriAl();
        $markalar = $stokModel->markalariAl();
        $grup_stoklar = $stokModel->grupStoklariAl($id);
        $s_tags =  $stokModel->stokEtiketleriAl($id);
        
        $s_tags_data = [];
        
        if($s_tags){
            
            foreach ($s_tags as $skey => $svalue) {
                
                
                $s_tags_data[$svalue["tag_id"]] = "ok";
                
            }
            
        }



        if($noframe == "ok") {

            $noframestatus = true;

            $layout = "layout-light-nofrme.twig";

        }else{

            $noframestatus = false;

            $layout = "layout-light.twig";

        }

        $arguments = [
            'tags' => $tags,
            'select_tags'=>$s_tags_data,
            'birimler' => $birimler,
            'siniflar' => $siniflar,
            'gruplar' => $gruplar,
            'stok' => $stok,
            'dovizler' => $dovizler,
            'markalar'=>$markalar,
            'grup_stoklar'=>$grup_stoklar,
            'noframestatus'=>$noframestatus,
            'layout' =>$layout
        ];




            return $this->view("stok/stok-edit", $arguments);

    }

    public function search() {

        return $this->view("stok/stok-arama");
    }

    public function cihazStokListesiJson() {
        header('Content-disposition: attachment; filename=stoklist.json');
        header('Content-type: application/json');
        $model = $this->model("stok", "stokModel");
        $last_id = $model->getId();
        $productresult = $model->getUpdated(0);
        $productCategoriesResult = $model->getCategoriesUpdated(0);
        $result = [];
        $result["account_id"] = Controller::$account_no;
        $result["owner_id"] = Controller::$userInfo["owner_id"];
        $result["serverlastId"] = $last_id;
        $result["products"] = $productresult;
        $result["categories"] = $productCategoriesResult;
        echo json_encode($result);
    }

    public function remove($id) {
        parent::get_auth("stok","stok_sil");
        $kontrol_durum = 0;

        $stokModel = $this->model("stok", "stokHaraketModel");

        $kontrol_durum = $stokModel->stokHareketKontrol($id);

        return $this->view("stok/stok-remove", [
                    'stokid' => $id,
                    'kontrol' => $kontrol_durum
        ]);
    }

    public function fastPriceUpdate() {

        return $this->view("stok/fast-price-stok-search");
    }


    public function webStatusChange($id , $status)
    {
        parent::get_auth("stok", "stok_duzenle");

        $stokModel = $this->model("stok", "stokActionModel");

         $stokModel->updateWebStatus($id,$status);


         $header = new \Dipa\Http\Header();

        $header->back();


    }

    public function n11Control($id , $status){

    }

}
