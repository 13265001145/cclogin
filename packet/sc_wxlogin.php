<?php
namespace cclogin\packet;
use cclogin\model\model;
use cclogin\packet\cclogin_config;
use cclogin\packet\loginsign;
use cclogin\packet\token;

class sc_wxlogin implements interface_cclogin{

    private $if_auto_register;//是否自动注册，1是0否，如果不自动注册，则返回该用户没注册

    protected $appid = '';
    protected $appsecret = '';

    /*
    public function __construct($openid='',$open_token=''){
        echo "this is weixin login";
    }
    */
    public function __construct($appid='',$appsecret=''){

        $this->appid = $appid;
        $this->appsecret = $appsecret;

        $this->if_auto_register = cclogin_config::config('if_auto_register');


    }

    public function login_before(){
        
    }


    public function logining(){

        $authorize = new WXauthorize($this->appid,$this->appsecret);

        //获取openid(扫码登录无分静默详细授权)
        $wx_return_data = $authorize->snsapi_userinfo();

        //是否注册
        $member_id = $this->if_register($wx_return_data['openid']);

        //没有注册
        if($member_id === false){

            if($this->if_auto_register == 1){

                //用户详细信息
                $wx_return_data_2 = $authorize->get_userinfo($wx_return_data['access_token'],$wx_return_data['openid']);

                $res_register = $this->register($wx_return_data_2);

                if(is_array($res_register)){
                    return $res_register;
                }

                $member_id = $res_register;
            }
            else{
                return error_info::returns(4);
            }
        }


        model::set_data(array(
            'loginSign_data'=>array(
                'member_id'=>$member_id,
                'access_token'=>$wx_return_data['access_token'],
                'refresh_token'=>$wx_return_data['refresh_token'],
                'login_type'=>'sc_wx',
                'login_token'=>token::new_token(),
                'private_key'=>token::new_token(),
            ),
        ));


        loginsign::del_loginSign();
        loginsign::set_loginSign();

        

        return error_info::returns(0);

    }

    public function login_after(){

        if(cclogin_config::config('after_login_auto_jump')==1){
            $url = cclogin_config::config('white_out_url');
            $url .= '?login_token='.model::get_data('loginSign_data','login_token');
            header("Location:".$url);
        }

    }


    //判断用户是否注册
    protected function if_register($kf_openid = ''){

        return model::check_register_by_kf_openid($kf_openid);

    }

    
    protected function register($data = array()){

        $data['kf_openid'] = $data['openid'];
        unset($data['openid']);

        $member_id = model::member_register($data);

        if($member_id===false){
            return error_info::returns(3);
        }
        return $member_id;

    }

    

//还有错误处理
//前台返回保存
//登录验证
   


}


?>