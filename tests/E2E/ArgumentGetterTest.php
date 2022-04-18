<?php

namespace Args\Test\E2E;

use Args\Loader;
use Args\Test\TestCase;
use Args\UtilityArgumentString;

class ArgumentGetterTest extends TestCase
{
    public function testGetArgument()
    {
        $util   = new UtilityArgumentString('(-f|--files) filename... [(-l|--limit) seconds]');
        $loader = new Loader($util);
        $this->setArgv('-f file1.txt file2.txt --limit=10 -f file3.txt');
        self::assertEquals(['file1.txt', 'file2.txt', 'file3.txt'], $loader->getOption('files')->getPrimitiveValues());
        self::assertEquals([10], $loader->getOption('limit')->getPrimitiveValues());

        $util   = new UtilityArgumentString('-u user[-p password]');
        $loader = new Loader($util);
        $this->setArgv('-u Saggre -p Hunter1');
        self::assertEquals(['Saggre'], $loader->getOption('u')->getPrimitiveValues());
        self::assertEquals(['Hunter1'], $loader->getOption('p')->getPrimitiveValues());

        $this->setArgv('-u Saggre');
        self::assertEquals(['Saggre'], $loader->getOption('u')->getPrimitiveValues());
        self::assertEmpty($loader->getOption('p')->getPrimitiveValues());
    }
}
