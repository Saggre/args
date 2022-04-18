<?php

namespace Args\Helpers;

use Args\Loader\InputOperand;
use Args\Loader\InputOption;
use Args\UtilityArgumentString;

class ArgumentsFormatter
{
    /**
     * Expands arguments with equals sign in to their own arguments.
     *
     * @param  array  $args
     *
     * @return array
     */
    public static function expandArgs(array $args): array
    {
        $options = array();

        foreach ($args as $arg) {
            $split = explode('=', $arg);

            foreach ($split as $value) {
                $options[] = trim($value);
            }
        }

        return $options;
    }

    /**
     * Replaces longhand arguments with their shorthand equivalents.
     *
     * @param  array  $args
     * @param  UtilityArgumentString  $map
     *
     * @return array
     */
    public static function normalizeArgs(array $args, UtilityArgumentString $map): array
    {
        foreach ($args as &$arg) {
            $option = $map->findOptionByKey($arg);

            if (empty($option)) {
                continue;
            }

            if (in_array($arg, $option->getAlternates(true))) {
                $arg = $option->getChar(true);
            }
        }

        return $args;
    }

    public static function reduceArgs(array $args): array
    {
        $options         = [];
        $operands        = [];
        $operandMode     = false;
        $lastOptionIndex = self::getLastOptionIndex($args);

        for ($i = 0; $i < count($args); $i++) {
            $arg = $args[$i];

            if ($arg === '--') {
                $operandMode = true;
                continue;
            }

            if ($i > $lastOptionIndex + 1) {
                $operandMode = true;
            }

            if ($operandMode) {
                $operands[] = $arg;
            } elseif (StringArgument::isArgument($arg)) {
                if ( ! isset($options[$arg])) {
                    $options[$arg] = [];
                }

                if (count($args) > $i + 1 && ! StringArgument::isArgument($args[$i + 1])) {
                    $options[$arg] = array_merge($options[$arg], [$args[$i + 1]]);
                } elseif (((int)reset($options[$arg])) > 0) {
                    $options[$arg][0] = ((int)$options[$arg][0]) + 1;
                } else {
                    $options[$arg][0] = true;
                }
            }
        }

        return [
            'options'  => $options,
            'operands' => $operands,
        ];
    }

    /**
     * Get index of the last option argument.
     *
     * @param  array  $args
     *
     * @return int|null Index of the last option argument or null if none found.
     */
    public static function getLastOptionIndex(array $args): ?int
    {
        for ($i = count($args) - 1; $i >= 0; $i--) {
            if (StringArgument::isArgument($args[$i])) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param  array  $options
     *
     * @return InputOption[]
     */
    public static function mapOptions(array $options): array
    {
        $result = [];

        $i = -1;
        foreach ($options as $arg => $values) {
            $i++;
            $result[] = new InputOption($i, $arg, $values);
        }

        return $result;
    }

    /**
     * @param  array  $operands
     * @param  int  $offset
     *
     * @return InputOperand[]
     */
    public static function mapOperands(array $operands, int $offset): array
    {
        // Combine all operands into one.
        return [new InputOperand($offset, 0, $operands)];
    }

    /*
     * public static function mapOperands(array $operands, int $offset): array
    {
        $result     = [];
        $indicesArr = [];

        $i = -1;
        foreach ($operands as $value) {
            $i++;
            if ( ! isset($indicesArr[$value])) {
                $indicesArr[$value] = [];
            }

            $indicesArr[$value][] = $i;
        }

        $i = -1;
        foreach ($indicesArr as $value => $indices) {
            $i++;
            $result[] = new InputOperand(
                array_map(fn($index) => $index + $offset, $indices),
                $indices,
                [$value]
            );
        }

        return $result;
    }
     */
}
