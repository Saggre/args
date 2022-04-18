<?php

namespace Args\UtilityArgument;

class Option extends Block
{
    protected string $char;
    protected array $alternates;
    protected ?Argument $argument = null;

    public function __construct(bool $isOptional, bool $isRepeating, string $char, array $alternates = array())
    {
        parent::__construct($isOptional, $isRepeating);

        if (empty($char)) {
            throw new \InvalidArgumentException('Option character must not be empty');
        }

        $this->char       = $char[0];
        $this->alternates = $alternates;
    }

    public function getChar(): string
    {
        return $this->char;
    }

    public function hasArgument(): bool
    {
        return $this->argument !== null;
    }

    /**
     * @return array
     */
    public function getAlternates(): array
    {
        return $this->alternates;
    }

    /**
     * @return Argument|null
     */
    public function getArgument(): ?Argument
    {
        return $this->argument;
    }

    /**
     * @param  Argument  $argument
     */
    public function setArgument(Argument $argument): void
    {
        $this->argument = $argument;
    }
}
