<?php


namespace Davajlama\AntLog\Utils;


class UnitsFormatter
{

    /**
     * @param int $bytes
     * @return string
     */
    public static function formatBytes($bytes)
    {
        $parts = [];

        $megabytes = 0;
        if($bytes >= 1048576) { // 1024 * 1024
            $megabytes = floor($bytes / 1048576);
            $parts[] = $megabytes . 'MB';
            $bytes -= $megabytes * 1048576;
        }

        if($megabytes || $bytes >= 1024) {
            $kilobytes = floor($bytes / 1024);
            $parts[] = $kilobytes . 'KB';
            $bytes -= $kilobytes * 1024;
        }

        $parts[] = $bytes . 'B';

        return implode(' ', $parts);
    }

    /**
     * @param int $seconds
     * @return string
     */
    public static function formatDuration($seconds)
    {
        $parts = [];

        $hours = 0;
        if($seconds >= 3600) {
            $hours = floor($seconds / 3600);
            $parts[] = $hours . 'h';
            $seconds -= $hours * 3600;
        }

        if($hours || $seconds >= 60) {
            $minutes = floor($seconds / 60);
            $parts[] = $minutes . 'm';
            $seconds -= $minutes * 60;
        }

        $parts[] = $seconds . 's';

        return implode(' ', $parts);
    }

}