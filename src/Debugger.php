<?php


namespace Davajlama\AntLog;


use Davajlama\AntLog\Utils\UnitsFormatter;

class Debugger
{
    public static function dumpMemory()
    {
        echo PHP_EOL . PHP_EOL
            . 'Memory usage: ' . self::memoryUsage() . PHP_EOL
            . 'Memory usage peak: ' . self::memoryUsagePeak() . PHP_EOL;
    }

    public static function dumpMemoryUsage()
    {
        echo PHP_EOL . PHP_EOL . 'Memory usage: ' . self::memoryUsage() . PHP_EOL;
    }

    public static function dumpMemoryUsagePeak()
    {
        echo PHP_EOL . PHP_EOL . 'Memory usage peak: ' . self::memoryUsagePeak() . PHP_EOL;
    }

    public static function memoryUsage()
    {
        $bytes = memory_get_usage();
        return UnitsFormatter::formatBytes($bytes);
    }

    public static function memoryUsagePeak()
    {
        $bytes = memory_get_peak_usage();
        return UnitsFormatter::formatBytes($bytes);
    }
}