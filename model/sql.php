<?php
namespace cclogin\model;

use cclogin\packet\cclogin_config;
use cclogin\packet\token;
use think\db;

class sql {


    public function __construct(){

        //echo "this is sql";
        
    }


    //通过开放平台openid检测用户是否注册
    public function check_register_by_kf_openid($kf_openid){

        return db::name('member')
                //->fetchsql()
                ->where('kf_openid',$kf_openid)
                ->find();

    }

    //通过公众号openid检测用户是否注册
    public function check_register_by_openid($openid){

        return db::name('member')
                //->fetchsql()
                ->where('openid',$openid)
                ->find();

    }


    public function check_register_by_pwd($account,$pwd){

        return db::name('member')
                ->where(
                    array(
                        cclogin_config::config('pwd_account_field') => $account,
                        cclogin_config::config('pwd_secret_field') => $pwd,
                    )
                )
                ->find();

    }


    public function check_register_by_mobile($mobile){

        return db::name('member')
                ->where(
                    array(
                        cclogin_config::config('mobile_account_field') => $mobile,
                    )
                )
                ->find();

    }


    //会员注册
    /*
        demo:
        $data = array(
            '表名A'=>array(
                '表A字段A'=>'表字段A数据',
                '表A字段B'=>'表字段B数据',
            )
        )
    */
    public function member_register($data = array()){

        $default_data = array(
            'member'=>array(
                'nickname'=>'',
                'openid'=>'',
                'kf_openid'=>'',
                'unionid'=>'',
                'head'=>'',
                'password'=>'',
                'create_time'=>time(),
                'update_time'=>time(),
            )
        );

        $last_data = $default_data;
     
        $last_data['member'] = array_merge($last_data['member'],$data);

        //其实这里把数据库这个字段改一下也是阔以滴，但是暂时不动了
        if(isset($data['headimgurl']) && !empty($data['headimgurl'])){
            $last_data['member']['head'] = $data['headimgurl'];
        }
        

        unset($last_data['member']['headimgurl']);
        unset($last_data['member']['privilege']);

        // 启动事务
        Db::startTrans();
        try {

            $member_id = Db::name('member')->insertGetId($last_data['member']);

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();

            return false;
        }

        return $member_id;

    }




    public function set_loginSign($data = array()){
       
        return db::name('login_token')->insert(array(
            'token'=>$data['login_token'],
            'member_id'=>$data['member_id'],
            'input_time'=>time(),
            'end_time'=>time()+cclogin_config::config('login_expire'),
            'type'=>$data['login_type'],
            'access_token'=>$data['access_token'],
            'refresh_token'=>$data['refresh_token'],
            'private_key'=>$data['private_key'],
        ));

    }

    public function get_loginSign($data = array()){
        return db::name('login_token')->where('token',$data['login_token'])->find();
    }

    public function del_loginSign($data = array()){

        return db::name('login_token')->where('member_id',$data['member_id'])->delete();

    }

 



}


?>