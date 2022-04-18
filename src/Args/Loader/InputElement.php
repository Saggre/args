<?php

namespace Args\Loader;

abstract class InputElement
{
    protected int $index;
    protected array $values;

    /**
     * @param  int  $index
     * @param  array  $values
     */
    public function __construct(int $index, array $values)
    {
        $this->index  = $index;
        $this->values = $values;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param  int  $index
     */
    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param  array  $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }


}
