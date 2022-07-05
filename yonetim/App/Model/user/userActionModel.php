<?php
use \Dipa\Db\Dimodel;
use \Dipa\Support\Hash;
/**
 *
 * @author Doğuş DİCLE
 */
class userActionModel extends Dimodel {

    public function updateUser($request,$id){
        
             $user = $this->table("users")->find($id);
             
             $user->col["name"] = $request->input("name");
             
             $user->col["surname"] = $request->input("surname");
             
             $user->col["email"] = $request->input("email");
             
             $user->col["gender"] = $request->input("gender");
             
             $user->col["phone"] = $request->input("phone");
             
             if($request->input("image")){
                 
                  $user->col["image"] = $request->input("image");
                  
                  echo "resim var";
             }
             
             return $user->update_();

    }
    
    public function updateUserPassword($request,$id){
        
             $user = $this->table("users")->find($id);
             
            if($user->col["password"] == Hash::generate("password",$request->input("eskisifre"))){
                 
                   $user->col["password"] = Hash::generate("password",$request->input("yenisifre"));
                   
                   return $user->update_();
                   
             }else{
                 return false;
             }  
        
    }
    
    
    
    
}
