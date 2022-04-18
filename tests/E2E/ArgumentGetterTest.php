<?php

namespace Args\Test\E2E;

use Args\Loader;
use Args\Test\TestCase;
use Args\UtilityArgumentString;

class ArgumentGetterTest extends TestCase
{
    public function testGetArgument()
    {
        $this->setArgv('-f file1.txt file2.txt --limit=10 -f file3.txt');

        global $argv;

        $util = new UtilityArgumentString('(-f|--files) filename... [(-l|--limit) seconds]');

        $loader = new Loader($util);
        $loader->getOption('files');

        self::assertEquals('--limit=10', reset($argv));
    }
}
