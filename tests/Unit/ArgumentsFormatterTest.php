<?php

namespace Args\Test\Unit;

use Args\Helpers\ArgumentsFormatter;
use Args\Test\TestCase;

class ArgumentsFormatterTest extends TestCase
{
    public function testGetLastOptionIndex()
    {
        self::assertEquals(2, ArgumentsFormatter::getLastOptionIndex(['-a', '-b', '-c']));
    }

    public function testReduceArgs()
    {
        $args = ['-c', 'c_value', '-b', 'b_value', 'operand_1', 'operand_2'];
        list($options, $operands) = array_values(ArgumentsFormatter::reduceArgs($args));

        self::assertEquals('c_value', $options['-c'][0]);
        self::assertEquals('b_value', $options['-b'][0]);
        self::assertEquals('operand_1', $operands[0]);
        self::assertEquals('operand_2', $operands[1]);

        $args = ['-a', '-b', '-b', '--', '-b', 'b_value'];
        list($options, $operands) = array_values(ArgumentsFormatter::reduceArgs($args));

        self::assertEquals(true, $options['-a'][0]);
        self::assertEquals(2, $options['-b'][0]);
        self::assertEquals('-b', $operands[0]);
        self::assertEquals('b_value', $operands[1]);

        $args = ['-a', 'value', '-b', '--'];
        list($options, $operands) = array_values(ArgumentsFormatter::reduceArgs($args));

        self::assertEquals('value', $options['-a'][0]);
        self::assertEquals(true, $options['-b'][0]);
        self::assertEmpty($operands);
    }

    public function testMapOptions()
    {
        $args = ['-c', 'c_value', '-b', 'b_value', 'operand_1', 'operand_2'];
        list($options,) = array_values(ArgumentsFormatter::reduceArgs($args));

        $result = ArgumentsFormatter::mapOptions($options);
        self::assertEquals('-c', $result[0]->getKey());
        self::assertEquals('c_value', $result[0]->getValues()[0]);
        self::assertEquals(0, $result[0]->getIndex());
        self::assertEquals('-b', $result[1]->getKey());
        self::assertEquals('b_value', $result[1]->getValues()[0]);
        self::assertEquals(1, $result[1]->getIndex());
    }

    public function testMapOperands()
    {
        $args = ['-c', 'c_value', '-b', 'b_value', 'operand_1', 'operand_n', 'operand_n'];
        list($options, $operands) = array_values(ArgumentsFormatter::reduceArgs($args));

        $result = ArgumentsFormatter::mapOperands($operands, count($options));
        self::assertEquals('operand_1', $result[0]->getValues()[0]);
        self::assertEquals('operand_n', $result[0]->getValues()[1]);
        self::assertEquals('operand_n', $result[0]->getValues()[2]);
        self::assertEquals(2, $result[0]->getIndex());
        self::assertEquals(0, $result[0]->getOperandIndex());
    }
}
