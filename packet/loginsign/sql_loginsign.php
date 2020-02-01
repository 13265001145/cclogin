<?php
/*
    登录标志类

    主要 设置、获取、删除、登录标志

    登录标志可以存放在session，redis，数据库

*/
namespace cclogin\packet\loginsign;

use cclogin\model\model;

class sql_loginsign{

   

    public function __construct(){
        echo "this is loginsign";
    }


    public function set_loginSign(){
        return model::set_loginSign();
    }

   
    public function get_loginSign(){
        return model::get_loginSign();
    }   


    public function del_loginSign(){
        return model::del_loginSign();
    }


}


?>