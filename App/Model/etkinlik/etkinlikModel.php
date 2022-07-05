<?php

use \Dipa\Db\Dimodel;
use \Dipa\Controller;

/**
 *
 * @author Doğuş DİCLE
 */
class etkinlikModel extends Dimodel
{
    public function etkinlikleriAl($basla, $bitir)
    {


        $baslama = date("Y-m-d", strtotime($basla)) . " " . date("H:i:s", strtotime($basla));
        $bitis = date("Y-m-d", strtotime($bitir)) . " " . date("H:i:s", strtotime($bitir));


        $etkinlik_sql = "
SELECT id , title ,start , end  
FROM yapilacaklar 
WHERE baslama >= ? and bitis <= ? and owner_id = ? and remove = ? and user_id = ? and takvim = ?   ";


        $query = $this->getConnection()->prepare($etkinlik_sql);

        $query->execute([$baslama,$bitis,Controller::$userInfo["owner_id"] , 0 , Controller::$userInfo["id"] , 1]);

        return  $query->fetchAll(PDO::FETCH_ASSOC);

    }

    public function takvimeEtkinlikTitleGuncelle($request){



        $insert =  $this->table("yapilacaklar", Controller::$userInfo)
            ->find($request->input("id"))
            ->col("title", $request->input("title"))
            ->col("user_id", Controller::$userInfo["id"])
            ->update_();



        return $insert;



    }

    public function takvimeEtkinlikEkle($request)
    {


        $start = $request->input("start");
        $end = $request->input("end");
        $tarih =  date("Y-m-d", strtotime($request->input("start")));
        $baslama = date("Y-m-d", strtotime($request->input("start"))) . " " . date("H:i:s", strtotime($request->input("start")));
        $bitis = date("Y-m-d", strtotime($request->input("end"))) . " " . date("H:i:s", strtotime($request->input("end")));
        $tamgun = 0;


        if ($request->input("allDay") == "ok") {


            $tamgun = 1;
            $start =  date("Y-m-d", strtotime($request->input("start")));
            $end = date("Y-m-d", strtotime($request->input("end")));


        }



        $insert =  $this->table("yapilacaklar", Controller::$userInfo)
            ->col("title", $request->input("title"))
            ->col("start", $start)
            ->col("end", $end)
            ->col("full_start", $request->input("fullstart"))
            ->col("full_end", $request->input("fullend"))
            ->col("baslama", $baslama)
            ->col("bitis", $bitis)
            ->col("tamgun", $tamgun)
            ->col("tarih",$tarih)
            ->col("durum",1)
            ->col("user_id", Controller::$userInfo["id"])
            ->save_();


        if($insert){


           $bildirim_id =  $this->bildirimEkle(new Controller() , 'Takvim Hatırlatması',$request->input("title") , 4 , $tarih ,  date("H:i:s", strtotime($request->input("start"))),Controller::$userInfo["id"],$insert,"alarm");

            $this->reset()->table("yapilacaklar", Controller::$userInfo)
                ->find($insert)
                ->col("bildirim_id",$bildirim_id)
                ->update_();

        }

        return $insert;


    }


    public function takvimeEtkinlikGuncelle($request)
    {


        $start = $request->input("start");
        $end = $request->input("end");
        $tarih =  date("Y-m-d", strtotime($request->input("start")));
        $baslama = date("Y-m-d", strtotime($request->input("start"))) . " " . date("H:i:s", strtotime($request->input("start")));
        $bitis = date("Y-m-d", strtotime($request->input("end"))) . " " . date("H:i:s", strtotime($request->input("end")));
        $tamgun = 0;


        if ($request->input("allDay") == "ok") {


            $tamgun = 1;
            $start =  date("Y-m-d", strtotime($request->input("start")));
            $end = date("Y-m-d", strtotime($request->input("end")));


        }



        $update =  $this->table("yapilacaklar", Controller::$userInfo)
            ->find($request->input("id"))
            ->col("title", $request->input("title"))
            ->col("start", $start)
            ->col("end", $end)
            ->col("full_start", $request->input("fullstart"))
            ->col("full_end", $request->input("fullend"))
            ->col("baslama", $baslama)
            ->col("bitis", $bitis)
            ->col("tamgun", $tamgun)
            ->col("tarih",$tarih)
            ->col("durum",1)
            ->col("user_id", Controller::$userInfo["id"])
            ->update_();


if($update){

$this->bildirimGuncelle($this->getColumn("bildirim_id"),new Controller() , 'Takvim Hatırlatması',$request->input("title") , 4 , $tarih ,  date("H:i:s", strtotime($request->input("start"))),$request->input("id"),"alarm");

}


        return $update;


    }

    public function takvimeEtkinlikIptal($request){

        $remove =  $this->table("yapilacaklar", Controller::$userInfo)
            ->find($request->input("id"))
            ->remove_();

        if($remove){

            $this->bildirimIptal($request->input("id"),new Controller());

        }

        return $remove;
    }


}
