<?php

namespace Args\Loader;

class InputOption extends InputElement
{
    protected string $key;

    public function __construct(int $index, string $key, array $values)
    {
        parent::__construct($index, $values);

        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param  string  $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }


}