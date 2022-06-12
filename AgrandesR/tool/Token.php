<?php
namespace AgrandesR\tool;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use AgrandesR\GlobalResponse;

use Exception;
use Error;

class Token {
    static function tokenEncode(array $data, string $flag='', int $expiration=3600) : string {
        try {
            $flag = (empty($flag)) ? 'JWT_SECRET' : 'JWT_'.$flag.'_SECRET';
            $token = array(
                'iat' => time(), // Tiempo que inició el token
                'exp' => time() + $expiration, // Tiempo que expirará el token (+1 hora)
                'data' => $data
            );
            return JWT::encode($token, $_ENV[$flag],'HS256');
        } catch(Exception | Error $e) {
            GlobalResponse::addErrorAndShowAndDie($e->getMessage(),401);
        }
    }
    static function tokenDecode(string $token, string $flag) : array {
        try {
            $flag = (empty($flag)) ? 'JWT_SECRET' : 'JWT_'.$flag.'_SECRET';
            $rawSTD = JWT::decode($token, new Key($_ENV[$flag], 'HS256'));
            $rawArray = json_decode(json_encode($rawSTD),true);
            $rawArray['status']=true;
            return $rawArray;
        } catch(Exception | Error $e) {
            //echo $e->getMessage();die;
            return [
                "status"=>false,
                "msg"=>$e->getMessage()
            ];
        }
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