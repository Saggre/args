<?php

namespace Args\UtilityArgument;

class Argument extends Block
{
    protected string $name;

    /**
     * @param  bool  $isOptional
     * @param  string  $name
     */
    public function __construct(bool $isOptional, string $name)
    {
        parent::__construct($isOptional, false);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
