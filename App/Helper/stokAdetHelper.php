<?php


Class stokAdetHelper
{


    private $stok_id;
    private $varyant_id;
    private $is_varyant;
    private $model;



    public function set($model, $stok_id){

        $this->model = $model;

        $get_stok_id_query = $this->model->prepare("SELECT * FROM stok WHERE id = ?  ");
        $get_stok_id_query->execute([$stok_id]);
        $stok_data = $get_stok_id_query->fetch();


        if($stok_data){

            if($stok_data["stok_parent_id"] > 0){

                $this->is_varyant = true;
                $this->varyant_id = $stok_id;
                $this->stok_id = $stok_data["stok_parent_id"];



            }else{

                $this->varyant_id = false;
                $this->varyant_id  = 0;
                $this->stok_id = $stok_id;
            }


        }


        return $this;

    }

    public function reset($model_rest = false){


        if($model_rest){

            $this->model = null;
        }

         $this->stok_id = 0;
         $this->varyant_id= 0;
         $this->is_varyant= 0;


    }


    public function count($update_stok = false)
    {

        $stok_id = 0;

        if($this->is_varyant){

            $stok_id = $this->varyant_id;

        }else{

            $stok_id = $this->stok_id;

        }


        $giris_sql = "SELECT SUM(adet) AS ta FROM stok_haraket_giris WHERE stok_haraket_giris.stok_id = ? and stok_haraket_giris.adet_etkisiz = 0  and stok_haraket_giris.remove = 0";
        $cikis_sql = "SELECT SUM(adet) AS ti FROM stok_haraket_cikis WHERE stok_haraket_cikis.stok_id = ? and stok_haraket_cikis.adet_etkisiz = 0 and stok_haraket_cikis.remove = 0";

        $giris = 0;
        $cikis = 0;


        $giris_query = $this->model->prepare($giris_sql);
        $giris_query->execute([$stok_id]);
        $giris_result = $giris_query->fetch(PDO::FETCH_ASSOC);

        if($giris_result){

            $giris = $giris_result["ta"];
        }




        $cikis_query = $this->model->prepare($cikis_sql);
        $cikis_query->execute([$stok_id]);
        $cikis_result = $cikis_query->fetch(PDO::FETCH_ASSOC);

        if($cikis_result){

            $cikis = $cikis_result["ti"];
        }

        $count = $giris - $cikis;

        if($update_stok){

            $update_query = $this->model->prepare("UPDATE stok  SET stok_adet = ? WHERE id = ? and remove = 0");

            $update_query->execute([$count,$stok_id]);

            if($this->is_varyant){


                $varyatnlar_toplami_sql = "SELECT SUM(stok_adet) AS toplam FROM stok WHERE stok_parent_id = ?  and stok.remove = 0";

                $query = $this->model->prepare($varyatnlar_toplami_sql);

                $query->execute([$this->stok_id]);

                $result = $query->fetch(PDO::FETCH_ASSOC);

                $toplam = 0;

                $toplam = $result["toplam"];

                $update_parent_query = $this->model->prepare("UPDATE stok  SET stok_adet = ? WHERE id = ? ");

                $update_parent_query->execute([$toplam,$this->stok_id]);


            }

        }



        return $this;


    }


}
