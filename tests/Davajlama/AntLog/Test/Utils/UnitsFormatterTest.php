<?php


namespace Davajlama\AntLog\Test\Utils;


use Davajlama\AntLog\Utils\UnitsFormatter;

class UnitsFormatterTest extends \BaseTestCase
{

    public function testFormatBytes()
    {
        $this->assertSame('0B', UnitsFormatter::formatBytes(0));
        $this->assertSame('512B', UnitsFormatter::formatBytes(512));
        $this->assertSame('1KB 0B', UnitsFormatter::formatBytes(1024));
        $this->assertSame('1KB 512B', UnitsFormatter::formatBytes(1024 + 512));
        $this->assertSame('1MB 0KB 0B', UnitsFormatter::formatBytes(1024 * 1024));
        $this->assertSame('1MB 1KB 512B', UnitsFormatter::formatBytes((1024 * 1024) + 1024 + 512));
    }

    public function testFormatDuration()
    {
        $this->assertSame('0s', UnitsFormatter::formatDuration(0));
        $this->assertSame('30s', UnitsFormatter::formatDuration(30));
        $this->assertSame('1m 0s', UnitsFormatter::formatDuration(60));
        $this->assertSame('1m 30s', UnitsFormatter::formatDuration(90));
        $this->assertSame('1h 0m 0s', UnitsFormatter::formatDuration(3600));
        $this->assertSame('1h 30m 30s', UnitsFormatter::formatDuration(5430));
    }

}