<?php
namespace App\Controller\Import;

class xmlViewController extends \Dipa\Controller
{

    public function __construct() {
        parent::__construct(true);
    }


    public function anasayfa(){




        $model = new \App\Model\xml\xmlModel();

        $xml_yuklemeler =  $model->xmlYuklemeleri($this->request);

        $xml_favori_yuklemeler =  $model->xmlFavoriYuklemeleri($this->request);


        $xmlDosyaListesi = $model->xmlDosyaListesi();

        return $this->view("xml/import-index",[
"xml_yuklemeler"=> $xml_yuklemeler,
            "xmlDosyaListesi"=>$xmlDosyaListesi,
            "kullanimdaki_xml_yuklemeler"=>$xml_favori_yuklemeler
        ]);

    }

    public function stokListesiEkle($id = 0) {

        $this->paket_kontrol(["web","standart"], "hesap-paketleri/i/web");


        $stok_ozellikleri =[

            "stok_barkod_no"=>"Stok Barkod No",
            "stok_kod" => "Stok Kod",
            "stok_adi" => "Stok Adı",
            "stok_satis_fiyati" => "Stok Satış Fiyatı 1",
            "stok_satis_fiyati2" => "Stok Satış Fiyatı 2",
            "stok_satis_fiyati3" => "Stok Satış Fiyatı 3",
            "stok_kdv_oran"=> "Kdv Oran",
            "stok_doviz" => "Stok Döviz",
            "stok_resim" => "Stok Resim 1",
            "stok_resim2" => "Stok Resim 2",
            "stok_resim3" => "Stok Resim 3",
            "stok_resim4" => "Stok Resim 4",
            "stok_detayi"=> "Stok Detayı",
            "stok_marka"=> "Stok Markası",

            "stok_varyant_adi" => "Varyant Adı",
            "stok_varyant_deger" => "Varyant Değeri",

            "varyant_barkod" => "Varyant Barkod No",
            "varyant_stok_kod" => "Varyant Stok Kodu ",
            "varyant_satis_fiyat1" => "Varyant Satış Fiyatı 1",
            "varyant_satis_fiyat2" => "Varyant Satış Fiyatı 2",
            "varyant_satis_fiyat3" => "Varyant Satış Fiyatı 3"


        ];

        $xml_data = [];
        $xmldata = [];

        $yukle = 0;

        $model = new \App\Model\xml\xmlModel();
        $xmldosyalar = $model->xmlDosyaListesi();

        $secilidosya  = "";

        if($id != 0){


            $xml_data = $model->xmlYuklemeBilgileri($id);

            if($xml_data){

                $secilidosya =   $xml_data["url_adresi"];

                $xmldata =   $xml_data["xml_data"];


                $yukle = 1;
            }


        }

        return $this->view("xml/xml-analiz",[
            "stokozellikleri"=>$stok_ozellikleri,
            "xml_data"=> $xmldata,
            "xml"=>$xml_data,
            "yukle"=>$yukle,
            "xmldosyalar"=> $xmldosyalar,
            "secilidosya"=> $secilidosya
        ]);
    }


}
