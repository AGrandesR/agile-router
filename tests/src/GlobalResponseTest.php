<?php declare(strict_types=1);

use Agrandesr\GlobalResponse;
use Agrandesr\http\Response;
use PhpParser\Node\Stmt\Global_;
use PHPUnit\Framework\TestCase;

final class GlobalResponseTest extends TestCase
{
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
    public function testAddDataRootValue() {
        GlobalResponse::addData("test1");
        $this->assertEquals(
            "test1",
            GlobalResponse::getData()
        );
    }

    public function testAddData() {
        GlobalResponse::addData("test1");
        GlobalResponse::addData("test2");
        $this->assertEquals(
            ["test1","test2"],
            GlobalResponse::getData()
        );
        $this->expectException(Exception::class);
        GlobalResponse::addData(function(){});
        $this->expectException(Exception::class);
        GlobalResponse::addData(function(){},'test');
    }
}