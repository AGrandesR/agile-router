<?php

namespace AgrandesR\extra;

use AgrandesR\GlobalRequest;
use AgrandesR\GlobalResponse;
use AgrandesR\tool\Utils;

class StringRouter {
    static function parseValues(string $sentence) : string {
        //region PARAMETERS ?value?
        $sentence = preg_replace_callback(
            '/\?\w{1,}\?/',
            function ($matches) {
                $value = $_GET[trim($matches[0],'? ')] ?? null;
                if(!isset($value)) return;
                if(!GlobalRequest::isRequiredParameter(trim($matches[0],'? '))) GlobalResponse::addWarning("GET parameter '".trim($matches[0],'? ')."' used but not is required. We recomend to make required for this route");
                return $value;
            },
            $sentence
        );
        //endregion

        //region HEADERS ^value^
        $sentence = preg_replace_callback(
            '/\?\w{1,}\?/',
            function ($matches) {
                $value = $_GET[trim($matches[0],'? ')] ?? null;
                if(!isset($value)) return;
                return $value;
            },
            $sentence
        );
        //endregion
        $sentence = preg_replace_callback(
            '/\^\w{1,}\^/',
            function ($matches) {
                $headerName=trim($matches[0],'^ ');
                $value = $_SERVER['HTTP_'.strtoupper($headerName)] ?? null;
                if(!isset($value)) return;
                if(!GlobalRequest::isRequiredHeader($headerName)) GlobalResponse::addWarning("GET header '".$headerName."' used but not is required. We recomend to make required for this route");
                return $value;
            },
            $sentence
        );
        //region SLUGS {}
        $sentence = preg_replace_callback(
            '/\{\w{1,}\}/',
            function ($matches) {
                $slugName=trim($matches[0],'{} ');
                $value=GlobalRequest::getSlug($slugName);
                if(!isset($value)) return;
                return $value;
            },
            $sentence
        );
        //endregion

        //region BODY **value.value**
        $sentence = preg_replace_callback(
            '/\$(\w|\.){1,}\$/',
            function ($matches) {
                $bodyName=trim($matches[0],'$ ');
                $value = GlobalRequest::getRequiredBody($bodyName) ?? null;
                if(!isset($value)) return;
                if(!GlobalRequest::isRequiredBody($bodyName)) GlobalResponse::addWarning("GET body '".$bodyName."' used but not is required. We recomend to make required for this route");
                return $value;
            },
            $sentence
        );
        //endregion

        //region HEADERS #value#
        $sentence = preg_replace_callback(
            '/\#\w{1,}\#/',
            function ($matches) {
                $tokenDataName=trim($matches[0],'# ');
                $tokenData = GlobalRequest::getTokenData($tokenDataName);
                if(!Utils::isArrayRouteSetInArray($tokenDataName,$tokenData,$value)) return $value;
                if(!isset($value)) return;
                return $value;
            },
            $sentence
        );
        //endregion

        //region MODEL $model.id$

        //endregion

        return $sentence;
    }
}