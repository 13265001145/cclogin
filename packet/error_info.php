<?php
namespace cclogin\packet;
class error_info{


    public function __construct(){
        echo "this is error_info";
    }


    public function returns($err_code='0',$msg='',$data=array()){

        $err_msg = array(
            '-1'=>'找不到登录态1',
            '-2'=>'找不到登录态2',
            '-3'=>'登录状态过期',

            '0'=>'ok',
            '1'=>'登录方式参数错误',
            '2'=>'账号密码错误',
            '3'=>'注册失败,请检查数据规范性',
            '4'=>'用户未注册',
            '5'=>'微信授权登录出错',
            '6'=>'账号密码错误',


            //手机登录
            '7'=>'今天验证码发送次数已满',
            '8'=>'',//sns接口错误
            '9'=>'验证码发送失败',
            '10'=>'验证码发送成功',

            '11'=>'验证码不存在',
            '12'=>'验证码过期',
            '13'=>'验证码错误',
           


        );

        $ret = array(
            'err_code'=>$err_code,
            'msg'=>!empty($msg)?$msg:$err_msg[$err_code],
            'data'=>$data
        );

        return $ret;
    }





}


?>