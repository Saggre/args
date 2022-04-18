<?php

namespace Args\Helpers;

class StringArgument
{
    /**
     * Remove leading dashes from a string.
     *
     * @param  string  $argument
     *
     * @return string
     */
    public static function stripArgument(string $argument): string
    {
        return preg_replace('/^--?/', '', $argument);
    }

    public static function isArgument(string $argument): bool
    {
        return preg_match('/^--?/', $argument);
    }

    public static function isShortArgument(string $argument): bool
    {
        return preg_match('/^-/', $argument);
    }
}
