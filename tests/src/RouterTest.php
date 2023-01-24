<?php declare(strict_types=1);

use Agrandesr\GlobalResponse;
use Agrandesr\Router;
use Agrandesr\Tests\testHelpers\Fake;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testControl(): void
    {
        $this->assertEquals(
            true,
            true
        );
    }

    public function testIsPathEqualToRouterPath() {
        Fake::request();
        $router = new Router();

        $this->assertEquals(true, $router->isPathEqualToRouterPath('',''));
        $this->assertEquals(true, $router->isPathEqualToRouterPath('','/'));
        $this->assertEquals(true, $router->isPathEqualToRouterPath('/','/'));
        $this->assertEquals(true, $router->isPathEqualToRouterPath('test/123/wtf','test/{b}/{c}'));
        $this->assertEquals(true, $router->isPathEqualToRouterPath('123/test/wtf','123/{b}/{c}'));
        $this->assertEquals(true, $router->isPathEqualToRouterPath('mype/tres/eds','mype/tres/eds'));
        $this->assertEquals(true, $router->isPathEqualToRouterPath('1/2/3','{a}/{b}/{c}'));

        $this->assertEquals(false, $router->isPathEqualToRouterPath('test/123/wtf','tes1t/{b}/{c}'));
        $this->assertEquals(false, $router->isPathEqualToRouterPath('123/test/wtf','1223/{b}/{c}'));
        $this->assertEquals(false, $router->isPathEqualToRouterPath('mype/tres/eds','mype/tres/ed2s'));
        $this->assertEquals(false, $router->isPathEqualToRouterPath('1/2/3','{a}/b/{c}'));
    }

    public function testAddRoutesFolderJsons(){
        //@TODO
    }
}