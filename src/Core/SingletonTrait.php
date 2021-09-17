<?php

namespace Zheltikov\Cms\Core;

/**
 *
 */
trait SingletonTrait
{
    /**
     * @var static|null
     */
    private static $instance = null;

    /**
     * @return static
     */
    public static function getInstance(...$args): self
    {
        if (static::$instance === null) {
            static::$instance = new static(...$args);
        }

        return static::$instance;
    }

    /**
     *
     */
    private function __construct(...$args)
    {
        static::$instance = $this;
        if (method_exists($this, '__init')) {
            $this->__init(...$args);
        }
    }

    /**
     *
     */
    private function __init(): void
    {
    }
}
