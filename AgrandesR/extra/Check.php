<?php

namespace AgrandesR\extra;

use AgrandesR\GlobalRequest;
use AgrandesR\tool\Token;
use AgrandesR\tool\Utils;

class Check {
    static function parameters(array $requiredParameters=[]) : array {
        //$requiredParameters = $this->route_data['req_parameters'];
        if(empty($requiredParameters)) return [];
        $idx=0;
        $requiredErrors=[];
        foreach($requiredParameters as $parameterData){
            if(is_string($parameterData)) $parameterData=['name'=>$parameterData];
            if(!isset($parameterData['name'])) $requiredErrors='Need to declare "name" of the parameter to can check: ' . json_encode($parameterData);
            if(!isset($_GET[$parameterData['name']]))  $requiredErrors[]= 'Forgot required parameter ' . $parameterData['name'];
            if(isset($parameterData['regex']) && !empty($parameterData['regex']) && preg_match($parameterData['regex'],$_GET[$parameterData['name']])) $requiredErrors[$parameterData['name']]='Value not valid with the regex';
            //In the router PRO we put into Request method the value with the correct type ;)
            if(empty($requiredErrors)) GlobalRequest::saveRequiredParamater($parameterData['name']);
        }
        return $requiredErrors;
    }
    static function headers(array $requiredHeaders=[]) : array {
        //$requiredHeaders = $this->route_data['req_headers'];
        if(empty($requiredHeaders)) return [];
        $idx=0;
        $requiredErrors=[];
        foreach($requiredHeaders as $headerData){
            if(is_string($headerData)) $headerData=['name'=>$headerData];
            if(!isset($headerData['name'])) $requiredErrors[]='Need to declare "name" of the header to can check: ' . json_encode($headerData);
            if(!isset($_SERVER['HTTP_' . strtoupper($headerData['name'])])) $requiredErrors[]='Forgot required header: '.$headerData['name'];
            if(isset($headerData['regex']) && !empty($headerData['regex']) &&  preg_match($headerData['regex'], $_SERVER['HTTP_' . strtoupper($headerData['name'])])) $requiredErrors[]='Value '.$headerData['name']. ' not valid with the regex';
            //In the router PRO we put into Request method the value with the correct type ;)
            if(empty($requiredErrors)) GlobalRequest::saveRequiredHeader($headerData['name']);
        }
        return $requiredErrors;
    }
    static function body(array $requiredBody) : array {
        if(empty($requiredBody)) return [];
        if(in_array(strtoupper($_SERVER['REQUEST_METHOD']),['GET'])) {
            return ["You can't use body parameters with a method " . $_SERVER['REQUEST_METHOD']];
        }
        $requiredErrors=[];
        $rawBody = file_get_contents('php://input');

        if(!empty($_POST)){
            foreach($requiredBody as $bodyData){
                if(is_string($bodyData)) $bodyData=['name'=>$bodyData];
                if(!isset($bodyData['name'])) $requiredErrors[]='Need to declare "name" of the body element to can check: ' . json_encode($bodyData);
                if(!isset($_POST[$bodyData['name']])) $requiredErrors[]='Forgot required body in form: '.$bodyData['name'];
                if(isset($bodyData['regex']) && !empty($bodyData['regex']) &&  preg_match($bodyData['regex'], $_POST[$bodyData['name']])) $requiredErrors[]='Value '.$bodyData['name']. ' not valid with the regex';
                //In the router PRO we put into Request method the value with the correct type ;)
                if(empty($requiredErrors)) GlobalRequest::saveRequiredBody($bodyData['name'],$_POST[$bodyData['name']]);
            }
        } else {
            $rawBody = file_get_contents('php://input');
        if(Utils::jsonable($rawBody)) {
                foreach($requiredBody as $bodyData){
                    if(is_string($bodyData)) $bodyData=['name'=>$bodyData];
                    if(!isset($bodyData['name'])) $requiredErrors[]='Need to declare "name" of the body element to can check: ' . json_encode($bodyData);
                    if(!Utils::isArrayRouteSetInArray($bodyData['name'],$rawBody, $value)) $requiredErrors[]='Forgot required body in json: '.$bodyData['name'];
                    //if(isset($bodyData['regex']) && !empty($bodyData['regex']) &&  preg_match($bodyData['regex'], $_POST[$bodyData['name']])) $requiredErrors[]='Value '.$bodyData['name']. ' not valid with the regex';
                    //In the router PRO we put into Request method the value with the correct type ;)
                    if(empty($requiredErrors)) GlobalRequest::saveRequiredBody($bodyData['name'], $value);
                }
            }else{
                foreach($requiredBody as $bodyData){
                    if(is_string($bodyData)) $bodyData=['name'=>$bodyData];
                    $requiredErrors[]='Forgot required body: '.$bodyData['name'];
                }
            }

        }
        return $requiredErrors;
    }
    static function token(array $reqTokenConfig) : array {
        if(empty($reqTokenConfig)) return [];
        
        $requiredErrors=[];
        if(!isset($reqTokenConfig['token']) || empty($reqTokenConfig['token'])) return ['Token parameter not defined in req_token'];
        $realToken=StringRouter::parseValues($reqTokenConfig['token']);
        $decodedToken=Token::decode($realToken,$reqTokenConfig['flag']??'',false);
        if(empty($decodedToken)) return ["The token is not valid"];
        GlobalRequest::setTokenData($decodedToken);
        echo "adasd";die;
        return $requiredErrors;
    }
    //region CHECK-SLUGS @NON-USED : (
    static function checkVariableSlugs(string $segment) : array {
        $res=[];
        preg_match_all('/\{\{\w{1,}\}\}/',$segment, $dirtyResult);
        $res = array_map('self::removeMacroVarsForSlugs',$dirtyResult[0]); //@CREDIT: https://stackoverflow.com/questions/35010429/php-array-map-with-static-method-of-object
        return $res;
    }
    static function removeMacroVarsForSlugs(string $dirtyMacro) : string {
        return preg_replace('/(\{\{|\}\})/','',$dirtyMacro);
    }
    //endregion
}