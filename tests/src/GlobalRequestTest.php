<?php declare(strict_types=1);

use Agrandesr\GlobalRequest;
use Agrandesr\GlobalResponse;
use Agrandesr\Tests\testHelpers\Fake;
use PHPUnit\Framework\TestCase;

final class GlobalRequestTest extends TestCase
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
    public function testGetSlug(): void
    {
        Fake::request();
        GlobalRequest::setSlug('test','value');
        $this->assertEquals(
            'value',
            GlobalRequest::getSlug('test')
        );
    }
}