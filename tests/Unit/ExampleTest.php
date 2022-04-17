<?php

namespace Args\Test\Unit;

use function Args\getArg;

class ExampleTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testSetArgv()
    {
        $this->setArgv('--limit=10');

        global $argv;

        self::assertEquals('--limit=10', reset($argv));
    }
}
