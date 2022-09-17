<?php
namespace Agrandesr\extra;

use Agrandesr\GlobalResponse;

class Errors {
    static function setHandler(){
        ini_set('display_errors', 'Off');
        set_error_handler(function($code, $description, $file = null, $line = null, $context = null) {
            //GlobalResponse::callSystemErrorCallback($code, $description, $file, $line, $context);
            switch($code){
                case E_PARSE:
                case E_NOTICE:
                case E_WARNING:
                case E_USER_WARNING:
                case E_COMPILE_WARNING: 
                case E_RECOVERABLE_ERROR: 
                    GlobalResponse::addSystemWarning($code, $description, $file, $line);
                    break;
                // case E_ERROR:
                // case E_COMPILE_ERROR:
                // case E_CORE_WARNING:
                // case E_CORE_ERROR:
                // case E_USER_ERROR:
                // case E_USER_NOTICE:
                // case E_STRICT:
                default:
                    GlobalResponse::setSystemErrorAndRenderAndDie($code, $description, $file, $line);
                    break;
            }
        });
        set_exception_handler(function($e) {
            if($e->getMessage()=='X-AGRANDESR-DIE') {
                return; //This will be used to stop the execution of the code without using die, to allow to use phpunit easy
            }
            GlobalResponse::setCatchedSystemErrorAndRenderAndDie($e);
            return true;
        });
        register_shutdown_function(function () {
            // check if the script ended up with an error
            $lastError    = error_get_last(); 
            $fatal_errors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
            if ($lastError && in_array($lastError['type'], $fatal_errors, true)) {
                //https://lessthan12ms.com/error-handling-in-php-and-formatting-pretty-error-responses-to-users.html
                //var_dump($lastError);die;
                GlobalResponse::setSystemError($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
                GlobalResponse::render();
            }
        });
    }

}
/*
switch($code){
    case E_ERROR:               $codeName = "Error";                  break;
    case E_WARNING:             $codeName = "Warning";                break;
    case E_PARSE:               $codeName = "Parse Error";            break;
    case E_NOTICE:              $codeName = "Notice";                 break;
    case E_CORE_ERROR:          $codeName = "Core Error";             break;
    case E_CORE_WARNING:        $codeName = "Core Warning";           break;
    case E_COMPILE_ERROR:       $codeName = "Compile Error";          break;
    case E_COMPILE_WARNING:     $codeName = "Compile Warning";        break;
    case E_USER_ERROR:          $codeName = "User Error";             break;
    case E_USER_WARNING:        $codeName = "User Warning";           break;
    case E_USER_NOTICE:         $codeName = "User Notice";            break;
    case E_STRICT:              $codeName = "Strict Notice";          break;
    case E_RECOVERABLE_ERROR:   $codeName = "Recoverable Error";      break;
    default:                    $codeName = "Unknown error ($code)";  break;
}
*/