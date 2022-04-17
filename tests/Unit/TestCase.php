<?php

namespace Args\Test\Unit;

use Args\Loader;

/**
 * Base test case class for all unit tests.
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Argument loader instance.
     *
     * @var Loader
     */
    protected Loader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new Loader();
    }

    /**
     * Set global arguments ($argv).
     *
     * @param  array|string  $arguments  An array of arguments or an argument string (space separated).
     *
     * @return void
     */
    protected function setArgv($arguments)
    {
        if (is_string($arguments)) {
            $arguments = explode(' ', $arguments);
        }

        if ( ! is_array($arguments)) {
            throw new \InvalidArgumentException('Arguments must be an array or string');
        }

        global $argv;
        $argv = $arguments;
    }
}
