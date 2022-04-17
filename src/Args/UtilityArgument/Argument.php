<?php

namespace Args\UtilityArgument;

class Argument extends Block
{
    use RepeatingBlockTrait;

    protected string $name;

    /**
     * @param  string  $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
