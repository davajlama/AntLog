<?php


namespace Davajlama\AntLog\Utils;


class Colorizer
{
    private static $fgColors = [
        'black' => '0;30',
        'green' => '0;32',
        'red'   => '0;31',
        'white' => '1;37',
        'yellow' => '1;33',
    ];

    private static $bgColors = [
        'black' => '40',
        'green' => '42'
    ];

    /**
     * @param string $string
     * @param string|null $fg
     * @param string|null $bg
     * @return string
     */
    public function colorize($string, $fg = null, $bg = null)
    {
        $result = '';

        if($fg && array_key_exists($fg, self::$fgColors)) {
            $result .= "\033[" . self::$fgColors[$fg] . 'm';
        }

        if($bg && array_key_exists($bg, self::$bgColors)) {
            $result .= "\033[" . self::$bgColors[$bg] . 'm';
        }

        if($result) {
            $result .= $string . "\033[0m";
        } else {
            $result = $string;
        }

        return $result;
    }

    /**
     * @param string $string
     * @return string
     */
    public function green($string)
    {
        return $this->colorize($string, 'green');
    }

}