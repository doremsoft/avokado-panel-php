<?php
namespace Dipa\Sys;

class Cryptor {

    protected $method = 'aes-128-ctr'; 
    private $key;

    protected function iv_bytes() {
        return openssl_cipher_iv_length($this->method);
    }

    public function __construct($key = FALSE, $method = FALSE) {
        if (!$key) {
            $key = php_uname(); 
        }
        if (ctype_print($key)) {
        
            $this->key = openssl_digest($key, 'SHA256', TRUE);
        } else {
            $this->key = $key;
        }
        if ($method) {
            if (in_array(strtolower($method), openssl_get_cipher_methods())) {
                $this->method = $method;
            } else {
                die(__METHOD__ . ": unrecognised cipher method: {$method}");
            }
        }
    }

    public function encrypt($data) {
        $iv = openssl_random_pseudo_bytes($this->iv_bytes());
        return bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
    }


    public function decrypt($data) {
        $iv_strlen = 2 * $this->iv_bytes();
        if (preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
            list(, $iv, $crypted_string) = $regs;
            if (ctype_xdigit($iv) && strlen($iv) % 2 == 0) {
                return openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
            }
        }
        return FALSE; 
    }
    
    public function fileEncrypt($file_full_path , $add_php_return = false , $name = "" , $key_id = 0){

        $file = $file_full_path;
        $file_contents = file_get_contents($file);
        $fh = fopen($file, "w");
        $file_contents =$this->encrypt($file_contents);
        if($add_php_return){
            
            $date = date("Y-m-d H:i:s");
            
            $file_contents = "<?php return [ \"keyid\" =>\"$key_id\",\"date\" => \"$date\" ,\"name\" =>\"".$name."\" ,  \"query\" => \"".$file_contents."\"]; ?>";
        }
        fwrite($fh, $file_contents);
        fclose($fh);
    }

}
