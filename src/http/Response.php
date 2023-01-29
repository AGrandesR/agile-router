<?php

namespace Agrandesr\http;


use Agrandesr\extra\Errors;
use Agrandesr\extra\StringRouter;
use Agrandesr\GlobalResponse;
use Error;
use Exception;

class Response {
    private array $systemError; //Only one error. When we throw a System Error we stop the code execution
    private array $systemWarnings=[];

    private bool $showAll=true;

    //region RESPONSE return
    private bool $status=true;
    private int $httpCode=200;
    private array $extraHeaders=[];

    private mixed $data;
    private array $meta;
    private array $code;
    //endregion

    //region RENDER functions
    public function render() : void {
        if (ob_get_level()) ob_end_clean();
        ob_start(function ($buffer) { return StringRouter::parseValues($buffer); });
        $response = [
            "success"=> $this->status
        ];
        if(isset($this->code)) $response['code']=$this->code;
        if(isset($this->data) && !empty($this->data)) $response['data']=$this->data;
        if(isset($this->meta) && !empty($this->meta)) $response['meta']=$this->meta;
        if(isset($this->systemError) && !empty($this->systemError)) $response['systemError']=$this->systemError;
        if(isset($this->systemWarnings) && !empty($this->systemWarnings)) $response['systemWarnings']=$this->systemWarnings;

        //region SET HEADERS
        http_response_code($this->httpCode);
        foreach ($this->extraHeaders as $key => $value) {
            header("$key: $value");
        }
        header('Content-Type: application/json');
        //endregion

        echo json_encode($this->showAll ? $response : $this->data);
    }

    public function unauthorized() : void {
        $this->httpCode=401;
        $this->status=false;
        $this->render();
        Errors::stop();
    }

    //endregion

    //region Data functions
    public function addData(mixed $data, string $key='') : void {
        if(!(is_array($data)||is_string($data))) throw new Exception('addData first parameter have to be array or string'); //In future we will use array|string and not mixed - but i want compatible with 7.4
        if(empty($this->data) && empty($key)) $this->data=$data;
        elseif(empty($key) && is_array($this->data)) $this->data[] = $data;
        elseif(empty($key) && is_string($this->data)) $this->data = [$this->data,$data];
        else {
            if(is_array($data) && is_array($this->data['key'])) $this->data[$key]=array_merge($this->data[$key],$data);
            elseif(is_string($data) && is_string($this->data['key'])) $this->data[$key]=[$this->data[$key],$data];
            elseif(is_array($data) && is_string($this->data['key'])) $this->data[$key]=array_merge([$this->data['key']],$data);
            elseif(is_string($data) && is_array($this->data['key'])) $this->data[$key]=array_merge($this->data['key'],[$data]);
        }
    }
    public function getData() : mixed {
        return $this->data;
    }

    public function setData(mixed $data) : void {
        $this->data = $data;
    }
    //endregion

    //region Errors functions
    public function throwSystemError(int $code, string $description, string $file, int $line, int $httpCode=500) : void {
        $this->addSystemError($code, $description, $file, $line, $httpCode=500);
        Errors::stop(); //Erros::stop will provocate the end of the program and the last render
    }

    public function addSystemError(int $code, string $description, string $file, int $line, int $httpCode=500) : void {
        $this->httpCode=$httpCode;
        $this->status=false;
        $this->systemError=[
            "code"=>$code,
            "description"=>$description,
            "file"=>$file,
            "line"=>$line
        ];
    }

    public function addSystemWarning(int $code, string $description, string $file, int $line){
        $this->systemWarnings[]=[
            "code"=>$code,
            "description"=>$description,
            "file"=>$file,
            "line"=>$line
        ];
    }
    public function addError(string $msg, int $httpCode=400) : void {
        $this->httpCode=$httpCode;
        $this->status=false;
        
        $this->meta['errors'][] = $msg;
    }
    public function addErrors(array $errors, $strict=false) : void {
        if(!$strict && empty($errors)) return;
        $this->status=false;
        $this->meta['errors'] = !isset($this->meta['errors']) ? $errors : array_merge($this->meta['errors'], $errors);
    }
    public function addWarning(string $msg) : void {
        $this->meta['warnings'][] = $msg;
    }
    public function addWarnings(array $warnings) : void {
        $this->meta['warnings'] = !isset($this->meta['warnings']) ? $warnings : array_merge($this->meta['warnings'], $warnings);
    }
    public function hasErrors() : bool {
        return !empty($this->meta['errors']);
    }
    public function getErrors() : array {
        return $this->meta['errors'] ?? [];
    }
    //endregion

    public function showAll() {
        $this->showAll=true;
    }

    public function showData() {
        $this->showAll=false;
    }
}