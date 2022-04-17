<?php

namespace Args;

class UtilityArgumentString
{
    private string $argumentString;
    private array $argumentBlocks;

    /**
     * @param  string  $argumentString
     */
    public function __construct(string $argumentString)
    {
        $this->argumentString = self::sanitizeArgumentString($argumentString);
        $this->argumentBlocks = self::parseStringBlocks($this->argumentString);
    }

    /**
     * Parse argument string into blocks.
     *
     * @param  string  $argumentString
     *
     * @return array
     */
    public static function parseStringBlocks(string $argumentString): array
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
        $argumentString = preg_replace('/^[^\s\[]*/', '', $argumentString) ?? $argumentString;
        // Replace equal signs with spaces.
        $argumentString = preg_replace('/=/', ' ', $argumentString);
        // Removes unnecessary whitespaces.
        $argumentString = preg_replace('/\s+([-\[\]])/', '$1', $argumentString) ?? $argumentString;

        return trim($argumentString);
    }

    public static function fromUsageMessageBlock(string $block): self
    {
        $pattern  = 'program [-aDde] [-f | -g] [(-n | --number) number] [-b b_arg | -c c_arg] req1 req2 [opt1 [opt2]]';
        $argument = new self();

        // TODO;

        return $argument;
    }

    /**
     * @return string
     */
    public function getArgumentString(): string
    {
        return $this->argumentString;
    }
}
