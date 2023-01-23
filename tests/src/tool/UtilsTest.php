<?php declare(strict_types=1);

use Agrandesr\tool\Utils;
use Agrandesr\Tests\testHelpers\Fake;
use PHPUnit\Framework\TestCase;


final class UtilsTest extends TestCase
{
    public function testControl(): void
    {
        $this->assertEquals(
            true,
            true
        );
    }

    public function testArrayToXML() {
        $jsonArray = [
            "lvl1"=>[
                "lvl2"=>"text",
                "lvl2b"=>[1,2,3]
            ]
        ];
        $xml="<?xml version=\"1.0\"?>\n<ALL><lvl1><lvl2>text</lvl2><lvl2b><0>1</0><1>2</1><2>3</2></lvl2b></lvl1></ALL>\n";

        $this->assertEquals($xml,Utils::arrayToXML($jsonArray));
    }

    public function testXmlToArray() {
        $xml="<?xml version=\"1.0\"?>\n<ALL><lvl1><lvl2>text</lvl2><lvl2b><0>1</0><1>2</1><2>3</2></lvl2b></lvl1></ALL>\n";
        $array = [
            "lvl1"=>[
                "lvl2"=>"text",
                "lvl2b"=>[1,2,3]
            ]
        ];
        $this->assertEquals(Utils::arrayToXML($array), $xml);
    }

    public function testJsonable() {
        $goodJson='{"test":1}';
        $badJson='{random::::test}';
        $this->assertEquals(true, Utils::jsonable($goodJson));
        $this->assertEquals(["test"=>1], $goodJson);
        $this->assertEquals(false, Utils::jsonable($badJson));
    }
}