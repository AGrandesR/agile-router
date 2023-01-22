<?php

namespace Agrandesr\http;


use Agrandesr\extra\Errors;

class Response {
    //region SYSTEM response functions
    private array $systemError; //Only one error. When we throw a System Error we stop the code execution
    private array $systemWarnings=[];

    private int $headerCode=200;
    private bool $status=true;

    public function throwSystemError(int $code, string $description, string $file, int $line, int $httpCode=500) : void {
        $this->headerCode=$httpCode;
        $this->status=false;
        $this->systemError=[
            "code"=>$code,
            "description"=>$description,
            "file"=>$file,
            "line"=>$line
        ];
        echo json_encode($this->systemError);
        Errors::stop();
    }

    public function addSystemWarning(int $code, string $description, string $file, int $line){
        $this->systemWarnings[]=[
            "code"=>$code,
            "description"=>$description,
            "file"=>$file,
            "line"=>$line
        ];
    }
    //endregion
}