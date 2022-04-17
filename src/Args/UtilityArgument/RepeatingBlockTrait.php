<?php

namespace Args\UtilityArgument;

trait RepeatingBlockTrait
{
    protected int $minCount = 0;
    protected int $maxCount = 0;

    public function setMinCount(int $minCount): void
    {
        $this->minCount = $minCount;
    }

    public function setMaxCount(int $maxCount): void
    {
        $this->maxCount = $maxCount;
    }

    public function setExactCount(int $count): void
    {
        $this->setMaxCount($count);
        $this->setMinCount($count);
    }

    public function getMinCount(): int
    {
        return $this->minCount;
    }

    public function getMaxCount(): int
    {
        return $this->maxCount;
    }

    public function allowMany(): bool
    {
        return $this->maxCount > 1;
    }

    public function isRequired(): bool
    {
        return $this->minCount > 0;
    }

    public function isOptional(): bool
    {
        return ! $this->isRequired();
    }
}
