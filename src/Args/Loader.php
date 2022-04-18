<?php

namespace Args;

use Args\Helpers\ArgumentsFormatter;
use Args\Loader\InputElement;
use Args\UtilityArgument\Argument;
use Args\UtilityArgument\Block;
use Args\UtilityArgument\Operand;
use Args\UtilityArgument\Option;

class Loader
{
    protected UtilityArgumentString $map;

    private array $options;
    private array $operands;

    public function __construct(UtilityArgumentString $map)
    {
        $this->map = $map;

        global $argv;
        $args = ArgumentsFormatter::expandArgs($argv);
        $args = ArgumentsFormatter::normalizeArgs($args, $this->map);
        list($options, $operands) = array_values(ArgumentsFormatter::reduceArgs($args));

        $this->options  = ArgumentsFormatter::mapOptions($options);
        $this->operands = ArgumentsFormatter::mapOperands($operands, count($options));
    }

    public function getInputElementForBlock(Block $block): ?InputElement
    {
        if (is_a($block, Argument::class)) {
            throw new \RuntimeException('Block cannot be an argument');
        }

        if (is_a($block, Option::class)) {
            foreach ($this->options as $option) {
                if ($option->getKey() === $block->getChar(true)) {
                    return $option;
                }
            }
        } elseif (is_a($block, Operand::class)) {
            return $this->operands[0] ?? null;
        }

        return null;
    }

    public function getOpt(string $key, $default = null)
    {
        $block = $this->map->findOptionByKey($key);

        if (empty($block)) {
            throw new \RuntimeException("Option $key not found");
        }

        $inputElement = $this->getInputElementForBlock($block);

        if (empty($inputElement)) {
            throw new \RuntimeException("Input for $key not found");
        }

        switch (count($inputElement->getValues())) {
            case 0:
                return $default;
            case 1:
                return $inputElement->getValues()[0];
            default:
                return $inputElement->getValues();
        }
    }
}
