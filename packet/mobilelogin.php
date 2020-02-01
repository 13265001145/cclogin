<?php
namespace cclogin\packet;
use cclogin\model\model;
use cclogin\packet\cclogin_config;
use cclogin\packet\loginsign;
use cclogin\packet\token;
use \ccredis\ccredis as R;

class mobilelogin{

    private $if_auto_register = 1;

    private $mobile = '';
    private $captcha = '';


    public function __construct($mobile='',$captcha=''){
        //echo "this is mobile login";

        $this->mobile = $mobile;
        $this->captcha = $captcha;

        $this->if_auto_register = cclogin_config::config('if_auto_register');
    }

    public function login_before(){

        //只有手机，没验证码，转到发验证码逻辑
        if($this->captcha=='sendcaptcha' && !empty($this->mobile)){
            return $this->send_captcha($this->mobile);
        }

        //都有，登录
        return error_info::returns(0); 
    }


    public function logining(){

        
          //验证码验证
          $check_captcha_res = $this->check_captcha($this->mobile,$this->captcha);
          if($check_captcha_res!==true)return $check_captcha_res;
        


          //插入数据源
          $member_id = model::check_register_by_mobile($this->mobile);
     
          if( !$member_id ){

            $register_data = array(
                'nickname'=>'手机注册用户'.$this->mobile,
                cclogin_config::config('mobile_account_field')=>$this->mobile,
            );

            $member_id = $this->register($register_data);
           
          }
         
       

        if( isset($member_id) && !empty($member_id) ){
            model::set_data(array(
                'loginSign_data'=>array(
                    'member_id'=>$member_id,
                    'login_type'=>'mobile',
                    'login_token'=>token::new_token(),
                    'private_key'=>token::new_token(),
                ),
            ));
    
            loginsign::del_loginSign();
            loginsign::set_loginSign();
    
            return error_info::returns(0); 
        }

        return error_info::returns(3); 

        

    }

    public function login_after(){

        return error_info::returns(0); 

    }


    private function send_captcha($phone){

         //验证发送次数
         if(R::get('snscaptcha_times_'.$phone) >= cclogin_config::config('sendCaptcha_limit_times') )return error_info::returns(7);//今天验证码发送次数已满;


        //生成验证码
        $captcha = rand(1000,9999);

        //模板参数
        $params = array($captcha);

        // 单发短信
        try {
            $ssender = new \Qcloud\Sms\SmsSingleSender(config('txsns')['appid'],config('txsns')['appkey']);
            $result = $ssender->cc_send(0, "86", $phone,config('txsns')['tpl_id'],$params);
            $rsp = json_decode($result,true);

            if( $rsp["result"] == 0 && $rsp["errmsg"] == 'OK' ){

                session($phone.'_captcha_value', $captcha , 'captcha');
                session($phone.'_captcha_startTime', time(), 'captcha');

                //今天发送次数
                $this->remarkSendTimes($phone);

                return error_info::returns(10);//验证码发送成功

            }
            else{
                return error_info::returns(8,$rsp['errmsg']);//接口错误
            }
            
        } catch(\Exception $e) {
            return error_info::returns(9);//验证码发送失败
        }

    }

    //检查验证码
    private function check_captcha($phone,$captcha){

        $value = session($phone.'_captcha_value', '', 'captcha');
        $startTime = session($phone.'_captcha_startTime', '', 'captcha');

        if( empty($value) || empty($startTime) )return error_info::returns(11);//验证码不存在

        $nowTime = time();

        if( $startTime + cclogin_config::config('captcha_expire') < $nowTime ){
            return error_info::returns(12);//验证码过期
        }

        if($captcha != $value){
            return error_info::returns(13);//验证码错误
        }

        return true;

    }

    //记录发送次数
    private function remarkSendTimes($phone){

        $key = 'snscaptcha_times_'.$phone;

        //检查key是否存在
        if(R::exists($key)){
            return R::incr($key);
        }
        else{
            $expire = strtotime(date('Y-m-d', time()+60*60*24)) - time();
            return R::set($key,1,$expire);
        }
        
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