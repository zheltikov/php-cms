<?php

(function () {
    foreach (
        [
            __DIR__ . '/../../../autoload.php',
            __DIR__ . '/../../vendor/autoload.php',
            __DIR__ . '/../vendor/autoload.php',
        ] as $file
    ) {
        if (file_exists($file)) {
            require_once($file);
            return;
        }
    }

    fwrite(
        STDERR,
        "You need to set up the project dependencies using Composer:\n\n    $ composer install\n\nYou can learn all about Composer on https://getcomposer.org/.\n"
    );
    die(1);
})();
