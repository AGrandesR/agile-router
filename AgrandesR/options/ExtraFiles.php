<?php

namespace AgrandesR\Options;

use AgrandesR\Router;
use AgrandesR\GlobalResponse;

class ExtraFiles {

    static function addExtraFiles(Router &$router, string $dirname='routes') {
        $req_uri=GlobalResponse::getPath();
        $pathArray = explode('/',$req_uri);
        // print_r($pathArray);die;
        if (Count($pathArray)>1 && $dir = opendir($dirname)) {
            /* Esta es la forma correcta de iterar sobre el directorio. */
            while (false !== ($filename = readdir($dir))) {
                // echo $filename . "==" . ($pathArray[0] . '.json') . "\n";
                if(preg_match('/\.json$/',$filename) && $filename==($pathArray[0] . '.json')) {
                    // echo $filename;
                    $extraRoutes = json_decode(file_get_contents('routes\\'.$filename),true);
                    $router->addPathRoutes($filename,$extraRoutes);
                    break;
                }
            }
            closedir($dir);
        }
    }
}