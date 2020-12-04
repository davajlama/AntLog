<?php


namespace Davajlama\AntLog\Sql;


class Record
{
    public $type = 'sql';
    public $query;
    public $time;
    public $runner;
    public $session;
    public $api;

    public function toArray()
    {
        return (array)$this;
    }
}