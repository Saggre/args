<?php

namespace Args;

const ARGS_DISALLOW_MULTIPLE_VALUES = 0b10;
const ARGS_UNWRAP_SINGLE_VALUE      = 0b01;

const ARGS_GET_ARG_DEFAULT_FLAGS = ARGS_UNWRAP_SINGLE_VALUE;

global $argsLoader;
$argsLoader = new Loader();

/**
 * Get argument by name or shorthand.
 *
 * @param  int|string  $arg  Full argument name ex. --usage or positional argument index. Positional arguments are indexed from 0.
 * @param  string|null  $short_arg  Shorthand argument name ex. -u.
 * @param  int  $flags  Parse options.
 *
 * @return string|array|null Argument value or array if there are multiple values or null if not found.
 */
function getArg($arg, ?string $short_arg = null, int $flags = ARGS_GET_ARG_DEFAULT_FLAGS)
{
    global $argsLoader;

    return $argsLoader->getArg($arg, $short_arg, $flags);
}
