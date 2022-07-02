<?php
namespace AgrandesR\extra;

use AgrandesR\GlobalResponse;

class Errors {
    static function setHandler(){
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
                    GlobalResponse::setSystemErrorAndShowAndDie($code, $description, $file, $line);
                    break;
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