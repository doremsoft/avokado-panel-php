<?php

namespace App\Controller\Integration;
use SoapClient;

ini_set("soap.wsdl_cache_enabled", "0");


Class N11 {

    protected static $_appKey, $_appSecret, $_parameters, $_sclient;

    public $_debug = false;

    public function __construct(array $attributes = array()) {
        self::$_appKey = $attributes['appKey'];
        self::$_appSecret = $attributes['appSecret'];
        self::$_parameters = ['auth' => ['appKey' => self::$_appKey, 'appSecret' => self::$_appSecret]];
    }

    public function setUrl($url) {
        self::$_sclient = new SoapClient($url);
    }

    public function GetTopLevelCategories() {
        $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');
        return self::$_sclient->GetTopLevelCategories(self::$_parameters);
    }

    public function GetSubCategories($categoryId) {
        $this->setUrl('https://api.n11.com/ws/CategoryService.wsdl');

        self::$_parameters['categoryId'] = $categoryId;

        return self::$_sclient->GetSubCategories(self::$_parameters);
    }




    public function GetCities() {
        $this->setUrl('https://api.n11.com/ws/CityService.wsdl');
        return self::$_sclient->GetCities(self::$_parameters);
    }

    public function GetProductList($itemsPerPage, $currentPage) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['pagingData'] = ['itemsPerPage' => $itemsPerPage, 'currentPage' => $currentPage];
        return self::$_sclient->GetProductList(self::$_parameters);
    }


    public function GetShipmentTemplate() {
        $this->setUrl('https://api.n11.com/ws/ShipmentService.wsdl');
        return self::$_sclient->GetShipmentTemplate(self::$_parameters);
    }


    public function GetProductBySellerCode($sellerCode) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['sellerCode'] = $sellerCode;
        return self::$_sclient->GetProductBySellerCode(self::$_parameters);
    }

    public function SaveProduct(array $product = Array()) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['product'] = $product;
        return self::$_sclient->SaveProduct(self::$_parameters);
    }

    public function DeleteProductBySellerCode($sellerCode) {
        $this->setUrl('https://api.n11.com/ws/ProductService.wsdl');
        self::$_parameters['productSellerCode'] = $sellerCode;
        return self::$_sclient->DeleteProductBySellerCode(self::$_parameters);
    }

    public function OrderList(array $searchData = Array()) {
        $this->setUrl('https://api.n11.com/ws/OrderService.wsdl');
        self::$_parameters['searchData'] = $searchData;
        return self::$_sclient->OrderList(self::$_parameters);
    }

    public function __destruct() {
        if ($this->_debug) {
            print_r(self::$_parameters);
        }
    }
}


class n11Controller extends \Dipa\Controller
{

    private $n11Connection;

    public function __construct()
    {
        parent::__construct(true);

        $n11Params = [
            'appKey' => '88f3e38b-ba9e-4b28-8f5b-26acb4f1eb51',
            'appSecret' => 'rkAD8UmfrM3ule0Q'
        ];

        $this->n11Connection = new N11($n11Params);

    }

    /*
     * $n11Params = [
        'appKey' => 'API_BILGILERINIZDEN_DOLDURUN',
        'appSecret' => 'API_BILGILERINIZDEN_DOLDURUN'
    ];

    $n11 = new N11($n11Params);


    $categories = $n11->GetTopLevelCategories();
    var_dump($categories);

    //* N11 Şehir Bilgileri
    $cities = $n11->GetCities();
    var_dump($cities);

    //* N11 Ürün Listesini Çekme
    $productList = $n11->GetProductList(5, 0);
    var_dump($productList);

    //* N11 Kayıtlı Ürünü Çekme
    $getProductBySeller = $n11->GetProductBySellerCode('db0000');
    var_dump($getProductBySeller);

    //*N11 Ürün Kaydetme
    $saveProduct = $n11->SaveProduct(
                [
                    'productSellerCode' => 'az32897591',
                    'title' => 'Deneme üründür satın almayınız.',
                    'subtitle' => 'Api Test ürünü ',
                    'description' => 'Deneme  ürünümüz.',
                    'attributes' =>
                    [
                        'attribute' => Array()
                    ],
                    'category' =>
                    [
                        'id' => 1000038
                    ],
                    'price' => 0.99,
                    'currencyType' => 'TL',
                    'images' =>
                    [
                        'image' =>
                        [
                            'url' => 'http://alyamedya.com/uploads/alya-medya-logo1.png',
                            'order' => 1
                        ]
                    ],
                    'saleStartDate' => '',
                    'saleEndDate' => '',
                    'productionDate' => '',
                    'expirationDate' => '',
                    'productCondition' => '1',
                    'preparingDay' => '3',
                    'discount' => 10,
                    'shipmentTemplate' => 'Alıcı Öder',
                    'stockItems' =>
                    [
                        'stockItem' =>
                        [
                            'quantity' => 1,
                            'sellerStockCode' => 'stokkodu',
                            'attributes' =>
                            [
                                'attribute' => []
                            ],
                            'optionPrice' => 0.99
                        ]
                    ]
                ]
    );
    var_dump($saveProduct);


    //* N11 Ürün Silme
    $deleteProductBySeller = $n11->DeleteProductBySellerCode('az3289759');
    var_dump($deleteProductBySeller);

    //* N11 Sipariş Listesi
    $orderList  = $n11->OrderList (
    [
        "productId"=>'',
        "status"=> 'New',
        "buyerName"=> '',
        "orderNumber"=> '',
        "productSellerCode" =>'',
        "recipient"=> '',
        "period"=>[
            "startDate"=> '?',
            "endDate"=> '?'
        ]
    ]
    );
    var_dump($orderList);
     */

