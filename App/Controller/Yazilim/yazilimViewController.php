<?php
namespace App\Controller\Yazilim;

class yazilimViewController extends \Dipa\Controller
{

   public function __construct() {
        parent::__construct(true);
    }

    public function index() {
        
        $model = $this->model("yazilim","yazilimModel");
        
        
        $yazilimlar = $model->getList();

    
        return $this->view("yazilim/index",['yazilimlar'=>$yazilimlar]);
    }
    
    
        public function edit($yazilim_id) {
        
        $model = $this->model("yazilim","yazilimModel");
        
        
        $yazilim = $model->getDevice($yazilim_id);

        $kasalar = $model->kasaListesi();
        
        $depolar = $model->stokDepolariAl();
        
        $hesaplar = $model->hesaplar();
    
        return $this->view("yazilim/yazilim-edit",[
            'yazilim'=>$yazilim,
            'kasalar'=>$kasalar,
            'depolar'=>$depolar,
            'hesaplar'=>$hesaplar
                
                
                ]);
    }

}