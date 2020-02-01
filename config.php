<?php
return array(

    //会员表id字段名
    'member_id_field'=>'id',

    //项目名称，用于redis作前缀或其他场景
    'project_name'=>'faensha',


    //账号密码登录方式配置
    'pwd_account_field'=>'username',//数据库用户账号字段名
    'pwd_secret_field'=>'password',//数据库用户密码字段名


    //手机验证码登录方式配置
    'mobile_account_field'=>'phone',//数据库手机号码字段名
    'captcha_expire'=>60*5,
    'sendCaptcha_limit_times'=>100,


    //微信授权登录配置
    'wx_account_field'=>'openid',//数据库openid字段名


    //微信公众平台信息
    'wx_appid'=>'wx2de89be1601f6663',
    'wx_appsecret'=>'6bea4177ae40dd0fd2be69341fed6c62',

    'wx_getCode_redirect'=>'http://www.h5-bus.com/api/login/login?login_type=wx',




    //微信开放平台信息
    'kf_appid'=>'wx98a86fc413ce6376',
    'kf_appsecret'=>'1282ba01f678e1cd6fa448c5c21ec193',


    'base_url'=>'',
    'this_file_url'=>'',//微信回调使用
    'white_out_url'=>'http://www.h5-bus.com/web/login/white_out_view',//微信授权出口空白页地址


    'if_auto_register'=>1,//是否自动注册，1是0否，如果不自动注册，则返回该用户没注册
    


    //登录标志存储方式
    'loginsign_type'=>'sql',//session,sql,redis


    //登录有效时长,单位秒
    'login_expire'=>1*24*3600,

    //微信登录后是否自动跳转,1自动跳0不自动
    'after_login_auto_jump'=>0,
    
);










?>