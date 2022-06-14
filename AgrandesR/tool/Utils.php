<?php

namespace AgrandesR\tool;

use SimpleXMLElement;

class Utils {
    //@CREDIT: https://stackoverflow.com/questions/26964136/how-do-i-convert-json-to-xml
    static function arrayToXML($array, $xml = false){

        if($xml === false){
            $xml = new SimpleXMLElement('<ALL/>');
        }
    
        foreach($array as $key => $value){
            if(is_array($value)){
                self::arrayToXML($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }
    
        return $xml->asXML();
    }

    //@CREDIT: https://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php + my touch
    static function jsonable(mixed &$string) : bool { //@TODO: I don't like the name, if someone have a better idea : /
        $string=json_decode($string,true);
        return json_last_error() === JSON_ERROR_NONE;
    }

    static function isArrayRouteSetInArray(string $arrayRoute, array $array, &$value=null) : bool {
        $paths = explode('.',$arrayRoute);
        $isSet=true;
        foreach($paths as $path){
            if(isset($array[$path])){
                $value=$array[$path];
                array_shift($paths);
                if(!empty($paths)) return self::isArrayRouteSetInArray(implode('.',$paths),$array[$path]);
            }else return false;
        }
        return $isSet;
    }
}