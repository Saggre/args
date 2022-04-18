<?php

namespace Args\Test\Unit;

use Args\Test\TestCase;
use Args\UtilityArgument\Argument;
use Args\UtilityArgument\Option;

class OptionTest extends TestCase
{
    public function testAsGetoptParams()
    {
        $option = new Option(true, false, null, 'c');
        $option->setArgument(new Argument(true, false, 'name'));
        list($shortOptions,) = array_values($option->asGetoptParams());
        self::assertEquals('c::', $shortOptions);
    }
}
