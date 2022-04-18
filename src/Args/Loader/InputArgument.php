<?php

namespace Args\Loader;

class InputArgument
{
    public const TYPE_OPTION  = 'option';
    public const TYPE_OPERAND = 'operand';

    /**
     *
     *
     * @var InputArgumentValue[]
     */
    protected array $values = array();
    protected string $type;

    public function addValue(InputArgumentValue $value, string $type = self::TYPE_OPTION): void
    {
        $this->values[] = $value;
        $this->type     = $type;
    }

    /**
     * @return InputArgumentValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return array[]
     */
    public function getPrimitiveValues(): array
    {
        return array_map(
            function (InputArgumentValue $value) {
                return $value->getValue();
            },
            $this->values
        );
    }

    /**
     * @param  InputArgumentValue[]  $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }


}
