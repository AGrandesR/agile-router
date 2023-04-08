<?php
namespace Agrandesr;

use Agrandesr\http\Request;
use Agrandesr\GlobalResponse;

use Exception;

class GlobalRequest {
    static function getGlobalRequest() {
        //Check if global response is created, if not, then is created
        if(!isset($GLOBALS['X-AGRANDESR-REQUEST'])){
            $GLOBALS['X-AGRANDESR-REQUEST'] = new Request();
        }
    }

    /**  MIN PHP 5.3.0  */
    public static function __callStatic($name, $arguments)
    {
        if(!isset($GLOBALS['X-AGRANDESR-REQUEST'])) self::getGlobalRequest();

        if($name=='init') return; //init is the function to be sure that GlobalRequest is created, don't have any other use

        if(method_exists($GLOBALS['X-AGRANDESR-REQUEST'], $name))
            $functionResponse=call_user_func_array([$GLOBALS['X-AGRANDESR-REQUEST'],$name], $arguments);
        else
            throw new Exception("The function '$name' doesn't exist in Request method : ( ", 1);
        
        return $functionResponse ?? null;
    }
}