<?php

class Encrypt extends ObjectModel
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public static function encrypt($secure_key, $data)
    {
        $crypt = Configuration::get("PS_FLUZ_CRY");
        $key = pack('H*', $secure_key);

        $iv_size = mcrypt_get_iv_size($crypt ,MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $ciphertext = mcrypt_encrypt($crypt, $key, $data, MCRYPT_MODE_CBC, $iv);
        $ciphertext = $iv . $ciphertext;
        $ciphertext_base64 = base64_encode($ciphertext);

        return $ciphertext_base64;
    }
    
    public static function decrypt($secure_key, $data)
    {
        $crypt = Configuration::get("PS_FLUZ_CRY");
        $key = pack('H*', $secure_key);
        $ciphertext_dec = base64_decode($data);
        
        $iv_size = mcrypt_get_iv_size($crypt, MCRYPT_MODE_CBC);
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        $ciphertext_dec = substr($ciphertext_dec, $iv_size);

        $plaintext_dec = mcrypt_decrypt($crypt, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        return $plaintext_dec;
    }
}
