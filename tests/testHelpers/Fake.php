<?php
namespace Agrandesr\Tests\testHelpers;

class Fake {
    static function request() {
        $server = [
            'REQUEST_METHOD'=>'GET',
            'SERVER_PROTOCOL'=>'http',
            'HTTP_HOST'=>'localhost',
            'SERVER_PROTOCOL'=>'test',
            'SERVER_PORT'=>'5000',
            'REQUEST_URI'=>'random',
        ];
        $_SERVER=array_merge($_SERVER, $server);
    }
}