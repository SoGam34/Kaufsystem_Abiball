<?php

class Security
{
     public function encrypt(string $data) : string {
        $crypt_key = Firstkey;
        $hash_key = Secondkey;    
    
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);

        $data_encrypted = openssl_encrypt($data, $method, $crypt_key, OPENSSL_RAW_DATA , $iv);    
        $hash_encrypted = hash_hmac('sha3-512', $data_encrypted, $hash_key, TRUE);

        $output = $iv.$hash_encrypted.$data_encrypted;    
        return $output;        
     }

     public function decrypt(string $data) {
        $crypt_key = Firstkey;
        $hash_key = Secondkey;            
        $mix = $data;

        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);

        $iv = substr($mix, 0, $iv_length);
        $hash_encrypted = substr($mix, $iv_length, 64);
        $data_encrypted = substr($mix, $iv_length+64);

        $data = openssl_decrypt($data_encrypted, $method, $crypt_key, OPENSSL_RAW_DATA, $iv);
        $hash_encrypted_new = hash_hmac('sha3-512', $data_encrypted, $hash_key, TRUE);

        if (hash_equals($hash_encrypted, $hash_encrypted_new))
        return $data;

        return false;
      }
};

new Security();