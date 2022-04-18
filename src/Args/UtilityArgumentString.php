<?php

namespace Args;

use Args\Helpers\StringArgument;
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
        $this->blocks         = [];
        foreach (array_values($stringBlocks) as $index => $stringBlock) {
            $this->blocks[] = self::parseStringBlock($stringBlock, $index);
        }
    }

    /**
     * @param  string  $key
     *
     * @return Option|null
     */
    public function findOptionByKey(string $key): ?Option
    {
        $key = StringArgument::stripArgument($key);

        foreach ($this->blocks as $block) {
            if ( ! is_a($block, Option::class)) {
                continue;
            }

            if (in_array($key, $block->getAllIdentifiers())) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @param  string  $name  Argument name.
     *
     * @return Option|null
     */
    public function findOptionByArgumentName(string $name): ?Option
    {
        $name = trim($name);

        foreach ($this->blocks as $block) {
            if ( ! is_a($block, Option::class)) {
                continue;
            }

            if ($block->getArgument() === null) {
                continue;
            }

            if ($block->getArgument()->getName() === $name) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @param  string  $name  Operand name.
     *
     * @return Option|null
     */
    public function findOperandByName(string $name): ?Operand
    {
        $name = trim($name);

        foreach ($this->blocks as $block) {
            if ( ! is_a($block, Operand::class)) {
                continue;
            }

            if ($block->getName() === $name) {
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
        $argumentString = preg_replace('/^[^-\s\[\(]*/', '', $argumentString) ?? $argumentString;
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

    public static function parseStringBlock(string $stringBlock, ?int $index = null): Block
    {
        $result = self::reduceStringBlock($stringBlock);
        list($stringBlock, $isOptional, $isRepeating) = array_values($result);

        if(preg_match('/^[\w\d]/',$stringBlock)){
            $optionStrings = [];
        }else {
            preg_match_all(
                '/[\(]?-{1,2}(\w+)/',
                $stringBlock,
                $matches
            );

            $optionStrings = $matches[1] ?? array();
        }

        if (empty($optionStrings)) {
            // Operand.
            return new Operand($isOptional, $isRepeating, trim($stringBlock));
        } else {
            // Option.
            usort($optionStrings, function ($a, $b) {
                return strlen($a) > strlen($b);
            });

            $option = new Option($isOptional, $isRepeating, $index, $optionStrings[0], array_slice($optionStrings, 1));

            if (preg_match('([\s\[]\w+(?:\.{3})?\]?$)', $stringBlock, $matches)) {
                $result = self::reduceStringBlock($matches[0]);
                list($stringBlock, $isOptional,) = array_values($result);

                $argument = new Argument($isOptional, trim($stringBlock));
                $argument->setParent($option);
                $option->setArgument($argument);
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
