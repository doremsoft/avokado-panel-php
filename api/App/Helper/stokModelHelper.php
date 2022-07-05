<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of stokModelHelper
 *
 * @author dogus
 */
class stokModelHelper
{


    private $stok_list_select =  " 
stok.id, 
stok.stok_kod , 
stok.stok_adet,
stok.stok_satis_fiyati , 
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,


IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
IF(lst.id IS NULL ,stok.stok_adi,lst.stok_adi) AS stok_adi,
IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_full_ad

";


    private $stok_varyant_select_full = "
    stok.* , 
stok.stok_parent_id,
stok.stok_adet,
stok.id , 
stok.stok_kod , 
stok.stok_resim , 
stok.stok_alis_fiyati  , 
stok.stok_satis_fiyati , 
stok.stok_max_iskontolu_satis_fiyati ,
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,

IF(lst.id IS NULL ,stok.stok_fiyat_vergi_durum,lst.stok_fiyat_vergi_durum) AS stok_fiyat_vergi_durum , 
IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
IF(lst.id IS NULL ,stok.stok_alim_doviz,lst.stok_alim_doviz) AS stok_alim_doviz , 
IF(lst.id IS NULL ,stok.stok_birimi,lst.stok_birimi) AS stok_birimi , 
IF(lst.id IS NULL ,stok.stok_resim,lst.stok_resim) AS stok_resim , 
IF(lst.id IS NULL ,stok.stok_adi,lst.stok_adi) AS stok_adi,
IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_full_ad ";


    private $stok_varyant_select = "
stok.stok_parent_id,
stok.stok_adet,
stok.id , 
stok.stok_kod , 
stok.stok_resim , 
stok.stok_alis_fiyati  , 
stok.stok_satis_fiyati , 
stok.stok_max_iskontolu_satis_fiyati ,
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,

IF(lst.id IS NULL ,stok.stok_fiyat_vergi_durum,lst.stok_fiyat_vergi_durum) AS stok_fiyat_vergi_durum , 
IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
IF(lst.id IS NULL ,stok.stok_alim_doviz,lst.stok_alim_doviz) AS stok_alim_doviz , 
IF(lst.id IS NULL ,stok.stok_birimi,lst.stok_birimi) AS stok_birimi , 
IF(lst.id IS NULL ,stok.stok_resim,lst.stok_resim) AS stok_resim , 
IF(lst.id IS NULL ,stok.stok_adi,lst.stok_adi) AS stok_adi,
IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_full_ad ";


    private $stok_varyant_select_name = "
stok.stok_parent_id,
stok.stok_adet,
stok.id , 
stok.stok_kod , 
stok.stok_satis_fiyati , 
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,

IF(lst.id IS NULL ,stok.stok_adi,lst.stok_adi) AS stok_adi,
IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_full_ad ";


    private $stok_adet_select = " , 
   stok.id as st_id ,  stok.stok_adet 
   ";

    public function getStokVaryantSelect($adet = false)
    {


        if($adet){

            return $this->stok_varyant_select.$this->stok_adet_select;

        }else{

            return $this->stok_varyant_select;
        }




    }

    public function getStokVaryantSelectName($adet = false)
    {


        if($adet){

            return $this->stok_varyant_select_name.$this->stok_adet_select;

        }else{

            return $this->stok_varyant_select_name;
        }

    }

    public function getStokVaryantSelectFull($adet = false)
    {

        if($adet){

            return $this->stok_varyant_select_full.$this->stok_adet_select;

        }else{

            return $this->stok_varyant_select_full;
        }

    }


    public function getStokList($adet = false)
    {

        if($adet){

            return $this->stok_list_select.$this->stok_adet_select;

        }else{

            return $this->stok_list_select;
        }

    }

    public function getLeft($array = false){

        if($array){
            return ["stok as lst" => "stok.stok_parent_id = lst.id"];
        }else{

            return " LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id  ";
        }

    }


    public function getStok($id,$owner_id){
        return "SELECT   
stok.* , 
stok.stok_parent_id ,
stok.id , 
stok.stok_kod , 
stok.stok_resim , 
stok.stok_alis_fiyati  , 
stok.stok_satis_fiyati , 
stok.stok_max_iskontolu_satis_fiyati ,
stok.stok_parent_id as s_pid , 
stok.stok_varyant_adi,
stok.stok_varyant_deger,
 stok.stok_web_title , 
 stok.stok_web_description , 
IF(lst.id IS NULL ,stok.stok_fiyat_vergi_durum,lst.stok_fiyat_vergi_durum) AS stok_fiyat_vergi_durum , 
IF(lst.id IS NULL ,stok.stok_kdv_oran,lst.stok_kdv_oran) AS stok_kdv_oran , 
IF(lst.id IS NULL ,stok.stok_doviz,lst.stok_doviz) AS stok_doviz , 
IF(lst.id IS NULL ,stok.stok_alim_doviz,lst.stok_alim_doviz) AS stok_alim_doviz , 
IF(lst.id IS NULL ,stok.stok_birimi,lst.stok_birimi) AS stok_birimi , 
IF(lst.id IS NULL ,stok.stok_resim,lst.stok_resim) AS stok_resim , 
IF(lst.id IS NULL ,stok.stok_adi,lst.stok_adi) AS stok_adi,
IF(lst.id IS NULL ,stok.stok_adi , CONCAT(lst.stok_adi , \" \" ,stok.stok_varyant_adi,\" \",stok.stok_varyant_deger ) ) AS stok_full_ad ,
stok.id as st_id , 
(
IFNULL((SELECT SUM(adet) AS ta FROM stok_haraket_giris WHERE stok_haraket_giris.stok_id = st_id and stok_haraket_giris.adet_etkisiz = 0 and stok_haraket_giris.remove = 0) , 0 ) -
IFNULL((SELECT SUM(adet) AS ti FROM stok_haraket_cikis WHERE stok_haraket_cikis.stok_id = st_id and stok_haraket_cikis.adet_etkisiz = 0 and stok_haraket_cikis.remove = 0) , 0 ) 
 ) as stok_adet  
 
 
FROM 
 
 stok  
 
 LEFT JOIN stok as lst ON stok.stok_parent_id = lst.id 

 WHERE 
 
 stok.remove = 0 and stok.owner_id = {$owner_id} and stok.id = {$id} 
";
    }

}
