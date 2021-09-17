#!/usr/bin/env php
<?php

use Zheltikov\Cms\Core\Config;

require_once(__DIR__ . '/../src/bin_helper.php');

function main(): void
{
    // Show some usage info
    if ($_SERVER['argc'] < 2) {
        HELP:
        fputs(
            STDERR,
            sprintf(
                <<<HELP
Usage: %s PATH
Where PATH is the path to your configuration file on disk.

HELP,
                $_SERVER['argv'][0]
            )
        );
        exit();
    }

    // Work with their file
    $their_file = getcwd() . '/' . $_SERVER['argv'][1];

    $realpath = realpath($their_file);
    if ($realpath === false) {
        fputs(STDERR, sprintf("Could not resolve file path: %s\n", $their_file));
        goto HELP;
    }

    // Work with our file
    $our_file = __DIR__ . '/../' . Config::CONFIG_FILENAME;

    if (file_exists($our_file)) {
        // Delete the link if it already exists
        unlink($our_file);
    }

    $link = symlink($realpath, $our_file);
    if ($link === false) {
        fputs(STDERR, sprintf("Could create symbolic link: %s\n", $our_file));
        goto HELP;
    }

    // Everything is great!
    echo "Done\n";
}

main();
