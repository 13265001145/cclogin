<?php
namespace cclogin\packet;
use cclogin\model\model;
use cclogin\packet\cclogin_config;
use cclogin\packet\loginsign;
use cclogin\packet\token;


class pwdlogin implements interface_cclogin{

    private $if_auto_register = 1;

    private $username = '';
    private $password = '';

    public function __construct($username='',$password=''){
        //echo "this is password login";

        $this->username = $username;
        $this->password = $password;
    }


    public function login_before(){

        //检查账号密码正确性
        $res = model::check_register_by_pwd($this->username,$this->password); 

        if($res===false)return error_info::returns(6); //账号密码错误

        model::set_data(array(
            'member_id'=>$res
        ));

        return error_info::returns(0); 

    }


    public function logining(){

        model::set_data(array(
            'loginSign_data'=>array(
                'member_id'=>model::get_data('member_id'),
                'login_type'=>'pwd',
                'login_token'=>token::new_token(),
                'private_key'=>token::new_token(),
            ),
        ));


        loginsign::del_loginSign();
        loginsign::set_loginSign();


        return error_info::returns(0); 
    }


    public function login_after(){

        return error_info::returns(0); 

    }




}


?>