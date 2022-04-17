<?php

namespace Args;

class Helper
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
}
