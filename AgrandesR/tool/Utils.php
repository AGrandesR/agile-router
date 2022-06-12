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
}