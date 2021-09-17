<?php

namespace Zheltikov\Cms;

use Throwable;
use Zheltikov\Cms\Core\SingletonTrait;

/**
 *
 */
class App
{
    use SingletonTrait;

    // -----------------------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function run(): void
    {
        try {
            Core\Config::getInstance()->load();
            
            Core\Router::getInstance()->init();
            Core\Router::getInstance()->run();
        } catch (Throwable $error) {
            if ($error instanceof Core\Exception) {
                echo $error, "\n";
            } else {
                echo $error->getMessage(), "\n";
            }
        }

        exit();
    }
}
