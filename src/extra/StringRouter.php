<?php

namespace Agrandesr\extra;

use Agrandesr\GlobalRequest;
use Agrandesr\GlobalResponse;
use Agrandesr\tool\Utils;

class StringRouter {
    static function parseValues(string $sentence) : string {
        //region PARAMETERS ?value?
        $sentence = preg_replace_callback(
            '/\?(\w|\-){1,}\?/',
            function ($matches) {
                $value = $_GET[trim($matches[0],'? ')] ?? null;
                if(!isset($value))  return $matches[0];
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

        //region MODEL ||model.id||
        /*
        $sentence = preg_replace_callback(
            '/||\w{1,}||/',
            function ($matches) {
                $tokenDataName=trim($matches[0],'| ');
                $tokenData = GlobalRequest::getSavedData($tokenDataName);
                if(!Utils::isArrayRouteSetInArray($tokenDataName,$tokenData,$value)) return $value;
                if(!isset($value))  return $matches[0];;
                return $value;
            },
            $sentence
        );
        */
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