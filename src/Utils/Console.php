<?php


namespace Davajlama\AntLog\Utils;


use Davajlama\AntLog\AntLog;
use Davajlama\AntLog\Storage\FileStorage;

class Console
{
    public function run($args)
    {
        $path   = isset($args[1]) ? $args[1] : null;
        $filter = isset($args[2]) ? $args[2] : null;

        $antlog = AntLog::create(new FileStorage($path));
        $antlog->view($filter);
    }
}