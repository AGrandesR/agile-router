<?php
namespace Agrandesr\tool;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Agrandesr\GlobalResponse;

use Exception;
use Error;

class Token {
    static function encode(array $data, string $flag='', int $expiration=3600) : string {
        $flag = (empty($flag)) ? 'JWT_SECRET' : 'JWT_'.$flag.'_SECRET';
        if(!isset($_ENV[$flag])) GlobalResponse::addErrorAndShowAndDie("The $flag is not set in your env variables.");
        $token = array(
            'iat' => time(), // Tiempo que inició el token
            'exp' => time() + $expiration, // Tiempo que expirará el token (+1 hora)
            'data' => $data
        );
        return JWT::encode($token, $_ENV[$flag],'HS256');       
    }
    static function decode(string $token, string $flag, bool $showAllErrorData=true) : array {
        $flag = (empty($flag)) ? 'JWT_SECRET' : 'JWT_'.$flag.'_SECRET';
        if(!isset($_ENV[$flag])) GlobalResponse::addErrorAndShowAndDie("The $flag is not set in your env variables.",401);
        if(!preg_match('/\w{1,}\.\w{1,}\.\w{1,}/',$token)) GlobalResponse::addErrorAndShowAndDie("$token not is a valid Token. Check the format.",401);
        try{
            $rawSTD = JWT::decode($token, new Key($_ENV[$flag], 'HS256'));
        } catch(SignatureInvalidException $e){
            if($showAllErrorData) GlobalResponse::setCatchedSystemErrorAndShowAndDie($e,401);
            else GlobalResponse::addErrorAndShowAndDie('Token error: '.$e->getMessage(),401);
        } catch(Exception $e){
            if($showAllErrorData) GlobalResponse::setCatchedSystemErrorAndShowAndDie($e,401);
            else GlobalResponse::addErrorAndShowAndDie('Token error: '.$e->getMessage(),401);
        }
        $rawArray = json_decode(json_encode($rawSTD),true);
        $rawArray['status']=true;
        return $rawArray;
    }
}
/*
$time = time();
$key = 'my_secret_key';

$token = array(
    'iat' => $time, // Tiempo que inició el token
    'exp' => $time + (60*60), // Tiempo que expirará el token (+1 hora)
    'data' => [ // información del usuario
        'id' => 1,
        'name' => 'Eduardo'
    ]
);





var_dump($data);

*/