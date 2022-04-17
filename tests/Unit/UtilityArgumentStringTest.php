<?php

namespace Args\Test\Unit;

use Args\UtilityArgumentString;

class UsageArgumentStringTest extends TestCase
{
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
    }

    /**
     * Test argument string block parsing.
     *
     * @see https://pubs.opengroup.org/onlinepubs/9699919799/basedefs/V1_chap12.html#tag_12_01
     * @return void
     */
    public function testParseStringBlocks()
    {
        $blocks = UtilityArgumentString::parseStringBlocks('[-a][-b[option_argument]]');
        self::assertCount(2, $blocks);
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-b[option_argument]]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('[-a][-b][-c option_argument][-d|-e][-f[option_argument]][operand...]');
        self::assertCount(6, $blocks);
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-b]', array_shift($blocks));
        self::assertEquals('[-c option_argument]', array_shift($blocks));
        self::assertEquals('[-d|-e]', array_shift($blocks));
        self::assertEquals('[-f[option_argument]]', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('[-abcDxyz][-p arg][operand]');
        self::assertCount(3, $blocks);
        self::assertEquals('[-abcDxyz]', array_shift($blocks));
        self::assertEquals('[-p arg]', array_shift($blocks));
        self::assertEquals('[operand]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('[options][operands]');
        self::assertCount(2, $blocks);
        self::assertEquals('[options]', array_shift($blocks));
        self::assertEquals('[operands]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('-d[-a][-c option_argument][operand...]');
        self::assertCount(4, $blocks);
        self::assertEquals('-d', array_shift($blocks));
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-c option_argument]', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('[-a][-b][operand...]');
        self::assertCount(3, $blocks);
        self::assertEquals('[-a]', array_shift($blocks));
        self::assertEquals('[-b]', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('[-g option_argument]...[operand...]');
        self::assertCount(2, $blocks);
        self::assertEquals('[-g option_argument]...', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('-f option_argument [-f option_argument]... [operand...]');
        self::assertCount(3, $blocks);
        self::assertEquals('-f option_argument', array_shift($blocks));
        self::assertEquals('[-f option_argument]...', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));

        $blocks = UtilityArgumentString::parseStringBlocks('(-f|--force) option_argument [(-l|--light) option_argument]... --usage [operand...]');
        self::assertCount(4, $blocks);
        self::assertEquals('(-f|--force) option_argument', array_shift($blocks));
        self::assertEquals('[(-l|--light) option_argument]...', array_shift($blocks));
        self::assertEquals('--usage', array_shift($blocks));
        self::assertEquals('[operand...]', array_shift($blocks));
    }
}
