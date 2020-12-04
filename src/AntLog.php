<?php

namespace Davajlama\AntLog;

use Davajlama\AntLog\Sql\Logger;
use Davajlama\AntLog\Sql\Record;
use Davajlama\AntLog\Sql\Viewer;
use Davajlama\AntLog\Storage\StorageInterface;

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
            $this->url = PHP_SAPI === 'cli' ? 'cli' : $_SERVER['QUERY_STRING'];
        }

        return $this->url;
    }

    protected function round($number)
    {
        return round($number, 4);
    }

    public function stats()
    {
        $list = $this->parse();
        $this->writeLine("Queries: " . TextColor::colorize(count($list), 'green'));

        $uniques = array_map(function($item){
            return $item->query;
        }, $list);

        $this->writeLine("Unique queries: " . TextColor::colorize(count(array_unique($uniques)), 'green'));

        $uniqueSessions = array_map(function($item){
            return $item->session;
        }, $list);

        $this->writeLine("Unique sessions: " . TextColor::colorize(count(array_unique($uniqueSessions)), 'green'));

        $runners = array_map(function($item){
            return $item->runner;
        }, $list);

        $this->writeLine("Unique runners: " . TextColor::colorize(count(array_unique($runners)), 'green'));

        $uniqueUrls = array_map(function($item){
            return $item->url;
        }, $list);

        $this->writeLine("Unique urls: " . TextColor::colorize(count(array_unique($uniqueUrls)), 'green'));

        $this->writeBreak();
    }

    protected function writeLine($string)
    {
        echo $string . PHP_EOL;
    }

    protected function writeBreak()
    {
        echo '============BREAK============' . PHP_EOL . PHP_EOL;
    }

    public function topRunners()
    {
        $runners = [];
        foreach($this->parse() as $item) {
            if(!isset($runners[$item->runner])) {
                $runners[$item->runner] = (object)[
                    'runner' => $item->runner,
                    'url' => $item->url,
                    'time' => 0,
                ];
            }

            $runners[$item->runner]->time += $item->time;
        }

        usort($runners, function($l, $r){
            return $l->time < $r->time;
        });

        $limit = 10;
        $i = 0;
        foreach($runners as $item) {
            echo TextColor::colorize($this->round($item->time), 'green') . " : " . $item->runner . ' ' . $item->url;
            echo PHP_EOL . PHP_EOL;

            if($i++ > $limit) {
                break;
            }
        }

        $this->writeBreak();
    }

    public function topQueries()
    {
        $list = $this->parse();
        usort($list, function($l, $r){
            return $l->time < $r->time;
        });

        $limit = 10;
        $i = 0;
        foreach($list as $item) {
            echo TextColor::colorize($this->round($item->time), 'green') . " : " . $this->format($item->query);
            echo PHP_EOL . PHP_EOL;

            if($i++ > $limit) {
                break;
            }
        }

        $this->writeBreak();
    }

    public function sameQueries()
    {
        $list = $this->parse();

        $queries = [];
        foreach($list as $item) {
            $query = $this->replace($item->query);
            $query = $this->format($query);
            if(!isset($queries[$query])) {
                $queries[$query] = (object)[
                    'query' => $query,
                    'count' => 0,
                    'time'  => 0,
                ];
            }

            $queries[$query]->count++;
            $queries[$query]->time += $item->time;
        }

        usort($queries, function($l, $r){
            return $l->time < $r->time;
        });

        $i = 0;
        $limit = 10;
        foreach($queries as $item) {
            echo TextColor::colorize($this->round($item->time) . 's' . ' / ' . $item->count . 'x', 'green') . " : $item->query";
            echo PHP_EOL . PHP_EOL;

            if($i++ > $limit) {
                break;
            }
        }

        $this->writeBreak();

        usort($queries, function($l, $r){
            return $l->count < $r->count;
        });

        $i = 0;
        $limit = 10;
        foreach($queries as $item) {
            echo TextColor::colorize($item->count . 'x' . ' / ' . $this->round($item->time) . 's', 'green') . " : $item->query";
            echo PHP_EOL . PHP_EOL;

            if($i++ > $limit) {
                break;
            }
        }

        $this->writeBreak();
    }
}