<?php

declare(strict_types=1);

namespace Mistralys\X4\ExtractedData;

class Console
{
    private static bool $enabled = true;

    public static function setEnabled(bool $enabled) : void
    {
        self::$enabled = $enabled;
    }

    public static function header(string $message, ...$args) : void
    {
         if(!self::$enabled) {
            return;
        }

        if(!empty($args)) {
            $message = vsprintf($message, $args);
        }

        echo str_repeat('-', 60) . PHP_EOL;
        echo $message . PHP_EOL;
        echo str_repeat('-', 60) . PHP_EOL;
        echo PHP_EOL;
    }

    public static function line1(string $message, ...$args) : void
    {
        self::line('- '.$message, ...$args);
    }

    public static function line2(string $message, ...$args) : void
    {
        self::line('  - '.$message, ...$args);
    }

    public static function line(string $message, ...$args) : void
    {
        if(!self::$enabled) {
            return;
        }

        if(!empty($args)) {
            $message = vsprintf($message, $args);
        }

        echo $message . PHP_EOL;
    }

    public static function nl() : void
    {
        if(!self::$enabled) {
            return;
        }

        echo PHP_EOL;
    }
}
