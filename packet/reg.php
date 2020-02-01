<?php
namespace cclogin\packet;
/*
    类注册器
*/
class reg{

   protected static $objs;

   static function set($alias,$obj){
        self::$objs[$alias] = $obj;
   }

   public function get(){
        return self::$objs[$alias];
   }

   function _unset(){
        unset(self::$objs[$alias]);
   }

}


?>