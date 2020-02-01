<?php
namespace cclogin\model;

use cclogin\packet\cclogin_config;
use cclogin\packet\token;
use ccredis\ccredis;

class redis {

    private $prefix;


    public function __construct(){

        //echo "this is redis";

        $this->prefix = cclogin_config::config('project_name').'_loginSign_';
        
    }



    public function set_loginSign($data = array()){

        $loginSign_data = array(
            'token'=>$data['login_token'],
            'member_id'=>$data['member_id'],
            'input_time'=>time(),
            'end_time'=>time()+cclogin_config::config('login_expire'),
            'type'=>$data['login_type'],
            'access_token'=>$data['access_token'],
            'refresh_token'=>$data['refresh_token'],
            'private_key'=>$data['private_key'],
        );


        //唉...为什么redis没有事务，这里手动事务
        $res1 = ccredis::set($this->prefix.'memberid_'.$data['member_id'],$data['login_token']);//member_id对应的登录标志
        ccredis::expire($this->prefix.'memberid_'.$data['member_id'],cclogin_config::config('login_expire'));
        if($res1!==true){
            return false;
        }

        $res2 = ccredis::hash_set($this->prefix.$data['login_token'],$loginSign_data);//登录标志
        ccredis::expire($this->prefix.$data['login_token'],cclogin_config::config('login_expire'));
        if($res2!==true){
            ccredis::del($this->prefix.'memberid_'.$data['member_id']);
            return false;
        }
        
        return true;

    }

    public function get_loginSign($data = array()){
        
        return ccredis::hash_get($this->prefix.$data['login_token']);
    
    }

    public function del_loginSign($data = array()){

        $login_token = ccredis::get($this->prefix.'memberid_'.$data['member_id']);

        ccredis::del($this->prefix.$login_token);//删除登录标志
        ccredis::del($this->prefix.'memberid_'.$data['member_id']);//删除会员id和登录标志的对应关系

        $res1 = ccredis::hash_get($this->prefix.$login_token);//登录标志
        $res2 = ccredis::get($this->prefix.'memberid_'.$data['member_id']);//会员id和登录标志的对应关系

        if(empty($res1) && $res2 === false ){//都获取不到就行了
            return true;
        }
        return false;

    }

 



}


?>