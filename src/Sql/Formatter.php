<?php

namespace Davajlama\AntLog\Sql;

class Formatter
{

    /**
     * @param string $query
     * @return string
     */
    public function clean($query)
    {
        $pattern = '~(["\'])(?:[^\1\\\\]|\\\\.)*?\1~';
        $query = preg_replace($pattern, '@value', $query);

        $pattern = '~([\s]|[=])\d+~';
        $query = preg_replace($pattern, '\1@value', $query);

        return $query;
    }

    /**
     * @param string $query
     * @return string
     */
    public function format($query)
    {
        $pattern = '~\n~';
        $query = preg_replace($pattern, ' ', $query);

        $pattern = '~\s+~';
        $query = preg_replace($pattern, ' ', $query);

        return trim($query);
    }

}