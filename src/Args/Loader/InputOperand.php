<?php

namespace Args\Loader;

class InputOperand extends InputElement
{
    protected int $operandIndex;

    public function __construct(int $index, int $operandIndex, array $values)
    {
        parent::__construct($index, $values);

        $this->operandIndex = $operandIndex;
    }

    /**
     * @return int
     */
    public function getOperandIndex(): int
    {
        return $this->operandIndex;
    }

    /**
     * @param  int  $operandIndex
     */
    public function setOperandIndex(int $operandIndex): void
    {
        $this->operandIndex = $operandIndex;
    }


}
