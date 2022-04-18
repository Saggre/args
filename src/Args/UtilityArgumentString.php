<?php

namespace Args;

use Args\UtilityArgument\Argument;
use Args\UtilityArgument\Block;
use Args\UtilityArgument\Operand;
use Args\UtilityArgument\Option;

class UtilityArgumentString
{
    private string $argumentString;

    /**
     * @var Block[]
     */
    private array $blocks;

    /**
     * @param  string  $argumentString
     */
    public function __construct(string $argumentString)
    {
        $this->argumentString = self::sanitizeArgumentString($argumentString);
        $stringBlocks         = self::explodeStringBlocks($this->argumentString);
        $this->blocks         = array_map([self::class, 'parseStringBlock'], $stringBlocks);
    }

    /**
     * @param  string  $name
     *
     * @return Option|null
     */
    public function findOption(string $name): ?Option
    {
        $name = Helper::stripArgument($name);

        foreach ($this->blocks as $block) {
            if ( ! is_a($block, Option::class)) {
                continue;
            }

            if (in_array($name, $block->getAllIdentifiers())) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        $options = [];
        foreach ($this->blocks as $block) {
            if ( ! is_a($block, Option::class)) {
                continue;
            }

            $options[] = $block;
        }

        return $options;
    }

    /**
     * Parse argument string into blocks.
     *
     * @param  string  $argumentString
     *
     * @return array
     */
    public static function explodeStringBlocks(string $argumentString): array
    {
        preg_match_all(
            '/((?:\[[^\]\.]*(?:\.{3})?\]+(?:\.{3})?)|(?:(?:(?:-[\w\d])|(?:--[\w\d]+)|(?:\([^\)\.]*(?:\.{3})?\)+ (?:\.{3})?))\s?[^\[\s\.]*(?:\.{3})?))/',
            $argumentString,
            $matches
        );

        $blocks = reset($matches) ?: array();

        return array_map('trim', $blocks);
    }

    /**
     * Sanitize the argument string.
     *
     * @param  string  $argumentString
     *
     * @return string
     */
    public static function sanitizeArgumentString(string $argumentString): string
    {
        // Removes program name from argument string if it is present.
        $argumentString = preg_replace('/^[^\s\[\(]*/', '', $argumentString) ?? $argumentString;
        // Replace equal signs with spaces.
        $argumentString = preg_replace('/=/', ' ', $argumentString);
        // Removes unnecessary whitespaces.
        $argumentString = preg_replace('/\s+([-\[\]])/', '$1', $argumentString) ?? $argumentString;

        return trim($argumentString);
    }

    protected static function reduceStringBlock(string $stringBlock): array
    {
        $stringBlock = trim($stringBlock);

        $isOptional  = false;
        $isRepeating = false;

        if (preg_match('/^.+\.{3}$/', $stringBlock)) {
            $isRepeating = true;
            $stringBlock = substr($stringBlock, 0, -3);
        }

        if (preg_match('/^\[.*\]$/', $stringBlock)) {
            $isOptional  = true;
            $stringBlock = substr($stringBlock, 1, -1);
        }

        if (preg_match('/^.+\.{3}$/', $stringBlock)) {
            $isRepeating = true;
            $stringBlock = substr($stringBlock, 0, -3);
        }

        return [
            'stringBlock' => $stringBlock,
            'isOptional'  => $isOptional,
            'isRepeating' => $isRepeating,
        ];
    }

    public static function parseStringBlock(string $stringBlock): Block
    {
        $result = self::reduceStringBlock($stringBlock);
        list($stringBlock, $isOptional, $isRepeating) = array_values($result);

        preg_match_all(
            '/[\(]?-{1,2}(\w+)/',
            $stringBlock,
            $matches
        );

        $optionStrings = $matches[1] ?? array();

        if (empty($optionStrings)) {
            // Operand.
            return new Operand($isOptional, $isRepeating, trim($stringBlock));
        } else {
            // Option.
            usort($optionStrings, function ($a, $b) {
                return strlen($a) > strlen($b);
            });

            $option = new Option($isOptional, $isRepeating, $optionStrings[0], array_slice($optionStrings, 1));

            if (preg_match('([\s\[]\w+(?:\.{3})?\]?$)', $stringBlock, $matches)) {
                $result = self::reduceStringBlock($matches[0]);
                list($stringBlock, $isOptional, $isRepeating) = array_values($result);

                if ($option->isRepeating()) {
                    $isRepeating = true;
                }

                $option->setArgument(new Argument($isOptional, $isRepeating, trim($stringBlock)));
            }

            return $option;
        }
    }

    /**
     * @return string
     */
    public function getArgumentString(): string
    {
        return $this->argumentString;
    }
}
