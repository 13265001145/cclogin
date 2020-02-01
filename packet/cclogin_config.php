<?php
namespace cclogin\packet;

class cclogin_config{

    protected static $config;

    public function __construct(){

        echo "this is cclogin_config";

    }


    public static function config($one='',$two=''){

        if(empty(self::$config)){
            self::$config=include __DIR__.'/../config.php';
        }

        if(!empty($one) && !empty($two)){
            return self::$config[$one][$two];
        }
        else if(!empty($one)){
            return self::$config[$one];
        }
        return self::$config;

    }


}


?>