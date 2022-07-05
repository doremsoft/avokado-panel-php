<?php
namespace App\Controller\User;

class userViewController extends \Dipa\Controller
{
    public function __construct() {
        parent::__construct(true);
    }
    
    
    
    public function edit($id=NULL){
        return $this->view("user/edit",['user'=> self::$userInfo]);
    }

      public function passwordEdit($id=NULL){
        return $this->view("user/password-edit",['user'=> self::$userInfo]);
    }
    
}