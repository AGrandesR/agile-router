<?php
namespace Agrandesr;

use Agrandesr\extra\Errors;
use Agrandesr\extra\ExtraFiles;

class Router {
    private array $routesMap;

    private array $routeData;

    function __construct(string $routePath = 'routes.json', string $folderPath='routes') {
        //All the error will be handled by Agile-router now
        Errors::setHandler();
        //Now you will be avaible to use GlobalRequest in all the code
        GlobalRequest::init();

        //We read all the routes and create the map
        if(!is_file($routePath)) GlobalResponse::throwSystemError(0, "The route file $routePath doesn't exist.",__FILE__,__LINE__ );
        $map = json_decode(file_get_contents($routePath),true);
        if(json_last_error()!==JSON_ERROR_NONE) GlobalResponse::throwSystemError(0, "$routePath is not a valid json [".json_last_error_msg().']',$routePath,0 );

        //We add all the routes posibles with the actual path to the routesMap (always routes.json and in the folder of routes, only if match with the first slug)
        $this->routesMap=$map;
        $this->addRoutesFolderJsons($folderPath);
    }

    /**
     * This is the main function that always have to be use to redirect to correct php files and render the correct response.
     * @throws SystemError If you don't do something OK the router will throw an error
     * @return Void
     */ 
    public function run() : void {
        //region READ path
        $isFound=false;
        foreach($this->routesMap as $routePath => $methodOption){
            if($this->isPathEqualToRouterPath(GlobalRequest::getPath(),$routePath)){
                foreach ($methodOption as $method => $methodData) {
                    if(strtoupper($method)==$_SERVER['REQUEST_METHOD']){
                        $isFound=true;
                        $this->routeData=$methodData;
                        break 2;
                    }
                }
            } 
        }
        //endregion

        //region EXECUTE actions of JSON in priority order

        //endregion
    }

    /**
     * This function is to add the routes that will mate with the first slug of the actual request.
     * @param string $dirname is used to specify the folder that have all the routes jsons.
     * @throws SystemError If found a json that is not well contruct
     * @return Void
     */ 
    public function addRoutesFolderJsons(string $dirname) : void {
        $pathArray = explode('/',GlobalRequest::getPath()??'/'); //We get the array of differents slugs of the actual path

        if (is_dir($dirname) && $dir = opendir($dirname)) {
            while (false !== ($filename = readdir($dir))) {
                if(preg_match('/\.json$/',$filename) && $filename==($pathArray[0] . '.json')) { //We ignore if don't is the actual pathArray
                    $newRoutes = json_decode(file_get_contents($dirname.'\\'.$filename),true);
                    if(json_last_error()!==JSON_ERROR_NONE) GlobalResponse::throwSystemError(0, "$dirname/$filename is not a valid json [".json_last_error_msg().']',"$dirname/$filename",0);
                    foreach($newRoutes as $key=>$value) {
                        $newkey = stripslashes($pathArray[0] . ($key ? ("/". $key):'')); //We add to the paths of the json file the name of the file that means the first slug of the path
                        $this->routesMap[$newkey ?? $key] = $value;
                    }
                    break;
                }
            }
            closedir($dir);
        }
    }
    /**
     * This function is to check that the actual path is equal to the router path. The problem is with the slugs. This function have to be avaible to detect a path with slugs and not only a exact match.
     * @param string $requestedPath is the actual path that we requested in the browser
     * @param string $routerPath is used to specify the folder that have all the routes jsons.
     * @throws SystemError If found a json that is not well contruct
     * @return Void
     */ 
    public function isPathEqualToRouterPath(string $requestedPath, string $routerPath) : bool {
        if(empty($requestedPath) && $routerPath=='/') $routerPath=''; //I want that can be use "" and "/" to say the root of a path! : )
        if(strpos($routerPath,'{')!=false && strpos($routerPath,'{')!=false){
            $uriArray=explode('/',$requestedPath);
            $pathArray=explode('/',$routerPath);
            $match=false;
            foreach($pathArray as $idx=>$path){
                if(!isset($uriArray[$idx])) break;
                if(strpos($path,'{')!==false && strpos($path,'}')!==false) {
                    $match=true;
                    GlobalRequest::setSlug(trim($path," {}"),$uriArray[$idx]);
                    continue;
                }
                if($uriArray[$idx]==$path) $match=true;
                else {
                    $match=false;
                    break;
                }
            }
            return $match;
        } else return $routerPath==$requestedPath;
    }
}