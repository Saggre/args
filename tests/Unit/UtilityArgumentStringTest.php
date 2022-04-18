<?php

namespace Args\Test\Unit;

use Args\Test\TestCase;
use Args\UtilityArgument\Operand;
use Args\UtilityArgument\Option;
use Args\UtilityArgumentString;

class UtilityArgumentStringTest extends TestCase
{
    public function testParseOptionStringBlock()
    {
        $block = UtilityArgumentString::parseStringBlock('[(-a|--alpha)[argument_name...]]...');

        if ( ! is_a($block, Option::class)) {
            self::fail('Block is not an Option');
        } else {
            self::assertEquals('a', $block->getChar());
            self::assertEquals(true, in_array('alpha', $block->getAlternates()));
            self::assertEquals(true, $block->isOptional());
            self::assertEquals(true, $block->isRepeating());
            self::assertNotNull($block->getArgument());
            self::assertEquals('argument_name', $block->getArgument()->getName());
            self::assertEquals(true, $block->getArgument()->isOptional());
            self::assertEquals(false, $block->getArgument()->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('-c[c_argument]');

        if ( ! is_a($block, Option::class)) {
            self::fail('Block is not an Option');
        } else {
            self::assertEquals('c', $block->getChar());
            self::assertEquals(false, $block->isOptional());
            self::assertEquals(false, $block->isRepeating());
            self::assertNotNull($block->getArgument());
            self::assertEquals('c_argument', $block->getArgument()->getName());
            self::assertEquals(true, $block->getArgument()->isOptional());
            self::assertEquals(false, $block->getArgument()->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('[-d d_argument]...');

        if ( ! is_a($block, Option::class)) {
            self::fail('Block is not an Option');
        } else {
            self::assertEquals('d', $block->getChar());
            self::assertEquals(true, $block->isOptional());
            self::assertEquals(true, $block->isRepeating());
            self::assertNotNull($block->getArgument());
            self::assertEquals('d_argument', $block->getArgument()->getName());
            self::assertEquals(false, $block->getArgument()->isOptional());
            self::assertEquals(false, $block->getArgument()->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('--delta delta_argument');

        if ( ! is_a($block, Option::class)) {
            self::fail('Block is not an Option');
        } else {
            self::assertEquals('d', $block->getChar());
            self::assertEquals(false, $block->isOptional());
            self::assertEquals(false, $block->isRepeating());
            self::assertNotNull($block->getArgument());
            self::assertEquals('delta_argument', $block->getArgument()->getName());
            self::assertEquals(false, $block->getArgument()->isOptional());
            self::assertEquals(false, $block->getArgument()->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('-h');

        if ( ! is_a($block, Option::class)) {
            self::fail('Block is not an Option');
        } else {
            self::assertEquals('h', $block->getChar());
            self::assertEquals(false, $block->isOptional());
            self::assertEquals(false, $block->isRepeating());
            self::assertNull($block->getArgument());
        }
    }

    public function testParseOperandStringBlock()
    {
        $block = UtilityArgumentString::parseStringBlock('[operand]...');

        if ( ! is_a($block, Operand::class)) {
            self::fail('Block is not an Operand');
        } else {
            self::assertEquals('operand', $block->getName());
            self::assertEquals(true, $block->isOptional());
            self::assertEquals(true, $block->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('[operand...]');

        if ( ! is_a($block, Operand::class)) {
            self::fail('Block is not an Operand');
        } else {
            self::assertEquals('operand', $block->getName());
            self::assertEquals(true, $block->isOptional());
            self::assertEquals(true, $block->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('[operand]');

        if ( ! is_a($block, Operand::class)) {
            self::fail('Block is not an Operand');
        } else {
            self::assertEquals('operand', $block->getName());
            self::assertEquals(true, $block->isOptional());
            self::assertEquals(false, $block->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('operand...');

        if ( ! is_a($block, Operand::class)) {
            self::fail('Block is not an Operand');
        } else {
            self::assertEquals('operand', $block->getName());
            self::assertEquals(false, $block->isOptional());
            self::assertEquals(true, $block->isRepeating());
        }

        $block = UtilityArgumentString::parseStringBlock('operand');

        if ( ! is_a($block, Operand::class)) {
            self::fail('Block is not an Operand');
        } else {
            self::assertEquals('operand', $block->getName());
            self::assertEquals(false, $block->isOptional());
            self::assertEquals(false, $block->isRepeating());
        }
    }

    /**
     * Test argument string sanitization function.
     *
     * @return void
     */
    public function testArgumentStringSanitize()
    {
        self::assertEquals('[-a][-b]', UtilityArgumentString::sanitizeArgumentString('program[-a][-b]'));
        self::assertEquals('-a[-b]', UtilityArgumentString::sanitizeArgumentString('program -a[-b]'));
        self::assertEquals('-a[-b]', UtilityArgumentString::sanitizeArgumentString('program -a [ -b] '));
        self::assertEquals('[-c option_argument]', UtilityArgumentString::sanitizeArgumentString('[-c=option_argument]'));
        self::assertEquals(
            '(-f|--files) filename...[(-l|--limit) seconds]',
            UtilityArgumentString::sanitizeArgumentString('(-f|--files) filename...[(-l|--limit) seconds]')
        );
        self::assertEquals('-u[-p]', UtilityArgumentString::sanitizeArgumentString('-u[-p]'));
    }

    /**
     * Test argument string block parsing.
     *
     * @see https://pubs.opengroup.org/onlinepubs/9699919799/basedefs/V1_chap12.html#tag_12_01
     * @return void
     */
    public function testExplodeStringBlocks()
    {
        $blocks = UtilityArgumentString::explodeStringBlocks('[-a][-b[option_argument]]');
        self::assertCount(2, $blocks);
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-b[option_argument]]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('(-f|--files) filename... [(-l|--limit) seconds]');
        self::assertCount(2, $blocks);
        self::assertEquals('(-f|--files) filename...', array_shift($blocks));
        self::assertEquals('[(-l|--limit) seconds]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('[-a][-b][-c option_argument][-d|-e][-f[option_argument]][operand...]');
        self::assertCount(6, $blocks);
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-b]', array_shift($blocks));
        self::assertEquals('[-c option_argument]', array_shift($blocks));
        self::assertEquals('[-d|-e]', array_shift($blocks));
        self::assertEquals('[-f[option_argument]]', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('[-abcDxyz][-p arg][operand]');
        self::assertCount(3, $blocks);
        self::assertEquals('[-abcDxyz]', array_shift($blocks));
        self::assertEquals('[-p arg]', array_shift($blocks));
        self::assertEquals('[operand]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('[options][operands]');
        self::assertCount(2, $blocks);
        self::assertEquals('[options]', array_shift($blocks));
        self::assertEquals('[operands]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('-d[-a][-c option_argument][operand...]');
        self::assertCount(4, $blocks);
        self::assertEquals('-d', array_shift($blocks));
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-c option_argument]', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('[-a][-b][operand...]');
        self::assertCount(3, $blocks);
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-b]', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('[-g option_argument]...[operand...]');
        self::assertCount(2, $blocks);
        self::assertEquals('[-g option_argument]...', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('-f option_argument [-f option_argument]... [operand...]');
        self::assertCount(3, $blocks);
        self::assertEquals('-f option_argument', array_shift($blocks));
        self::assertEquals('[-f option_argument]...', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::explodeStringBlocks('(-f|--force) option_argument [(-l|--light) option_argument]... --usage [operand...]');
        self::assertCount(4, $blocks);
        self::assertEquals('(-f|--force) option_argument', array_shift($blocks));
        self::assertEquals('[(-l|--light) option_argument]...', array_shift($blocks));
        self::assertEquals('--usage', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));
    }
}
