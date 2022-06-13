<?php

namespace AgrandesR\Options;

use AgrandesR\Router;
use AgrandesR\GlobalRequest;

class ExtraFiles {

    static function addExtraFiles(Router &$router, string $dirname='routes', bool $fileNameIsPath=true) {
        $pathArray = explode('/',GlobalRequest::getPath()??'/');

        if (Count($pathArray)>0 && $dir = opendir($dirname)) {
            /* Esta es la forma correcta de iterar sobre el directorio. */
            while (false !== ($filename = readdir($dir))) {
                //echo $filename . "==" . ($pathArray[0] . '.json') . "\n";
                if(preg_match('/\.json$/',$filename) && $filename==($pathArray[0] . '.json')) {
                    $extraRoutes = json_decode(file_get_contents($dirname.'\\'.$filename),true);
                    $router->addPathRoutes($filename,$extraRoutes,$fileNameIsPath);
                    break;
                }
            }
            closedir($dir);
        }
    }
}