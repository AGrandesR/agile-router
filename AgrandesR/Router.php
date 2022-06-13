<?php
namespace AgrandesR;

//region HTTP libraries
use AgrandesR\GlobalResponse;
use AgrandesR\GlobalRequest;
//endregion

//region Extra options tools
use AgrandesR\Documentation;
use AgrandesR\tool\Token;
use AgrandesR\tool\Utils;
use AgrandesR\extra\Errors;
use AgrandesR\extra\Check;
//endregion

// use AgrandesR\Options\ExtraFiles;

use Exception;
use Error;

//Version: 1.0
class Router {
    //Request data
    protected string $req_uri;
    protected string $req_method;
    protected array $req_sections;
    //Route data
    protected string $route_path;
    protected string $route_method;
    protected array $route_req_parameters;
    protected array $route_opt_parameters;
    protected array $route_data;
    //Route Map data
    protected $routes;
    protected $constants;

    //AGR options
    protected bool $checkParams = true;
    protected bool $extraFiles = true;
    protected bool $fileNameIsPath = true;

    protected $validMethods=['GET','POST','PUT','PATCH','DELETE','COPY','HEAD','OPTIONS','LINK','UNLINK','PURGE','LOCK','UNLOCK','PROPFIND','VIEW'];

    function __construct(bool $frameworkErrors=true,string $routePath = 'routes.json', string $constantsPath='routeConstants.json') {
        try{
        if($frameworkErrors) Errors::setHandler(); //We rewritte the php warnings to include in the response
            $this->req_uri=GlobalRequest::getPath()??'/';
            $this->req_sections=explode('/',$this->req_uri);
            $this->req_method=$_SERVER['REQUEST_METHOD'];
            
            //region READ the json files
            //region READ ROUTES json
            
            //endregion
            //endregion
            
            $map = json_decode(file_get_contents($routePath),true);
            //$this->routes = $map['routes']; //OLD
            $this->routes = $map;
            $this->constants = isset($map['constants'])?$map['constants']:[];
        } catch(Error | Exception $e){
            GlobalResponse::setCatchedSystemErrorAndShowAndDie($e);
        }
    }
    
    public function run() : void {
        try {
            if($this->extraFiles) Options\Extrafiles::addExtraFiles($this,$this->fileNameIsPath);
            print_r($this->routes);die;
            $isFound=false;
            //region FIND PATH .- In this step we want to find the actual path and save the data
            foreach($this->routes as $path => $pathData){
                if($this->isPathEqualToRouterPath($this->req_uri,$path)){
                    $this->route_path=$path;
                    
                    $this->route_req_parameters=$pathData['ext']['req_parameters'] ?? [];
                    $this->route_opt_parameters=$pathData['ext']['opt_parameters'] ?? [];
    
                    foreach ($pathData as $method => $methodData) {
                        if($method==$this->req_method && in_array($method,$this->validMethods)){
                            if(isset($methodData['parameters'])){
                                $this->route_req_parameters=array_merge($this->route_req_parameters, $methodData['req_parameters']);
                                $this->route_opt_parameters=array_merge($this->route_req_parameters, $methodData['opt_parameters']);
                            }
                            $isFound=true;
                            $this->route_data=$methodData;
                            break 2;
                        } else {
                            
                        }
                    }
                } 
            }
            //endregion

            if($isFound){
                //region 1.-CHECKERS - First step, we validate all the parameters required
                    //We evaluate Parameters
                    $err=Check::parameters($this->route_data['req_parameters'] ?? []);
                    //We evalute headers and merge of errors
                    $err=array_merge(Check::headers($this->route_data['req_headers'] ?? [] ),$err);
                    //We evaluate body and merge errors
                    $err=array_merge(Check::body($this->route_data['req_body'] ?? []),$err);
                    //Error if not checked all
                    if(!empty($err)) $this->errorMessage($err);
                //endregion

                //region 1.2 - TOKEN CHECK
                    $tokenErrors=[];
                    Check::token($this->route_data['req_token']??[],$tokenErrors);
                    if(!empty($tokenErrors)){
                        GlobalResponse::addErrorAndDie($tokenErrors,401);
                    }
                    if(!empty($tokenErrors)) $this->errorMessage($tokenErrors);
                //endregion

                //region 2. -MODELS - We load the models that we need to make more checks

                //endregion

                //region 3. -CONDITIONS - We check if different conditions 

                //endregion

                //region 4. -RENDER - If all is OK we can render!
                if(empty($err)) $this->render();
                else ($this->errorMessage($err));
                //endregion
            } else {
                $this->pageNotFound();
            }
        } catch(Error | Exception $e){
            GlobalResponse::setCatchedSystemErrorAndShowAndDie($e);
        }
    }

