<?php
/*
    登录标志类

    主要 设置、获取、删除、登录标志

    登录标志可以存放在session，redis，数据库

*/
namespace cclogin\packet;
use cclogin\packet\cclogin_config;

class loginsign{

   

    public function __construct(){
        echo "this is loginsign";
    }


    public function set_loginSign(){
        return ('cclogin\\packet\\loginsign\\'.cclogin_config::config('loginsign_type').'_loginsign')::set_loginSign();
    }

   
    public function get_loginSign(){
        return ('cclogin\\packet\\loginsign\\'.cclogin_config::config('loginsign_type').'_loginsign')::get_loginSign();
    }


    public function del_loginSign(){
        return ('cclogin\\packet\\loginsign\\'.cclogin_config::config('loginsign_type').'_loginsign')::del_loginSign();
    }


}


?>