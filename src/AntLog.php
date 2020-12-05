<?php

namespace Davajlama\AntLog;

use Davajlama\AntLog\Sql\Logger;
use Davajlama\AntLog\Sql\Record;
use Davajlama\AntLog\Sql\Viewer;
use Davajlama\AntLog\Storage\StorageInterface;
use Davajlama\AntLog\Utils\BlindColorizer;

class AntLog
{
    /** @var AntLog */
    private static $self;

    /** @var StorageInterface */
    private $storage;

    /** @var string */
    private $session;

    /** @var string */
    private $runner;

    /** @var string */
    private $url;

    /** @var Logger */
    private $sqlLogger;

    /**
     * AntLog constructor.
     * @param StorageInterface $storage
     */
    protected function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param StorageInterface|null $storage
     * @return AntLog
     */
    public static function create(StorageInterface $storage = null)
    {
        if(!isset(self::$self)) {
            self::$self = new self($storage);
        }

        return self::$self;
    }

    /**
     * @param string $query
     * @param float|int $time
     */
    public static function logSql($query, $time)
    {
        $self = self::create();

        $record = new Record((object)[
            'query'     => $query,
            'time'      => $time,
            'runner'    => $self->getRunner(),
            'session'   => $self->getSession(),
            'api'       => $self->getUrl(),
        ]);

        $self->getSqlLogger()->log($record);
    }

    public function view($filter)
    {
        $viewer = new Viewer($this->storage);

        if($filter) {
            $viewer->filterByRunner($filter);
        }

        $viewer->view();
    }

    /**
     * @return Logger
     */
    public function getSqlLogger()
    {
        if($this->sqlLogger === null) {
            $this->sqlLogger = new Logger($this->storage);
        }

        return $this->sqlLogger;
    }

    /**
     * @return string
     */
    protected function getSession()
    {
        if(!isset($this->session)) {
            $this->session = PHP_SAPI === 'cli' ? 'cli' : md5(session_id());
        }

        return $this->session;
    }

    /**
     * @return string
     */
    protected function getRunner()
    {
        if(!isset($this->runner)) {
            $this->runner = uniqid();
        }

        return $this->runner;
    }

    /**
     * @return string
     */
    protected function getUrl()
    {
        if(!isset($this->url)) {
            //var_dump($_SERVER);exit;
            $this->url = PHP_SAPI === 'cli' ? 'cli' : $_SERVER['REQUEST_URI'];
        }

        return $this->url;
    }

}