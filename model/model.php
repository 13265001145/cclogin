<?php
namespace cclogin\model;
use cclogin\packet\cclogin_config;

class model implements interface_model{

    protected static $db;

    /*
        set_loginSign、get_loginSign、del_loginSign由配置文件决定
        这里采取的混合记录模式，各自配置更加灵活
    */
    protected static $type = array(
        'check_register_by_kf_openid'=>'sql',
        'member_register'=>'sql',
        'check_register_by_openid'=>'sql',
        'check_register_by_pwd'=>'sql',
        'check_register_by_mobile'=>'sql',
    );

    protected static $data = array();

    private function __construct(){

        echo "this is model";

    }


    public static function getInstance($type = 'sql'){

        if(self::$db[$type]){
            return self::$db[$type];
        }
        else{
        
            $class = false !== strpos($type, '\\') ? $type : '\\cclogin\\model\\' . $type;

            self::$db[$type] = new $class();
            return self::$db[$type];
            
        }

    }

    public static function set_data($data){
        self::$data = array_merge(self::$data,$data);
        return self::$data;
    }

    public static function get_data($one = '',$two=''){
        if(!empty($one) && !empty($two)){
            return self::$data[$one][$two];
        }
        else if(!empty($one)){
            return self::$data[$one];
        }
        else{
            return self::$data;
        }
    }

    public static function del_data($one = ''){
        if(empty($one)){
            return false;
        }
        else{
            unset(self::$data[$one]);
            return true;
        }
    }

   


    public function member_register($data = array()){
        
        return self::getInstance(self::$type[__FUNCTION__])->member_register($data);

    }


    public function set_loginSign(){

        $res = self::getInstance(cclogin_config::config('loginsign_type'))->set_loginSign(self::$data['loginSign_data']);

        if(cclogin_config::config('loginsign_type')=='sql'){
            if($res==1)return true;
            return false;
        }
        
        return $res;
    }

    public function get_loginSign(){
        return self::getInstance(cclogin_config::config('loginsign_type'))->get_loginSign(self::$data['login_token']);
    }

    public function del_loginSign(){
        return self::getInstance(cclogin_config::config('loginsign_type'))->del_loginSign(self::$data['loginSign_data']);
    }



    public function check_register_by_kf_openid($kf_openid = ''){

        $res = self::getInstance(self::$type[__FUNCTION__])->check_register_by_kf_openid($kf_openid);

        if(!empty($res)){
            return $res[cclogin_config::config('member_id_field')];//返回用户id
        }
        return false;

    }


    public function check_register_by_openid($openid = ''){

        $res = self::getInstance(self::$type[__FUNCTION__])->check_register_by_openid($openid);

        if(!empty($res)){
            return $res[cclogin_config::config('member_id_field')];//返回用户id
        }
        return false;

    }


    //通过账号密码检测用户是否注册
    public function check_register_by_pwd($account,$pwd){

        $res = self::getInstance(self::$type[__FUNCTION__])->check_register_by_pwd($account,$pwd);

        if(!empty($res)){
            return $res[cclogin_config::config('member_id_field')];//返回用户id
        }
        return false;

    }


    public function check_register_by_mobile($mobile){

        $res = self::getInstance(self::$type[__FUNCTION__])->check_register_by_mobile($mobile);
;
        if(!empty($res)){
            return $res[cclogin_config::config('member_id_field')];//返回用户id
        }
        return false;

    }


    public function test(){
        $res = self::getInstance('redis')->get_loginSign();
        dump($res);
    }



    

}


?>