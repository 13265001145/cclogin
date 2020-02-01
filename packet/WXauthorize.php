<?php
namespace cclogin\packet;
use cclogin\packet\cclogin_config;

class WXauthorize{

    protected $config ;

    protected $appid = '';
    protected $appsecret = '';

   
    //注意网页授权(公众平台)和扫码授权(开放平台)后面的操作是一样的，只是获取code的方式不同，但是公众平台和开放平台的appid和appsecret是不同的
    public function __construct($appid = '',$appsecret = ''){
        //echo "this is WXauthorize<br/>";

        $this->config = cclogin_config::config();

        $this->appid = $appid;
        $this->appsecret = $appsecret;

    }



    //授权第一步，拿code;如果是扫码授权则直接进入第二步
    function wx_get_code($redirect_uri='',$scope='snsapi_userinfo',$state = 'state'){
        
        $redirect_uri = urlencode(trim($redirect_uri));
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->config['wx_appid'].'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
        header("Location:".$url);
        
    }

    //静默授权
    function snsapi_base(){
        
        $wx_return_data = $this->wxjudge(file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$_GET['code'].'&grant_type=authorization_code'));

        return $wx_return_data;
    }


    //详细授权
    function snsapi_userinfo(){

        $wx_return_data = $this->wxjudge(file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$_GET['code'].'&grant_type=authorization_code'));
        return $wx_return_data;
    }

    //获取用户详细信息
    function get_userinfo($access_token = '',$openid = ''){

        //用户详细信息
        $wx_return_data_2 = $this->wxjudge(file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN'));
        return $wx_return_data_2;
    }


    //更新access_token
    function update_access_token($refresh_token=''){

    }


    function wxjudge($data)
    {
        if(empty($data)) die('wxjudge()函数传值不能等于空');
        $data = json_decode($data,TRUE);
        if(isset($data['errcode']) and $data['errcode'] != 0)
        {
            $info = 'errcode:'.$data['errcode'].'&nbsp;errmsg:'.$data['errmsg'].'&nbsp;';
            unset($data);
            die($info);
        }
        return $data;
    }




}


?>