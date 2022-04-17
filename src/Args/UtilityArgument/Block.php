<?php

namespace Args\UtilityArgument;

abstract class Block
{
    protected self $parent;
    protected bool $isOptional;

    /**
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * @param  bool  $isOptional
     */
    public function setIsOptional(bool $isOptional): void
    {
        $this->isOptional = $isOptional;
    }

    /**
     * @return Block
     */
    public function getParent(): Block
    {
        return $this->parent;
    }

    /**
     * @param  Block  $parent
     */
    public function setParent(Block $parent): void
    {
        $this->parent = $parent;
    }
}
