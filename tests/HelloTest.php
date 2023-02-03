<?php

use PHPUnit\Framework\TestCase;

final class HelloTest extends TestCase
{
    public function testItWorks(): void
    {
// Проверяем, что true – это true
        $this->assertTrue(true);
    }

    public function testAdd():void
    {
        $this->assertEquals(6, 2*3);
    }
}