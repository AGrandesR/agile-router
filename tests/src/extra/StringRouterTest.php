<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Agrandesr\extra\StringRouter;
use Agrandesr\GlobalRequest;

final class StringRouterTest extends TestCase
{
    public function testControl(): void
    {
        $this->assertEquals(
            true,
            true
        );
    }

    public function testParseValues(): void {
        $_GET['help']='parameter test';
        $test='This is a ?help?';
        $result='This is a parameter test';
        $this->assertEquals(
            $result,
            StringRouter::parseValues($test)
        );
        $_SERVER['HTTP_HELP']='header test';
        $test='This is a ^help^';
        $result='This is a header test';
        $this->assertEquals(
            $result,
            StringRouter::parseValues($test)
        );
        GlobalRequest::saveRequiredBody('help','body test');
        $test='This is a $help$';
        $result='This is a body test';
        $this->assertEquals(
            $result,
            StringRouter::parseValues($test)
        );

        GlobalRequest::setTokenData(['help'=>'token test']);
        $test='This is a #help#';
        $result='This is a token test';
        $this->assertEquals(
            $result,
            StringRouter::parseValues($test)
        );
    }
}