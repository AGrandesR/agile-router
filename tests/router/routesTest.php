<?php declare(strict_types=1);

use Agrandesr\GlobalResponse;
use Agrandesr\http\Response;
use PhpParser\Node\Stmt\Global_;
use PHPUnit\Framework\TestCase;

final class routesTest extends TestCase
{
    public function testControl(): void
    {
        $this->assertEquals(
            true,
            true
        );
    }
    function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r")); 
        }
        else {
            exec($cmd . " > /dev/null &");  
        }
    }
    public function testServer(): void
    {
        return;
        //$this->execInBackground('composer serve');
        $proc=proc_open('composer serve',[],$pipe);
        $response=$this->call('localhost:9876/php?param=test');

        $this->assertEquals(
            ["success"=>true,'data' => 'data test'],
            json_decode($response,true)
        );
        proc_terminate($proc);

    }

    public function call($url) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        return curl_exec($curl);
    }

/*
    public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }*/
}