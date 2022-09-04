<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//set_error_handler("handleError");
//trigger_error("asdasdasd", E_ERROR);

require './vendor/autoload.php';

use AgrandesR\Router;
$_ENV['JWT_SECRET']='This is a secret';
$_ENV['DB_HOST']='localhost';
$_ENV['DB_TYPE']='mysql';
$_ENV['DB_USER']='root';
$_ENV['DB_PASS']='2ga259lb';
$_ENV['DB_DTBS']='test';
$_ENV['DB_PORT']='5555';
$_ENV['JWT_SECRET']='This is a secret';
$router = new Router();

//use AgrandesR\GlobalResponse;

// GlobalResponse::setSystemErrorCallback(function($test){
//     print_r($test);
// });

$router->run();