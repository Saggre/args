<?php

namespace Args\UtilityArgument;

class Option extends Block
{
    protected string $char;
    protected array $alternates;
    protected ?Argument $argument = null;
    protected ?int $index;

    public function __construct(bool $isOptional, bool $isRepeating, ?int $index, string $char, array $alternates = array())
    {
        parent::__construct($isOptional, $isRepeating);

        if (empty($char)) {
            throw new \InvalidArgumentException('Option character must not be empty');
        }

        $this->char       = $char[0];
        $this->alternates = $alternates;
        $this->index      = $index;
    }

    public function asGetoptParams(): array
    {
        $shortOptions = '';
        $longOptions  = [];

        foreach ($this->getAllIdentifiers() as $identifier) {
            $modifiers = '';

            if ($this->getArgument() !== null) {
                $modifiers = $this->getArgument()->isOptional() ? '::' : ':';
            }

            if (strlen($identifier) > 1) {
                $longOptions[] = "$identifier$modifiers";
            } else {
                $shortOptions .= "$identifier$modifiers";
            }
        }

        return [
            'shortOptions' => $shortOptions,
            'longOptions'  => $longOptions
        ];
    }

    public function hasArgument(): bool
    {
        return $this->argument !== null;
    }

    public function getChar(bool $withDash = false): string
    {
        return $withDash ? "-$this->char" : $this->char;
    }

    /**
     * @param  bool  $withDashes
     *
     * @return array
     */
    public function getAlternates(bool $withDashes = false): array
    {
        if ( ! $withDashes) {
            return $this->alternates;
        }

        return array_map(function ($identifier) {
            if (strlen($identifier) > 1) {
                return "--$identifier";
            } else {
                return "-$identifier";
            }
        }, $this->alternates);
    }

    public function getAllIdentifiers(): array
    {
        return array_merge(array($this->char), $this->alternates);
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

    /**
     * @return int|null
     */
    public function getIndex(): ?int
    {
        return $this->index;
    }

    /**
     * @param  int|null  $index
     */
    public function setIndex(?int $index): void
    {
        $this->index = $index;
    }
}
