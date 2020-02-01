<?php
namespace cclogin\packet;
use cclogin\model\model;
use cclogin\packet\cclogin_config;
use cclogin\packet\loginsign;
use cclogin\packet\token;

class wxlogin implements interface_cclogin{

    private $if_auto_register = 1;//是否自动注册，1是0否，如果不自动注册，则返回该用户没注册

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
        return error_info::returns(0); 
    }


    public function logining(){

        $authorize = new WXauthorize($this->appid,$this->appsecret);
        
        //拿code
        if(!isset($_GET['code'])){   
            $redirect_uri = cclogin_config::config('wx_getCode_redirect');
            $authorize->wx_get_code($redirect_uri,'snsapi_base','snsapi_base');
        }

        //获取openid查数据库,有这个人直接返回openid,没有这个人再详细授权，获取用户信息注册
        else if( (isset($_GET['scope']) && $_GET['scope'] == 'snsapi_base') || (isset($_GET['state']) && $_GET['state'] == 'snsapi_base') ){//好像微信现在不返回scope了，所以要考state判断

            $wx_return_data = $authorize->snsapi_base();


            //是否注册
            $member_id = $this->if_register($wx_return_data['openid']);

            

            //没有用户信息，跳详细授权;(注册存在用户信息，直接登录)
            if( $member_id === false ){

                $authorize->wx_get_code(cclogin_config::config('wx_getCode_redirect'),'snsapi_userinfo','snsapi_userinfo');
            
            }

        }

        //详细授权的跳转返回处理
        else if( (isset($_GET['scope']) && $_GET['scope'] == 'snsapi_userinfo') || (isset($_GET['state']) && $_GET['state'] == 'snsapi_userinfo') ){

            //自动注册
            if($this->if_auto_register == 1){

                //详细授权
                $wx_return_data = $authorize->snsapi_userinfo();
                
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

    


        if( isset($member_id) && !empty($member_id) ){
            model::set_data(array(
                'loginSign_data'=>array(
                    'member_id'=>$member_id,
                    'access_token'=>$wx_return_data['access_token'],
                    'refresh_token'=>$wx_return_data['refresh_token'],
                    'login_type'=>'wx',
                    'login_token'=>token::new_token(),
                    'private_key'=>token::new_token(),
                ),
            ));
    
            loginsign::del_loginSign();
            loginsign::set_loginSign();
    
            return error_info::returns(0); 
        }

        return error_info::returns(5); 

        

    }

    public function login_after(){

        if(cclogin_config::config('after_login_auto_jump')==1){
            $url = cclogin_config::config('white_out_url'); //$white_url.'?login_token='.$token;
            $url .= '?login_token='.model::get_data('loginSign_data','login_token');
            header("Location:".$url);
        }

    }


    //判断用户是否注册
    protected function if_register($openid = ''){

        return model::check_register_by_openid($openid);

    }

    
    protected function register($data = array()){

        $member_id = model::member_register($data);

        if($member_id===false){
            return error_info::returns(3);
        }
        return $member_id;

    }



    


}


?>