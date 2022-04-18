<?php

namespace Args\UtilityArgument;

class Argument extends Block
{
    protected string $name;

    /**
     * @param  bool  $isOptional
     * @param  bool  $isRepeating
     * @param  string  $name
     */
    public function __construct(bool $isOptional, bool $isRepeating, string $name)
    {
        parent::__construct($isOptional, $isRepeating);

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
