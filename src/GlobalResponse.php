<?php
namespace Agrandesr;

use Agrandesr\http\Response;
use Error;

class GlobalResponse {
    static function getGlobalResponse() {
        //Check if global response is created, if not, then is created
        if(!isset($GLOBALS['X-AGRANDESR-RESPONSE'])){
            $GLOBALS['X-AGRANDESR-RESPONSE'] = new Response();
        }
    }

    /**  MIN PHP 5.3.0  */
    public static function __callStatic($name, $arguments)
    {
        if(!isset($GLOBALS['X-AGRANDESR-RESPONSE'])) self::getGlobalResponse();

        $die=false;

        if(method_exists($GLOBALS['X-AGRANDESR-RESPONSE'], $name))
            $functionResponse=call_user_func_array([$GLOBALS['X-AGRANDESR-RESPONSE'],$name], $arguments);
        /*else
            $GLOBALS['X-AGRANDESR-RESPONSE']->addWarning("The function '$name' doesn't exist in Response method : ( ");*/

        if($die) throw new Error("X-AGRANDESR-DIE", 1995);
    
        return $functionResponse??null;
    }
}