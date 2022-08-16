<?php

namespace AgrandesR\tool;

use SimpleXMLElement;
use AgrandesR\tool\MimeTypes;
use Exception;

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

    /**
     * @credit https://www.php.net/manual/en/class.simplexmlelement.php
     * @param SimpleXMLElement $xml
     * @return array
    **/
    function xmlToArray(SimpleXMLElement $xml): array {
        $parser = function (SimpleXMLElement $xml, array $collection = []) use (&$parser) {
            $nodes = $xml->children();
            $attributes = $xml->attributes();

            if (0 !== count($attributes)) {
                foreach ($attributes as $attrName => $attrValue) {
                    $collection['attributes'][$attrName] = strval($attrValue);
                }
            }

            if (0 === $nodes->count()) {
                $collection['value'] = strval($xml);
                return $collection;
            }

            foreach ($nodes as $nodeName => $nodeValue) {
                if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
                    $collection[$nodeName] = $parser($nodeValue);
                    continue;
                }

                $collection[$nodeName][] = $parser($nodeValue);
            }

            return $collection;
        };

        return [
            $xml->getName() => $parser($xml)
        ];
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

    static function getMimeTypeFromExtension(string $extension) : string {
        $mimeTypes=MimeTypes::getList();
        foreach($mimeTypes as $mimeType=>$extensions) {
            if(in_array($extension,$extensions)) return $mimeType;
        }
        return 'text/plain' ; //default text
    }
}

//

