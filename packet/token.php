<?php
namespace cclogin\packet;
class token{

   

    public function __construct(){
        echo "this is token";
    }



    //登录时生成token
    public function new_token($length = 24){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    //验证token
    public function check_token(){

    }



}


?>