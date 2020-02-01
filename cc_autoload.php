<?php


function cclogin_autoload($class){

    $url_arr = array(
        __DIR__.'\\model\\'.$class.'.php',
        __DIR__.'\\packet\\'.$class.'.php',
        ROOT_PATH.'\\'.$class.'.php',
        ROOT_PATH.'thinkphp\\library\\'.$class.'.php',
        __DIR__.'\\packet\\loginsign\\'.$class.'.php',
        EXTEND_PATH.'ccredis\\'.$class.'.php',
    );

    foreach ($url_arr as $k => $v) {
//dump($v);
//dump(is_file($v));
        if(is_file($v)){
            require $v;
            break;
        }

    }
    
}








?>