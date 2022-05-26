<?php
namespace AgrandesR;

//region HTTP libraries
use AgrandesR\GlobalResponse;
use AgrandesR\GlobalRequest;
//endregion

//region Extra options tools
use AgrandesR\Documentation;
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

    protected $validMethods=['GET','POST','PUT','PATCH','DELETE','COPY','HEAD','OPTIONS','LINK','UNLINK','PURGE','LOCK','UNLOCK','PROPFIND','VIEW'];

    function __construct(string $routePath = 'routes.json', string $constantsPath='routeConstants.json') {
        $this->req_uri=$this->getURI();
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
    }

    public function run() : void {
        if($this->extraFiles) Options\Extrafiles::addExtraFiles($this);
        try {
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
                    $err=$this->checkParameters($this->route_data['req_parameters'] ?? []);
                    //We evalute headers and merge of errors
                    $err=array_merge( $this->checkHeaders($this->route_data['req_headers'] ?? [] ),$err);
                    //We evaluate body and merge errors
                    $err=array_merge( $this->checkBody($this->route_data['req_body'] ?? []),$err);
                    //Error if not checked all
                    if(!empty($err)) $this->errorMessage($err);

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
            echo $e->getMessage();
            die;
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
                    $content=json_decode($content,true);
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

    public function addPathRoutes(string $path, array $newRoutes) : bool {
        $check=Count($this->routes);

        foreach($newRoutes as $key=>$value) {
            
            $newkey = stripslashes(str_replace('.json','',$path) ."/". $key);
            // echo $newkey . "\n";
            $this->routes[$newkey] = $value;
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

    public function extraFiles(bool $extra=null) {
        if(!isset($extra)) return $this->extra;
        $this->extra=$extra;
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

    protected function checkParameters(array $requiredParameters=[]) : array {
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

    protected function checkHeaders(array $requiredHeaders=[]) : array {
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
    protected function checkBody(array $requiredBody) : array {
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
        if($this->jsonable($rawBody)) {
                foreach($requiredBody as $bodyData){
                    if(is_string($bodyData)) $bodyData=['name'=>$bodyData];
                    if(!isset($bodyData['name'])) $requiredErrors[]='Need to declare "name" of the body element to can check: ' . json_encode($bodyData);
                    if(!$this->isSetInArray($bodyData['name'],$rawBody, $value)) $requiredErrors[]='Forgot required body in json: '.$bodyData['name'];
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
        return $uri;
    }

    private function checkVariableSlugs(string $segment) : array {
        $res=[];
        preg_match_all('/\{\{\w{1,}\}\}/',$segment, $dirtyResult);
        $res = array_map($this->removeMacroVarsForSlugs,$dirtyResult[0]);
        return $res;
    }
    private function removeMacroVarsForSlugs(string $dirtyMacro) : string {
        return preg_replace('/(\{\{|\}\})/','',$dirtyMacro);
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

    //https://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php + my touch
    function jsonable(mixed &$string) : bool {
        $string=json_decode($string,true);
        return json_last_error() === JSON_ERROR_NONE;
     }
}