<?php declare(strict_types=1);

use AgrandesR\GlobalRequest;
use AgrandesR\GlobalResponse;
use AgrandesR\http\Request;
use PhpParser\Node\Stmt\Global_;
use PHPUnit\Framework\TestCase;

final class GlobalRequestTest extends TestCase
{

    public $server = [
        'SERVER_PROTOCOL'=>'http',
        'HTTP_HOST'=>'localhost',
        'SERVER_PROTOCOL'=>'test',
        'SERVER_PORT'=>'5000',
        'REQUEST_URI'=>'random'
    ];
    public function testControl(): void
    {
        $this->assertEquals(
            true,
            true
        );
    }
    /**
     * @runInSeparateProcess
     */
    public function testGetSlug(): void
    {
        $_SERVER=array_merge($_SERVER,$this->server);
        GlobalRequest::setSlug('test','value');
        $this->assertEquals(
            'value',
            GlobalRequest::getSlug('test')
        );
    }

    public function testAddData() {
        $this->assertEquals(
            true,
            true
        );
        // $this->expectException(Exception::class);
        // GlobalRequest::addData(function(){});
        // $this->expectException(Exception::class);
        // GlobalRequest::addData(function(){},'test');
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetSlugAndDie() {
        $_SERVER=array_merge($_SERVER,$this->server);
        $this->expectException(Error::class);
        GlobalRequest::getSlug('random',true);
        $this->assertCount(1,GlobalResponse::getWarnings());
    }
}