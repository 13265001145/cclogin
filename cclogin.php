<?php
namespace cclogin;

use cclogin\model\model;
use cclogin\packet\cclogin_config;
use cclogin\packet\loginsign;
use cclogin\packet\pwdlogin;
use cclogin\packet\wxlogin;
use cclogin\packet\sc_wxlogin;
use cclogin\packet\mobilelogin;
use cclogin\packet\error_info;

require "cc_autoload.php";

class cclogin{

    private $login_obj = null;

    protected $config ;

    private $account;
    private $secret;

    //private $err_obj;

    function __construct($login_type='',$login_data=array()){

        //自动加载
        spl_autoload_register('cclogin_autoload');

        //获取配置
        $this->config = cclogin_config::config();

        $this->account = isset($login_data['account'])?$login_data['account']:'';
        $this->secret = isset($login_data['secret'])?$login_data['secret']:'';


        switch ($login_type) {
            case 'pwd':
                

                $this->login_obj = new pwdlogin($this->account,$this->secret);//账号、密码
                break;
            case 'mobile':
                
              
                $this->login_obj = new mobilelogin($this->account,$this->secret);//手机号码、验证码
                break;
            case 'wx':
                

                $appid = $this->config['wx_appid'];
                $appsecret = $this->config['wx_appsecret'];

                $this->login_obj = new wxlogin($appid,$appsecret);
                break;
               
            case 'sc_wx'://sweep code wxlogin 微信扫码登录
                
                
                $appid = $this->config['kf_appid'];
                $appsecret = $this->config['kf_appsecret'];
                
                $this->login_obj = new sc_wxlogin($appid,$appsecret);

                break;
            default:
                die(json_encode(error_info::returns(1)));
                break;
        }


        return $this->login_obj;

        //$this->login();

    }


    public function login(){

        //登录前生命周期，主要是例如微信的授权获取用户信息、或者手机验证码验证等
                                                               //或者是登录前的一些限制，比如说用户禁用之类的
        $res_login_before = $this->login_before();
        if($res_login_before['err_code']!=0)return $res_login_before;

        
        //登录中，主要是验证登录的凭据是否正确，自动注册，设置登录标志的记录
        $res_logining = $this->logining();
        if($res_logining['err_code']!=0)return $res_logining;


        //登录后，主要是登录后的其他业务逻辑
        $res_login_after = $this->login_after();
        if($res_login_after['err_code']!=0)return $res_login_after;
        
        return error_info::returns(0,'',array(
                                            'login_token'=>model::get_data('loginSign_data','login_token'),
                                            'private_key'=>model::get_data('loginSign_data','private_key'),
                                        )
                                );

    }

    private function login_before(){

        //公共

        //公共 end

        return $this->login_obj->login_before();
    }

    private function logining(){

        //外面这里可以放一些公共的业务逻辑

        return $this->login_obj->logining();

    }

    private function login_after(){

        //公共

        //公共 end

        return $this->login_obj->login_after();

    }

     /**
     * 和下面的get_member_id功能一样，只是返回方式不同
     */
    public function check_login(){

        //自动加载,该方法无需实例化调用，所以手动注册
        spl_autoload_register('cclogin_autoload');

        $token = self::getheaders('Token');
        if(empty($token)){
            return error_info::returns(-1);//请求头中没有token
        }


        model::set_data(array(
            'login_token'=>array('login_token'=>$token),
        ));
        $res = loginsign::get_loginSign();
        if(empty($res)){
            return error_info::returns(-2);//数据库或缓存中没有token对应的数据
        }


        if( $res['end_time'] < time() ){
            return error_info::returns(-3);//登录状态过期
        }

        spl_autoload_unregister('cclogin_autoload');

        return error_info::returns(0,'',$res['member_id']);

    }

    /**
     * 和上面的check_login功能一样，只是返回方式不同
     */
    public function get_member_id($ONLYCHECK = FALSE){

        //自动加载,该方法无需实例化调用，所以手动注册
        spl_autoload_register('cclogin_autoload');

        $token = self::getheaders('Token');
        if(empty($token)){
            return $ONLYCHECK ? -1 : die(json_encode(array('err_code'=>-1,'msg'=>'找不到登录状态1')));//请求头中没有token
        }


        model::set_data(array(
            'login_token'=>array('login_token'=>$token),
        ));
        $res = loginsign::get_loginSign();
        if(empty($res)){
            return $ONLYCHECK ? -2 : die(json_encode(array('err_code'=>-2,'msg'=>'找不到登录状态2')));//数据库或缓存中没有token对应的数据
        }


        if( $res['end_time'] < time() ){
            return $ONLYCHECK ? -3 : die(json_encode(array('err_code'=>-3,'msg'=>'登录状态过期')));//登录状态过期
        }

        spl_autoload_unregister('cclogin_autoload');

        return $res['member_id'];
    }
    
    
    //获取请求头
    private static function getheaders( $fieldname = '' ) 
    {
       $headers = [];
       foreach ($_SERVER as $name => $value) {
          if (substr($name, 0, 5) == 'HTTP_') {
              $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
          }
      }
      if( $fieldname ){
          return ($headers[$fieldname] ?? false);
      }
      else{
          return $headers;
      }
    }



    public function send_captcha(){
        
    }


    public function check_captcha(){
        
    }


    public function wx_authorize(){
        
    }





    public function test(){
        
        model::test($data);
        
    }




    function __destruct(){
        spl_autoload_unregister('cclogin_autoload');
    }









}
?>