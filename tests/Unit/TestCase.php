<?php

namespace Args\Test\Unit;

use Args\Loader;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Loader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new Loader();
    }
}