    protected function render() : void {
        //print_r($this->route_data);die;
        if(isset($this->route_data['render'])) {
            $type = $this->route_data['render']['type'];
            $content = isset($this->route_data['render']['content'])?$this->route_data['render']['content'] : null;
            switch($type){
                case "json":
                    //header('Content-Type: application/json');
                    //if (is_array($content)) $content=json_encode($content, JSON_PRETTY_PRINT);
                    if(is_array($content)) $content=json_encode($content);
                    $content=$this->parseStringRouterValues($content);
                    if(!Utils::jsonable($content)) GlobalResponse::addWarning("You don't add a valid json in render content. Nothing showed.");
                    GlobalResponse::setData($content);
                    isset($this->route_data['render']['showOnlyData']) && $this->route_data['render']['showOnlyData']===true ? GlobalResponse::showDataAndDie() : GlobalResponse::showAndDie();
                    break;
                case "string":
                    echo $content;
                    break;
                case "class":
                    $path = $content['path'] . '\\' . $content['name'];
                    $func = $content['function'];
                    $class= new $path();
                    $class->$func();
                    break;
                case "sql":
                    $DB = new DBtool($content['flag'] ?? '');
                    $sql = $this->parseStringRouterValues($content['sql']);
                    if(GlobalResponse::hasErrors()) GlobalResponse::showAndDie();
                    $response=$DB->query($sql);
                    if(!$response) GlobalResponse::showAndDie();
                    GlobalResponse::setData($response);
                    GlobalResponse::showAndDie();
                    break;
                case "doc":
                case "docs":
                    //We need to create a doc with all the routes and subroutes, etc and send to showDocumentation
                    $routeMap = json_decode(file_get_contents('routes.json'),true);
                    $this->showDocumentation($routeMap);
                    

            }
        } else {
            throw new Exception("Not render method for this path", 1);
        }
        //Load * headers
        
    }

    public function setRoutes(array $newRoutes) : bool {
        if(true) $this->routes=$newRoutes;
        else return false;
        return true;
    }

    public function addPathRoutes(string $path, array $newRoutes, bool $fileNameIsPath=true) : bool {
        $check=Count($this->routes);

        foreach($newRoutes as $key=>$value) {
            if($fileNameIsPath) $newkey = stripslashes(str_replace('.json','',$path) . ($key ? ("/". $key):''));
            //echo"-$newkey";die;
            // echo $newkey . "\n";
            $this->routes[$newkey ?? $key] = $value;
            //unset($arr[$oldkey]);
        }
        // die;

        return Count($this->routes)>$check;
    }

    public function isPathEqualToRouterPath(string $reqUri, string $routerPath) : bool {
        if(strpos($routerPath,'{')!=false && strpos($routerPath,'{')!=false){
            $uriArray=explode('/',$reqUri);
            $pathArray=explode('/',$routerPath);
            $match=false;
            foreach($pathArray as $idx=>$path){
                if(!isset($uriArray[$idx])) break;
                if(strpos($path,'{')!==false && strpos($path,'{')!==false) {
                    $match=true;
                    GlobalRequest::addSlug(trim($path," {}"),$uriArray[$idx]);
                    continue;
                }
                if($uriArray[$idx]==$path) $match=true;
                else {
                    $match=false;
                    break;
                }
            }
            return $match;
        } else return $routerPath==$reqUri;
    }

    public function extraFiles(bool $extra=null, bool $fileNameIsPath=true) {
        if(!isset($extra)) return $this->extraFiles;
        $this->extraFiles=$extra;
        $this->fileNameIsPath=$fileNameIsPath;
    }

    ////////////////////////////////////////////////////////
    // S> EXTENSIBLE FUNCTIONS
    protected function pageNotFound() {
        http_response_code(404);
    }
    protected function showDocumentation(array $routeMap) {
        //In that place you can overwritte the standard model of documentation for your own style
        $doc = new Documentation($routeMap);
        
        $doc->render();
    }
    protected function errorMessage(array $errorData) {
        GlobalResponse::setErrorsAndShowAndDie($errorData);
    }


    private function isSetInArray(string $arrayRoute, array $array, &$value=null) : bool {
        $paths = explode('.',$arrayRoute);
        $isSet=true;
        foreach($paths as $path){
            if(isset($array[$path])){
                $value=$array[$path];
                array_shift($paths);
                if(!empty($paths)) return $this->isSetInArray(implode('.',$paths),$array[$path]);
            }else return false;
        }
        return $isSet;
    }
    // E> EXTENSIBLE FUNCTIONS
    ////////////////////////////////////////////////////////

    //@PRIVATE!!!
    public function getURI() : string {
        $uri=$_SERVER['REQUEST_URI'];
        $uri = trim($uri,'/');
        $paramsSymbolPosition = strpos($uri, '?', 0);
        if($paramsSymbolPosition>0){
            $uri=substr( $uri, 0, $paramsSymbolPosition);
        }
        echo "test". $uri;die;
        return $uri;
    }

    

    private function parseStringRouterValues(string $sentence) : string {
        //region PARAMETERS ?value?
        $sentence = preg_replace_callback(
            '/\?\w{1,15}\?/',
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
            '/\?\w{1,15}\?/',
            function ($matches) {
                $value = $_GET[trim($matches[0],'? ')] ?? null;
                if(!isset($value)) return;
                return $value;
            },
            $sentence
        );
        //endregion
        $sentence = preg_replace_callback(
            '/\^\w{1,15}\^/',
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
            '/\{\w{1,15}\}/',
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
            '/\$(\w|\.){1,30}\$/',
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

        //region MODEL $model.id$

        //endregion

        return $sentence;
    }

    // private function pregReplaceWithRegex(string $sentence, string $regex, string $trim) : string {
    //     return preg_replace_callback(
    //         $regex,
    //         function ($matches) {
    //             $value = $_GET[trim($matches[0],$trim)] ?? null;
    //             if(!isset($value)) return;
    //             return $value;
    //         },
    //         $sentence
    //     );
    // }

    private function testForParseStringRouterValues(){
        $_GET['test']=12;
        $_GET['bob']="Bob";
        $line = 'SELECT * FROM user WHERE id=?test? and name=?bob?';
        $line = preg_replace_callback(
            '/\?\w{1,15}\?/',
            function ($matches) {
                $value = $_GET[trim($matches[0],'? ')] ?? null;
                if(!isset($value)) return;
                return $value;
            },
            $line
        );
        echo $line;
    }

    
}