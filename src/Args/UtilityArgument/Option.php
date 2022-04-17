<?php

namespace Args\UtilityArgument;

class Option extends Block
{
    use RepeatingBlockTrait;

    protected int $char;
    protected ?Argument $argument;

    public function __construct(string $char)
    {
        $this->char = ord($char);
    }

    public function getChar(): string
    {
        return chr($this->char);
    }

    public function hasArgument(): bool
    {
        return $this->argument !== null;
    }
}
