<?php

namespace Args\UtilityArgument;

abstract class Block
{
    protected self $parent;
    protected bool $isOptional;
    protected bool $isRepeating;

    /**
     * @param  bool  $isOptional
     * @param  bool  $isRepeating
     */
    public function __construct(bool $isOptional, bool $isRepeating)
    {
        $this->isOptional  = $isOptional;
        $this->isRepeating = $isRepeating;
    }

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
     * @return bool
     */
    public function isRepeating(): bool
    {
        return $this->isRepeating;
    }

    /**
     * @param  bool  $isRepeating
     */
    public function setIsRepeating(bool $isRepeating): void
    {
        $this->isRepeating = $isRepeating;
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
