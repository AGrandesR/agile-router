<?php

namespace Agrandesr\extra;

use Agrandesr\GlobalData;
use Agrandesr\GlobalRequest;
use Agrandesr\GlobalResponse;
use Agrandesr\tool\Utils;
use PhpParser\Node\Stmt\Global_;

use function PHPUnit\Framework\returnSelf;

class StringRouter {
    static function parseValues(string $sentence) : string {
        //TODO: Avoid that can parse a parsed value example if "?value?" contains "Something^ extrange^" we will have a problem

        //region PARAMETERS ?value?
        $sentence = preg_replace_callback(
            '/\?(\w|\-){1,}\?/',
            function ($matches) {
                $value = $_GET[trim($matches[0],'? ')] ?? null;
                if(!isset($value)) return $matches[0];
                //if(!GlobalRequest::isRequiredParameter(trim($matches[0],'? '))) GlobalResponse::addWarning("GET parameter '".trim($matches[0],'? ')."' used but not is required. We recomend to make required for this route");
                return $value;
            },
            $sentence
        );
        //endregion

        //region HEADERS ^value^
        $sentence = preg_replace_callback(
            '/\^(\w|\-){1,}\^/',
            function ($matches) {
                $headerName=trim($matches[0],'^ ');
                $headerName=str_replace('-','_',$headerName);
                $value = $_SERVER['HTTP_'.strtoupper($headerName)] ?? null;
                if(!isset($value))  return $matches[0];
                //if(!GlobalRequest::isRequiredHeader($headerName)) GlobalResponse::addWarning("GET header '".$headerName."' used but not is required. We recomend to make required for this route");
                return $value;
            },
            $sentence
        );
        //endregion

        //region SLUGS {}
        $sentence = preg_replace_callback(
            '/\{(\w|\-){1,}\}/',
            function ($matches) {
                $slugName=trim($matches[0],'{} ');
                $value=GlobalRequest::getSlug($slugName);
                if(!isset($value))  return $matches[0];;
                return $value;
            },
            $sentence
        );
        //endregion

        //region BODY $value.value$
        $sentence = preg_replace_callback(
            '/\$(\w|\.){1,}\$/',
            function ($matches) {
                $bodyName=trim($matches[0],'$ ');
                $value = GlobalRequest::getRequiredBody($bodyName) ?? null;
                if(!isset($value)) return $matches[0];
                //if(!GlobalRequest::isRequiredBody($bodyName)) GlobalResponse::addWarning("GET body '".$bodyName."' used but not is required. We recomend to make required for this route");
                return $value;
            },
            $sentence
        );
        //endregion

        //region TOKEN #value#
        $sentence = preg_replace_callback(
            '/\#(\w|-){1,}\#/',
            function ($matches) {
                $tokenDataName=trim($matches[0],'# ');
                $tokenData = GlobalRequest::getTokenData($tokenDataName);
                if(!Utils::isArrayRouteSetInArray($tokenDataName,$tokenData,$value)) return $value;
                if(!isset($value))  return $matches[0];;
                return $value;
            },
            $sentence
        );
        //endregion

        //region GlobalData ||KEY.etc.etc||
        $sentence = preg_replace_callback(
            '/\|\|(\w|\.){1,}\|\|/',
            function ($matches) use ($sentence) {
                $globalDataName=trim($matches[0],'| ');
                $globalData=explode('.',$globalDataName);
                $mixedContent = GlobalData::get($globalData[0]);
                unset($mixedContent[0]);
                $globalDataName=implode('.',$mixedContent);

                if(!isset($mixedContent))  return $matches[0];
                if(isset($globalData[1]) && !Utils::isArrayRouteSetInArray($globalDataName,$mixedContent,$value)) $return=$value;
                $return = $mixedContent;
                if($sentence == $matches[0]) { //If is exactly the same we stop the function and replace $sentence by the new $solution
                    $sentence=$return;
                    return null;
                }
                if(!isset($mixedContent))  return $matches[0];
                if(isset($globalData[1]) && !Utils::isArrayRouteSetInArray($globalDataName,$mixedContent,$value)) return $value;
                return $mixedContent;
            },
            $sentence
        );
        //endregion
        return $sentence;
    }

    static function dataParseValues(mixed $dataToParse) : mixed{
        if(is_string($dataToParse)) {
            return StringRouter::parseValues($dataToParse);
        } else if(is_array($dataToParse)) {
            foreach ($dataToParse as $key => $value) {
                $dataToParse[$key]=StringRouter::dataParseValues($value);
            }
            return $dataToParse;
        } else return $dataToParse;
    }
}