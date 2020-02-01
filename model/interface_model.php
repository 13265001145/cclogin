<?php
namespace cclogin\model;

interface interface_model{

    //通过开放平台openid检测用户是否注册
    public function check_register_by_kf_openid($kf_openid);

    
    public function member_register($data = array());

}


?>