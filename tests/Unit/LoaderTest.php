<?php

namespace Args\Test\Unit;

use Args\Loader;
use Args\Test\TestCase;
use Args\UtilityArgumentString;

class LoaderTest extends TestCase
{
    public function testGetInputElementForBlock1()
    {
        $this->setArgv('-f file1.txt --limit=10 -f file2.txt -- operand1 operand2');
        $util   = new UtilityArgumentString('(-f|--files) filename... [(-l|--limit) seconds] [operands]...');
        $loader = new Loader($util);

        $block = $util->findOptionByKey('-f');
        self::assertNotEmpty($block);
        $inputElement = $loader->getInputElementForBlock($block);
        self::assertNotEmpty($inputElement);
        self::assertEquals([
            'file1.txt',
            'file2.txt',
        ], $inputElement->getValues());

        $block = $util->findOptionByArgumentName('filename');
        self::assertNotEmpty($block);
        $inputElement = $loader->getInputElementForBlock($block);
        self::assertNotEmpty($inputElement);
        self::assertEquals([
            'file1.txt',
            'file2.txt',
        ], $inputElement->getValues());

        $block = $util->findOperandByName('operands');
        self::assertNotEmpty($block);
        $inputElement = $loader->getInputElementForBlock($block);
        self::assertNotEmpty($inputElement);
        self::assertEquals([
            'operand1',
            'operand2',
        ], $inputElement->getValues());
    }

    public function testGetInputElementForBlock2()
    {
        $this->setArgv('-d val op');
        $util   = new UtilityArgumentString('-d arg [test-args]');
        $loader = new Loader($util);

        $block = $util->findOptionByKey('-d');
        self::assertNotEmpty($block);
        $inputElement = $loader->getInputElementForBlock($block);
        self::assertNotEmpty($inputElement);
        self::assertEquals(['val',], $inputElement->getValues());

        $block = $util->findOperandByName('test-args');
        self::assertNotEmpty($block);
        $inputElement = $loader->getInputElementForBlock($block);
        self::assertNotEmpty($inputElement);
        self::assertEquals(['op',], $inputElement->getValues());
    }

    public function testGetOpt()
    {
        $this->setArgv('--test=1 -f file1.txt -f file2.txt');
        $util   = new UtilityArgumentString('[(-t|--test) test-arg] [(-f|--files) filename...]');
        $loader = new Loader($util);

        self::assertEquals(1, $loader->getOpt('-t'));
        self::assertEquals(1, $loader->getOpt('--test'));
        self::assertEquals(['file1.txt', 'file2.txt'], $loader->getOpt('files'));
    }
}
