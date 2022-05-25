<?php
namespace AgrandesR;

use AgrandesR\Response;

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
        $show=false;
        $showData=false;
        $replace=[];
        if(strpos($name, 'AndShowData')) {
            $name = str_replace('AndShowData','',$name);
            if(!in_array($name,['show','showData']));
            $showData=true;
        }
        if(strpos($name, 'AndShow')) {
            $name = str_replace('AndShow','',$name);
            if(!in_array($name,['show','showData']));
            $show=true;
        }
        if(strpos($name, 'AndDie')) {
            print_r($die);
            $name = str_replace('AndDie','',$name);
            $die=true;
        }

        if(method_exists($GLOBALS['X-AGRANDESR-RESPONSE'], $name))
            $functionResponse=call_user_func_array([$GLOBALS['X-AGRANDESR-RESPONSE'],$name], $arguments);
        else
            $GLOBALS['X-AGRANDESR-RESPONSE']->addWarning("The function '$name' doesn't exist in Response method : ( ");

        if($showData) $GLOBALS['X-AGRANDESR-RESPONSE']->showData();
        elseif($show) $GLOBALS['X-AGRANDESR-RESPONSE']->show();

        if($die) die;
    
        return $functionResponse??null;
    }
}