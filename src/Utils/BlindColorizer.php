<?php


namespace Davajlama\AntLog\Utils;


class BlindColorizer extends Colorizer
{

    /**
     * @param string $string
     * @param null $fg
     * @param null $bg
     * @return string
     */
    public function colorize($string, $fg = null, $bg = null)
    {
        return $string;
    }

}