    public function sendProduct($product_id)
    {


        $stokModel = $this->model("stok", "stokModel");
        $stok = $stokModel->getStokFromN11($product_id);


        if($stok["stok_parent_id"] == 0){

            $varyantlar = $stokModel->varyantlariAl($stok["id"]);

        }else{
            $varyantlar = $stokModel->varyantlariAl($stok["stok_parent_id"]);

        }


        $kdv_oran = $stok["stok_kdv_oran"];

        $params = [];
        $params["productSellerCode"] = "az32897591";
        $params["title"] = $stok["stok_full_ad"];

        if(empty($stok["stok_web_title"]) || $stok["stok_web_title"] == ""){

            $params["subtitle"] =  $stok["stok_full_ad"];
        }else{
            $params["subtitle"] =$stok["stok_web_title"];

        }

        $params["description"] = $stok["stok_detayi"];
        $params["category"]["id"] = $stok["n11_category_no"];
        $params["price"] = $stok["stok_satis_fiyati"] * $kdv_oran;
        $params["currencyType"] = $stok["stok_doviz"];

       $params["images"] =
                    [
                        'image' =>[
                                'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim_1"],
                                'order' => 1]
                    ];

    /*

        if($stok["stok_resim_1"] != NULL && !empty($stok["stok_resim_1"]) && $stok["stok_resim_1"] != ""){




            if($stok["stok_resim_1"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim_1"],
                    'order' => 1
                ];

            }

        }else{
            if($stok["stok_resim"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim"],
                    'order' => 1
                ];
            }

        }





        if($stok["stok_resim_2"] != NULL && !empty($stok["stok_resim_2"])  && $stok["stok_resim_2"] != "" ){

            if($stok["stok_resim_2"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim_2"],
                    'order' => 2
                ];
            }


        }else{

            if($stok["stok_resim2"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim2"],
                    'order' => 2
                ];
            }

        }




        if($stok["stok_resim_3"] != NULL && !empty($stok["stok_resim_3"])  && $stok["stok_resim_3"] != "" ){

            if($stok["stok_resim_3"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim_3"],
                    'order' => 3
                ];
            }


        }else{

            if($stok["stok_resim3"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim3"],
                    'order' => 3
                ];
            }

        }



        if($stok["stok_resim_4"] != NULL && !empty($stok["stok_resim_4"])  && $stok["stok_resim_4"] != "" ){

            if($stok["stok_resim_4"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim_4"],
                    'order' => 4
                ];

            }

        }else{

            if($stok["stok_resim4"] != ""){
                $params["images"][]["image"] = [
                    'url' => "https://avokadopanel.com/media/119/s/".$stok["stok_resim4"],
                    'order' => 4
                ];
            }

        }

        */



        $params["approvalStatus"] = 1;



        /*
         * Ürün durumu 1 = yeni 2 = 2. el
         */

        $params["productCondition"] = 1;
        $params["preparingDay"] = "3";
        $params["shipmentTemplate"] = "YURTİÇİ";

        /*
         * yerli üretimmi true / false
         *
         */

        $params["domestic"] =  false;
        $params["groupAttribute"] =  "";
        $params["groupItemCode"] =  "";
        $params["itemName"] =  "";
        $params["saleStartDate"] = date("d/m/Y");
        $params["saleEndDate"] =   date("d/m/Y",strtotime("+250 day"));
        $params["productionDate"] =  "";
        $params["expirationDate"] =  "";
        $params["discount"] =  "";
        $params["attributes"]  = [];

        $params["attributes"][] = [
            "name" => "Marka",
            "value" => "Kendo"
        ];

        $params["unitInfo"] =  "";


        if($varyantlar != NULL){


            $params["stockItems"] = [];


            foreach ($varyantlar as $key => $val){


                $params["stockItems"][]=
                    [
                        'n11CatalogId' => "",
                        'quantity' => $val["stok_adet"],
                        'sellerStockCode' => $val["stok_kod"],
                        'gtin'=>  $val["stok_barkod_no"],
                        'attributes' =>
                            [
                                'attribute' => [
                                    "name" => $val["stok_varyant_adi"],
                                    "value" => $val["stok_varyant_deger"],
                                ]
                            ],

                        'optionPrice' => $val["stok_satis_fiyati"]  * $kdv_oran
                    ];

            }

        }else{


            $params["stockItems"] = [

                'stockItem' => [
                        'n11CatalogId' => "",
                        'quantity' => $stok["stok_adet"],
                        'sellerStockCode' => $stok["stok_kod"],
                        'gtin'=>  $stok["stok_barkod_no"],


                        'attributes' =>
                            [
                                'attribute' => [
                                    "name" => "Marka",
                                    "value" => "Kendo"
                                ]
                            ],

                        'optionPrice' => $stok["stok_satis_fiyati"] * $kdv_oran
                    ]
            ];



        }




        var_dump($params);
        echo "<hr>";
       $saveProduct = $this->n11Connection->SaveProduct($params);
       var_dump($saveProduct);



    }


}
