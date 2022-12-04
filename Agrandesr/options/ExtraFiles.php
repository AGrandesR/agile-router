<?php

namespace Agrandesr\options;

use Agrandesr\Router;
use Agrandesr\GlobalRequest;
use Agrandesr\GlobalResponse;

class ExtraFiles {

    static function addExtraFiles(Router &$router, string $dirname='routes', bool $fileNameIsPath=true) {
        $pathArray = explode('/',GlobalRequest::getPath()??'/');

        if (is_dir($dirname) && $dir = opendir($dirname)) {
            /* Esta es la forma correcta de iterar sobre el directorio. */
            while (false !== ($filename = readdir($dir))) {
                //echo $filename . "==" . ($pathArray[0] . '.json') . "\n";
                if(preg_match('/\.json$/',$filename) && $filename==($pathArray[0] . '.json')) {
                    $extraRoutes = json_decode(file_get_contents($dirname.'\\'.$filename),true);
                    if(json_last_error()!==JSON_ERROR_NONE) GlobalResponse::addErrorAndShowAndDie("$dirname/$filename is not a valid json [".json_last_error_msg().']');
                    $router->addPathRoutes($filename,$extraRoutes,$fileNameIsPath);
                    break;
                }
            }
            closedir($dir);
        }
    }
}