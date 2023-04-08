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
    public function testDataParseValues(): void {
        $_GET['help']='parameter test';
        $_SERVER['HTTP_HELP']='header test';
        GlobalRequest::saveRequiredBody('help','body test');
        GlobalRequest::setTokenData(['help'=>'token test']);

        $testArray=[
            "lvl1"=>[
                "lvl2"=>'This is a ?help?',
                "lvl2b"=>[
                    "lvl3"=>'This is a ^help^',
                    "lvl3b"=>'This is a $help$'
                ]
            ],
            'This is a #help#'
        ];
        $resultArray=[
            "lvl1"=>[
                "lvl2"=>'This is a parameter test',
                "lvl2b"=>[
                    "lvl3"=>'This is a header test',
                    "lvl3b"=>'This is a body test'
                ]
            ],
            'This is a token test'
        ];

        $this->assertEquals(
            $resultArray,
            StringRouter::dataParseValues($testArray)
        );
    }
}