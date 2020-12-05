<?php


namespace Davajlama\AntLog\Sql;


/**
 * Class Record
 * @package Davajlama\AntLog\Sql
 *
 * @property-read string $type
 * @property-read string $query
 * @property-read float|int $time
 * @property-read string $runner
 * @property-read string $session
 * @property-read string $api
 */
class Record
{
    protected $type = 'sql';
    protected $query;
    protected $time;
    protected $runner;
    protected $session;
    protected $api;

    /**
     * Record constructor.
     * @param array $binds
     */
    public function __construct(\stdClass $binds)
    {
        $this->query    = $this->get($binds, 'query');
        $this->time     = $this->get($binds, 'time');
        $this->runner   = $this->get($binds, 'runner');
        $this->session  = $this->get($binds, 'session');
        $this->api      = $this->get($binds, 'api');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @param \stdClass $data
     * @param string $key
     * @return mixed
     */
    protected function get(\stdClass $data, $key)
    {
        if(isset($data->$key)) {
            return $data->$key;
        }
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }
}