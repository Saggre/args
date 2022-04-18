<?php

namespace Args\Loader;

class InputArgumentValue
{
    /**
     * @var string|int|bool|float
     */
    protected $value;
    protected int $index;

    /**
     * @param  string|int|bool|float  $value
     */
    public function __construct($value, int $index)
    {
        if (is_float($value)) {
            $this->value = (float)$value;
        } elseif (is_int($value)) {
            $this->value = (int)$value;
        } elseif (is_bool($value)) {
            $this->value = (bool)$value;
        } else {
            $this->value = (string)$value;
        }

        $this->index = $index;
    }

    /**
     * @return string|int|bool|float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param  string|int|bool|float  $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}
