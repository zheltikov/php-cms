<?php

namespace Zheltikov\Cms\Core;

/**
 *
 */
trait NamedSingletonTrait
{
    /**
     * @var static[]
     */
    private static $instances = [];

    /**
     * @return static
     */
    public static function getInstance(string $name = 'default', ...$args): self
    {
        if (!array_key_exists($name, static::$instances)) {
            static::$instances[$name] = new static($name, ...$args);
        }

        return static::$instances[$name];
    }

    /**
     *
     */
    private function __construct(string $name = 'default', ...$args)
    {
        static::$instances[$name] = $this;
        if (method_exists($this, '__init')) {
            $this->__init($name, ...$args);
        }
    }

    /**
     *
     */
    private function __init(): void
    {
    }
}
