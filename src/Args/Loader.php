<?php

namespace Args;

class Loader
{
    protected array $parsedArgs;

    public function __construct()
    {
        $this->parsedArgs = $this->parseArgs();
    }

    /**
     * @return array[]
     */
    public function getParsedArgs(): array
    {
        return $this->parsedArgs;
    }

    /**
     * Add an argument to argument array.
     *
     * @param array        $args Argument array.
     * @param string       $key Argument key.
     * @param array|string $value Argument value or values.
     *
     * @return void
     */
    public function addArg(array &$args, string $key, array|string $value)
    {
        if (empty($value)) {
            $value = true;
        }

        $value = is_array($value) ? $value : array($value);

        if ( ! isset($args[$key])) {
            $args[$key] = array();
        }

        $args[$key] = array_merge(
            $value,
            $args[$key]
        );
    }

    /**
     * Parse input params.
     *
     * @return array
     */
    protected function parseArgs(): array
    {
        global $argv;

        $args = array(
            'named' => array(),
            'short' => array(),
            'posit' => array(),
        );

        for ($i = 1; $i < count($argv); $i++) {
            if (preg_match('/^--([^=]+)=?(.*)/', $argv[$i], $m)) {
                $this->addArg($args['named'], $m[1], $m[2] ?? null);
            } elseif (preg_match('/^-([^=-]+)=?(.*)/', $argv[$i], $m)) {
                foreach (str_split($m[1]) as $char) {
                    $this->addArg($args['short'], $char, $m[2] ?? null);
                }
            } else {
                $args['posit'][] = $argv[$i];
            }
        }

        return $args;
    }

    /**
     * Get argument by name or shorthand.
     *
     * @param int|string  $arg Full argument name ex. --usage or positional argument index. Positional arguments are indexed from 0.
     * @param string|null $short_arg Shorthand argument name ex. -u.
     * @param int         $flags Parse options.
     *
     * @return string|array|null Argument value or array if there are multiple values or null if not found.
     */
    public function getArg(int|string $arg, ?string $short_arg = null, int $flags = GET_ARG_DEFAULT_FLAGS): array|string|null
    {
        list($named, $short, $posit) = array_values($this->getParsedArgs());

        if (is_numeric($arg)) {
            $index = $arg;

            if ($short_arg !== null) {
                throw new \RuntimeException("Argument $arg is not a valid argument name");
            }

            return $posit[$index] ?? null;
        }

        $result = array();

        if (isset($named[$arg])) {
            $this->addArg($result, $arg, $named[$arg]);
        }

        if ($short_arg && isset($short[$short_arg])) {
            $this->addArg($result, $arg, $short[$short_arg]);
        }

        if (($flags & UNWRAP_SINGLE_VALUE) && isset($result[$arg]) && count($result[$arg]) === 1) {
            return reset($result[$arg]);
        }

        $arg_count = isset($result[$arg]) ? count($result[$arg]) : 0;
        if (($flags & DISALLOW_MULTIPLE_VALUES) && $arg_count > 1) {
            $arg_name = "--$arg" . ($short_arg ? " or -$short_arg" : '');
            throw new \RuntimeException("Multiple values not allowed for argument $arg_name. $arg_count provided.");
        }

        return $result[$arg] ?? array();
    }

}
