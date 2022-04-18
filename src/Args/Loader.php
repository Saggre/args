<?php

namespace Args;

use Args\UtilityArgument\Option;

class Loader
{
    protected UtilityArgumentString $map;

    public function __construct(UtilityArgumentString $map)
    {
        $this->map = $map;
    }

    public function parseArgs(array $args): array
    {
        $arguments = array();

        foreach ($args as $arg) {
            $split = explode('=', $arg);

            foreach ($split as $value) {
                $arguments[] = trim($value);
            }
        }

        return $arguments;
    }

    public function normalizeArgs(array $args): array
    {
        foreach ($args as &$arg) {
            $option = $this->map->findOption($arg);

            if (empty($option)) {
                continue;
            }

            if (in_array($arg, $option->getAlternates(true))) {
                $arg = $option->getChar(true);
            }
        }

        return $args;
    }

    public function collectArgument(array $args, Option $option): array
    {
        $values = [];

        $args = $this->parseArgs($args);
        $args = $this->NormalizeArgs($args);

        $collectingArgument = null;
        $foundCurrent       = 0;
        for ($i = 0; $i < count($args); $i++) {
            $value     = $args[$i];
            $isOption  = Helper::isArgument($value);
            $isCurrent = $value === $option->getChar(true);

            if ( ! empty($collectingArgument)) {
                if ($isOption) {
                    $collectingArgument = null;
                } else {
                    $values[] = $value;

                    if (count($values) > 1 && ! $collectingArgument->isRepeating()) {
                        throw new \RuntimeException("Argument {$collectingArgument->getName()} is not repeating");
                    }
                }
            }

            if ($isCurrent) {
                $foundCurrent++;

                if ($foundCurrent > 1 && ! $option->isRepeating()) {
                    throw new \RuntimeException(sprintf('Option "%s" is not repeating', $option->getChar(true)));
                }

                if ($option->getArgument() === null) {
                    $values[] = true;
                } else {
                    $collectingArgument = $option->getArgument();
                }

                continue;
            }
        }

        if ( ! $option->isOptional() && empty($values)) {
            throw new \RuntimeException("Option '{$option->getChar(true)}' is required");
        }

        return $values;
    }

    public function getOption(string $name): array
    {
        $option = $this->map->findOption($name);

        if (empty($option)) {
            throw new \RuntimeException("Option '$name' not specified in utility argument string");
        }

        global $argv;

        return $this->collectArgument($argv, $option);
    }
}
