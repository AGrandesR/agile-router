<?php declare(strict_types=1);

use AgrandesR\GlobalResponse;
use AgrandesR\http\Response;
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
    public function testSetType(): void
    {
        $tryFalse=GlobalResponse::setType("random");
        $this->assertEquals(
            false,
            $tryFalse
        );
        $this->assertEquals(
            'JSON',
            GlobalResponse::getType()
        );
        $tryTrue=GlobalResponse::setType("txt");
        $this->assertEquals(
            true,
            $tryTrue
        );
        $this->assertEquals(
            'TXT',
            GlobalResponse::getType()
        );
        
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