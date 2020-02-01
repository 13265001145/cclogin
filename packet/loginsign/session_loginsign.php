<?php
/*
    登录标志类

    主要 设置、获取、删除、登录标志

    登录标志可以存放在session，redis，数据库

*/
namespace cclogin\packet\loginsign;
use cclogin\packet\token;
class session_loginsign{

    public function __construct(){
        echo "this is loginsign";

        if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}//开启session
    }


    public function set_loginSign(){
        
        $_SESSION['loginsign'] = array(
            'loginsign'=>token::new_token(),
            'server'=>$_SERVER,
        );

        if(isset($_SESSION['loginsign']) && !empty($_SESSION['loginsign'])){
            return true;
        }
        return false;
    }

   
    public function get_loginSign(){

        if(isset($_SESSION['loginsign']) && !empty($_SESSION['loginsign'])){
            return $_SESSION['loginsign'];
        }
        return false;
        
    }


    public function del_loginSign(){
        $_SESSION['loginsign'] = null;
        return true;
    }


}


?>