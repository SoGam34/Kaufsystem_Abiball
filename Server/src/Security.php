<?php

class Security
{
   public function encrypt(string $data) : string {
      $crypt_key = base64_decode(Firstkey);
      $hash_key = base64_decode(Secondkey);    
   
      $method = "aes-256-cbc";    
      $iv_length = openssl_cipher_iv_length($method);
      $iv = openssl_random_pseudo_bytes($iv_length);

      $data_encrypted = openssl_encrypt($data, $method, $crypt_key, OPENSSL_RAW_DATA , $iv);    
      $hash_encrypted = hash_hmac('sha3-512', $data_encrypted, $hash_key, TRUE);

      $output = base64_encode($iv.$hash_encrypted.$data_encrypted);    
      return $output;        
   }

   public function decrypt(string $input) {
      $crypt_key = base64_decode(Firstkey);
      $hash_key = base64_decode(Secondkey);            
      $mix = base64_decode($input);

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

   public function PSW_is_safe($data) : bool 
   {
      settype($data, "string");
      
      if(strlen($data)<10)
      {         
         echo json_encode(["Status"=>"ERROR", "Message"=>"Das Passwort muss mindestens 10 Zeichen lang sein."]);
         exit;
      }

      settype($zahl, "bool");
      settype($buchstabe, "bool");

      $zahl = false;
      $buchstabe = false;

      for($i=0; $i<strlen($data); $i++)
      {
         switch($data[$i])
         {
            case "1":$zahl = true;break;
            case "2":$zahl = true;break;
            case "3":$zahl = true;break;
            case "4":$zahl = true;break;
            case "5":$zahl = true;break;
            case "6":$zahl = true;break;
            case "7":$zahl = true;break;
            case "8":$zahl = true;break;
            case "9":$zahl = true;break;
            case "0":$zahl = true;break;
            
            case "a": $buchstabe = true;break;
            case "b": $buchstabe = true;break;
            case "c": $buchstabe = true;break;
            case "d": $buchstabe = true;break;
            case "e": $buchstabe = true;break;
            case "f": $buchstabe = true;break;
            case "g": $buchstabe = true;break;
            case "h": $buchstabe = true;break;
            case "i": $buchstabe = true;break;
            case "j": $buchstabe = true;break;
            case "k": $buchstabe = true;break;
            case "l": $buchstabe = true;break;
            case "m": $buchstabe = true;break;
            case "n": $buchstabe = true;break;
            case "o": $buchstabe = true;break;
            case "p": $buchstabe = true;break;
            case "q": $buchstabe = true;break;
            case "r": $buchstabe = true;break;
            case "s": $buchstabe = true;break;
            case "t": $buchstabe = true;break;
            case "u": $buchstabe = true;break;
            case "v": $buchstabe = true;break;
            case "w": $buchstabe = true;break;
            case "x": $buchstabe = true;break;
            case "y": $buchstabe = true;break;
            case "z": $buchstabe = true;break;

            case "A": $buchstabe = true;break;
            case "B": $buchstabe = true;break;
            case "C": $buchstabe = true;break;
            case "D": $buchstabe = true;break;
            case "E": $buchstabe = true;break;
            case "F": $buchstabe = true;break;
            case "G": $buchstabe = true;break;
            case "H": $buchstabe = true;break;
            case "I": $buchstabe = true;break;
            case "J": $buchstabe = true;break;
            case "K": $buchstabe = true;break;
            case "L": $buchstabe = true;break;
            case "M": $buchstabe = true;break;
            case "N": $buchstabe = true;break;
            case "O": $buchstabe = true;break;
            case "P": $buchstabe = true;break;
            case "Q": $buchstabe = true;break;
            case "R": $buchstabe = true;break;
            case "S": $buchstabe = true;break;
            case "T": $buchstabe = true;break;
            case "U": $buchstabe = true;break;
            case "V": $buchstabe = true;break;
            case "W": $buchstabe = true;break;
            case "X": $buchstabe = true;break;
            case "Y": $buchstabe = true;break;
            case "Z": $buchstabe = true;break;

            case "ö": $buchstabe = true;break;
            case "ü": $buchstabe = true;break;
            case "ä": $buchstabe = true;break;

            case "Ö": $buchstabe = true;break;
            case "Ü": $buchstabe = true;break;
            case "Ä": $buchstabe = true;break;

            case "+": $buchstabe = true;break;
            case "-": $buchstabe = true;break;
            case "*": $buchstabe = true;break;
            case "/": $buchstabe = true;break;
         }
      }

      if(($buchstabe==true)&&($zahl==true))
      {
         return true;
      }

      else if($buchstabe)
      {
         echo json_encode(["Status" => "ERROR", "Message"=>"Es muss mindestens eine Zahl im Passwort enthalten sein."]);
         exit;
      }

      echo json_encode(["Status" => "ERROR", "Message"=>"Es muss mindestens ein Buchstabe im Passwort sein."]);
      exit;
          
   }

   public function EMail_is_safe($data) : bool 
   {
      settype($data, "string");

      if(filter_var($data, FILTER_VALIDATE_EMAIL)){
         return true;
      }

      echo json_encode(["Status" => "ERROR", "Message"=>"Die Email ist ungültig, bitte verwenden Sie gueltige Email-Adressen"]);
      exit;
   }

   public function check_id($data) : bool
   {
      if(settype($data, "int") == true)
      {
         return true;
      }

      return false;
   }
};