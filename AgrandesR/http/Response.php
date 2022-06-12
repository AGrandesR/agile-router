<?php

namespace AgrandesR\http;

use Exception;
use Error;

class Response {
    private string $responseType = 'JSON'; //XML or JSON (¿More ideas?) HTML
    private array $allowedTypes = ['JSON','XML'];
    private string $language;
    private array $dictionary;
    
    //region RESPONSE-BODY properties
    private bool $status=true;
    private int $code;
    private string $msg;
    private /*array*/ $data=[];
    private array $meta=[];
    private array $errors=[];
    private array $warnings;
    private array $systemError;
    //endregion

    //region RESPONSE-HEADER properties
    private int $headerCode=200;
    private array $extraHeaders=[];
    //endregion

    //region CALLBACKS
    private $warningFunction;
    private $errorFunction;
    //endregion

    function __construct() {

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    ////S> RESPONSE OPERATIVE FUNCTIONS

    //region DATA FUNCTIONS
    public function addData($data, string $key='') : void {
        if(empty($key)) $this->data[] = $data;
        else $this->data[$key] = $data;
    }
    public function setData($data) : void {
        $this->data = $data;
    }
    public function setSystemError(int $code, string $description, string $file, int $line) : void {
        $this->headerCode=500;
        $this->status=false;
        $this->systemError=[
            "code"=>$code,
            "description"=>$description,
            "file"=>$file,
            "line"=>$line
        ];
    }
    public function setCatchedSystemError(mixed $error) : void {
        $this->setSystemError($error->getCode(),$error->getMessage(),$error->getFile(),$error->getLine());
    }
    public function addSystemWarning(int $code, string $description, string $file, int $line){
        $this->meta['systemWarnings']=[
            "code"=>$code,
            "description"=>$description,
            "file"=>$file,
            "line"=>$line
        ];
    }
    //endregion

    //region META FUNCTIONS
    public function addWarning(string $msg) : void {
        $this->meta['warnings'][] = $msg;
    }

    //You are going to need a dictionary to use this
    public function addWarningByCode() : bool {
        return false;
    }
    public function addWarningCallback($function) : void{
        if(method_exists($function,'call'))
            $this->warningFunction=$function;
    }
    
    public function addError(string $msg, int $headerCode=400) : void {
        $this->headerCode=$headerCode;
        $this->status=false;
        
        $this->meta['errors'][] = $msg;
    }

    public function addErrorByCode(int $code, int $headerCode, string $msg=null) : void {
        if(isset($msg)){
            $msg = str_replace('%msg%', 'AÑADIR AQUÍ EL VALOR DEL MAPA SI ES POSIBLE',$msg);
        }
        $this->addError($msg);
    }

    public function setErrors(array $errors, $strict=false) : void {
        if(!$strict && empty($errors)) return;
        $this->status=false;
        $this->meta['errors'] = !isset($this->meta['errors']) ? $errors : array_merge($this->meta['errors'], $errors);
    }

    public function hasErrors() : bool {
        return !empty($this->meta['errors']);
    }

    public function setPagination(int $actualPage, int $totalPage) : bool {
        return false;
    }
    //endregion
    
    //region RENDER FUNCTIONS
    public function show() : void {
        //$this->code = $this->code ?? ($this->code % 2 == 0 || $this->code==0);
        
        $response = [
            "success"=> $this->status /*?? ($this->code % 2 == 0 || $this->code==0)*/, //Code errors are odd
            //"code"=>$this->code??1,
            //"data"=>empty($this->data) || !isset($this->data)? null : $this->data,
            //"meta"=>empty($this->meta) || !isset($this->meta)? null : $this->meta,
        ];
        if(isset($this->code)) $response['code']=$this->code;
        if(isset($this->data) && !empty($this->data)) $response['data']=$this->data;
        if(isset($this->meta) && !empty($this->meta)) $response['meta']=$this->meta;
        if(isset($this->msg) && !empty($this->msg)) $response['msg']=$this->msg;
        if(isset($this->systemError)) $response['systemError']=$this->systemError;

        foreach ($this->extraHeaders as $key => $value) {
            header("$key: $value");
        }
        switch ($this->responseType){
            case 'JSON':
                //region SET HEADERS
                http_response_code($this->headerCode);
                header('Content-Type: application/json');
                //endregion
                
                //region PRINT BODY
                //endregion
                echo json_encode($response);
                break;
            case 'XML':
                //region SET HEADERS
                http_response_code($this->headerCode);
                header('Content-Type: application/json');
                //endregion
                break;
        }
    }
    
    public function showData() : void {
        switch ($this->responseType){
            case 'JSON':
                //region SET HEADERS
                http_response_code($this->headerCode ?? 200);
                header('Content-Type: application/json');
                foreach ($this->extraHeaders as $key => $value) {
                    header("$key: $value");
                }
                //endregion
                
                //region PRINT BODY
                //endregion
                echo json_encode($this->data);
                break;
        }
    }
    //endregion

    ////E> RESPONSE OPERATIVE FUNCTIONS
    /////////////////////////////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    ////S> SETTERS & GETTERS
    public function setResponseType(string $type) : bool {
        if(in_array(strtoupper($type),$this->allowedTypes)){
            $this->responseType=strtoupper($type);
            return true;
        }
        $this->addWarning("Try to set a $type response, but this response type is not allowed");
        return false;
    }
    
    public function setStatus(bool $status) : void {
        
    }
    
    public function setDictionary(string $path, bool $external=false) : bool {
        if($external) {
            try {
                $this->dictionary = json_decode(file_get_contents($path));
            } catch(Error|Exception $e){
                $this->addWarning('Dictionary for message responses not found in the external URL');
            }
        } else {
    
        }
        return false;
    }
    ////E> SETTERS & GETTERS
    /////////////////////////////////////////////////////////////////////////////////////////////////////
}

class DictionaryResponse {
    static function send(int $code, array $data=[], array $meta_data=[], string $lng='es') : void {
        

        $response_template = [
            "success"=>($code % 2 == 0 || $code==0), //Code errors are odd
            "code"=>$code,
        ];

        //Check if we have a message
        $dictionaryPath="dictionary\\";
        $fileName="msg.$lng.json";
        $msg=false;
        if(file_exists($dictionaryPath.$fileName)) {
            $map = json_decode(file_get_contents("dictionary\\msg.$lng.json"),true);
            if (isset($map[$code])) $msg=$map[$code];
        }
        if(!$msg && file_exists($dictionaryPath.'msg.en.json')){
            $map = json_decode(file_get_contents("dictionary\\msg.en.json"),true);
            if (isset($map[$code])) $msg=$map[$code];
        }
        if($msg) $response_template['msg']=$msg;

        if(isset($data)) $response_template['data']=$data;
        if(isset($meta_data)) $response_template['meta']=$meta_data;

        echo json_encode($response_template);
        die;


        echo json_encode([
            "success"=>($code % 2 == 0 || $code==0), //Code errors are odd
            "code"=>$code,
            "msg"=>$map[$code],
            "data"=>$data,
            "meta"=>$meta_data
        ]);
    }
}