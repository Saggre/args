<?php

namespace Args\Test\Unit;

use Args\Test\TestCase;

class TestCaseTest extends TestCase
{
    public function testSetArgv()
    {
        global $argv;

        $this->setArgv('--limit=10');
        self::assertEquals('--limit=10', reset($argv));

        $this->setArgv('-f file1.txt file2.txt --limit=10 -f file3.txt');
        self::assertEquals(explode(' ', '-f file1.txt file2.txt --limit=10 -f file3.txt'), $argv);
    }
}
