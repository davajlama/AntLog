<?php

namespace Davajlama;

class AntLog
{
    const FILE      = 'query-log.log';
    const DELIMETER = '<<|CDATA|DELIMETER>>';

    /** @var AntLog */
    private static AntLog $self;

    /** @var string */
    private static string $tempDir;

    /** @var string */
    private static string $session;

    /** @var string */
    private static string $runner;

    /** @var string */
    private static string $url;

    /**
     * SqlLogger constructor.
     * @param string $temp
     */
    public function __construct($tempDir = null)
    {
        if(!isset(self::$tempDir)) {
            if(!is_dir($tempDir)) {
                throw new \Exception("Temp path [$tempDir] not exists");
            }

            self::$tempDir = $tempDir;
        }
    }

    /**
     * @param string|null $tempDir
     * @return AntLog
     * @throws \Exception
     */
    public static function create(string $tempDir = null) : AntLog
    {
        if(!isset(self::$self)) {
            self::$self = new self($tempDir);
        }

        return self::$self;
    }


    public static function logSql(string $sql, $time, string $session = null, string $runner = null, string $url = null) : void
    {
        $session    = $session ? $session : self::getSession();
        $runner     = $runner ? $runner : self::getRunner();
        $url        = $url ? $url : self::getUrl();

        self::create()->log($sql, $time, $session, $runner, $url);
    }

    /**
     * @return string
     */
    protected static function getSession() : string
    {
        if(!isset(self::$session)) {
            self::$session = PHP_SAPI === 'cli' ? 'cli' : md5(session_id());
        }

        return self::$session;
    }

    /**
     * @return string
     */
    protected static function getRunner() : string
    {
        if(!isset(self::$runner)) {
            self::$runner = uniqid();
        }

        return self::$runner;
    }

    /**
     * @return string
     */
    protected static function getUrl() : string
    {
        if(!isset(self::$url)) {
            self::$url = PHP_SAPI === 'cli' ? 'cli' : $_SERVER['QUERY_STRING'];
        }

        return self::$url;
    }

    protected function log(string $query, $time, string $session = null, string $runner = null, string $url = null)
    {
        $this->write(json_encode((object)[
            'query'     => $query,
            'time'      => $time,
            'session'   => $session,
            'runner'    => $runner,
            'url'       => $url,
        ]));
    }

    protected function round($number)
    {
        return round($number, 4);
    }

    protected function write($string)
    {
        $data = $string . self::DELIMETER;
        file_put_contents(self::$tempDir . '/' . self::FILE, $data, FILE_APPEND);
    }

    protected function parse()
    {
        static $list;

        if(empty($list)) {
            $data = file_get_contents(self::$tempDir. '/' . self::FILE);
            $list = [];

            foreach(explode(self::DELIMETER, $data) as $json) {
                if($obj = json_decode($json)) {
                    $list[] = $obj;
                }
            }
        }

        return $list;
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

    public function format($query)
    {
        $pattern = '~\n~';
        $query = preg_replace($pattern, ' ', $query);

        $pattern = '~\s+~';
        $query = preg_replace($pattern, ' ', $query);

        return $query;
    }

    public function replace($query)
    {
        $pattern = '~(["\'])(?:[^\1\\\\]|\\\\.)*?\1~';
        $query = preg_replace($pattern, '@value', $query);

        $pattern = '~([\s]|[=])\d+~';
        $query = preg_replace($pattern, '\1@value', $query);
        return $query;
    }

    public function test()
    {
        $query  = "SELECT * FROM `users` WHERE name = 'david'";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->_test($expect, $this->replace($query));

        $query  = "SELECT * FROM `users` WHERE name = ''";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->_test($expect, $this->replace($query));

        $query  = 'SELECT * FROM `users` WHERE name = "david"';
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->_test($expect, $this->replace($query));

        $query  = 'SELECT * FROM `users` WHERE name = ""';
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->_test($expect, $this->replace($query));

        $query  = 'SELECT * FROM `users` WHERE name = "Teste escaped \" double quotes"';
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->_test($expect, $this->replace($query));

        $query  = "SELECT * FROM `users` WHERE name = 'Teste escaped \' double quotes'";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->_test($expect, $this->replace($query));

        $query  = "SELECT * FROM `users` WHERE name = 123";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->_test($expect, $this->replace($query));

        $query  = "SELECT * FROM `users` WHERE name2 = 123";
        $expect = "SELECT * FROM `users` WHERE name2 = @value";
        $this->_test($expect, $this->replace($query));

        $query  = "SELECT * FROM `users` WHERE name2=123";
        $expect = "SELECT * FROM `users` WHERE name2=@value";
        $this->_test($expect, $this->replace($query));

    }

    public function _test($expect, $query)
    {
        if($expect !== $query) {
            echo 'ERROR: ' . $query . PHP_EOL;
        }
    }
}

class TextColor
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

    public static function colorize($string, $fg = null, $bg = null)
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
}

function aaamquery($q, $ignoreErrors = false) {
    require_once __DIR__ . '/../../SqlLogger.php';
    $logger = new SqlLogger(__DIR__ . '/../../var/temp');
    return $logger->run(function() use($q, $ignoreErrors, $logger){

        $logger->start();

        $result = _mquery($q, $ignoreErrors);

        $logger->log($q);

        return $result;
    });
}

//PerformanceLogger::formatter('sql', SqlFormatter::create());
//PerforamnceLogger::logSql();
//PerformanceLogger::log()->run();

//retrun $this->log($query)->run(['callback', [$q, $ignoreErrors]]);