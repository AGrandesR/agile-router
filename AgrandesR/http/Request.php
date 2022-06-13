<?php

namespace AgrandesR\http;

use AgrandesR\http\Response;
use AgrandesR\GlobalResponse;

class Request {
    private array $requiredParameters=[];
    private array $requiredHeaders=[];

    private string $link;
    private string $address;
    private string $subject; //$subject = https://localhost::6000
    private string $predicate; //$predicate =  /register/?algo=2#1
    private string $protocol;
    private string $host;
    private string $port;
    private bool $ssl;

    protected array $slugs;

    function __construct() {
        $this->protocol=strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
        $this->ssl=$this->protocol=='https://';
        $this->host=$_SERVER['HTTP_HOST'];
        $this->port=$_SERVER['SERVER_PORT'];
        //$this->domain = strpos($this->host,':') ? preg_replace("patron","",$this->host) : $this->host;
        $this->subject=$this->protocol . $this->host;
        $this->predicate=trim($_SERVER['REQUEST_URI'],' /');
        $this->path=strpos($this->predicate,'?')==false ? $this->predicate : preg_replace('/\?.{0,}$/','',$this->predicate);
        $this->link=$this->subject . $this->predicate;
        $this->slugs=[];
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    ////S> REQUEST OPERATIVE FUNCTIONS
    public function saveRequiredParamater(string $paramName) : void {
        if(!isset($_GET[$paramName])) GlobalResponse::addWarning('You try to save a variable that doesn\'t exist in ' . debug_backtrace()[2]['file'] . ' line ' . debug_backtrace()[2]['194']);
        $this->requiredParameters[$paramName]=$_GET[$paramName];
    }
    public function saveRequiredHeader(string $headerName) : void {
        $this->requiredHeaders[$headerName]=$_SERVER['HTTP_' . strtoupper($headerName)];
    }
    public function saveRequiredBody(string $bodyName, $value) : void {
        $this->requiredBody[$bodyName]=$value;
    }
    public function isRequiredBody(string $bodyName) : bool {
        return in_array($bodyName,array_keys($this->requiredBody));
    }
    public function getRequiredBody(string $bodyName, bool $dieIfFails=false) : mixed {
        if(!isset($this->requiredBody)) {
            if($dieIfFails) GlobalResponse::addErrorAndShowAndDie("Try to access a required body that doesn't exist in route config: $bodyName");
            else GlobalResponse::addError("Try to access a required body that doesn't exist in route config: $bodyName"); //This is an error because in theory a required body have to throw an error before in the code
            return null;
        }
        if(in_array($bodyName,array_keys($this->requiredBody))) return $this->requiredBody[$bodyName];
        if($dieIfFails) GlobalResponse::addErrorAndShowAndDie("The body param $bodyName that you try to get doesn't exist.");
        else GlobalResponse::addError("Check if $bodyName is in the req_body parameter to be sure that you can use");
        return null;
    }
    
    public function isRequiredParameter(string $paramName) : bool {
        return in_array($paramName,array_keys($this->requiredParameters));
    }
    public function isrequiredHeader(string $headerName) : bool {
        return in_array($headerName,array_keys($this->requiredHeaders));
    }
    public function getHeader(string $headerName, $errorIfFails=false) : mixed {
        if(in_array($headerName,$this->requiredHeaders)) return $this->requiredHeaders[$headerName];
        if(isset($_GET[$headerName])){

            return $_GET[$headerName];
        }
        if($errorIfFails) GlobalResponse::addError('The header that you try to ');

        return [in_array($headerName,array_keys($this->requiredParameters))];
    }
    public function getRequiredParameter(string $paramName) : mixed {
        return in_array($paramName,array_keys($this->requiredParameters));
    }
    public function getSlug(string $slugName) : mixed {
        return $this->slugs[$slugName] ?? null;
    }

    public function addSlug(string $key, string $value) : void {
        $this->slugs[$key]=$value;
    }
    ////E> REQUEST OPERATIVE FUNCTIONS
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    ////S> SETTERS & GETTERS
    public function getHost() : string{
        return $this->host;
    }
    public function getPort() : string{
        return $this->port;
    }
    public function getProtocol() : string{
        return $this->protocol;
    }
    public function getLink() : string {
        return $this->link;
    }
    public function getSubject() : string {
        return $this->subject;
    }
    public function getPredicate() : string {
        return $this->predicate;
    }
    public function getAddress() : string {
        return $this->address;
    }
    public function isSSL() : bool {
        return $this->ssl;
    }
    public function getPath() : string {
        return $this->path;
    }
    ////E> SETTERS & GETTERS
    /////////////////////////////////////////////////////////////////////////////////////////////////////
}