<?php


namespace Davajlama\AntLog\Sql;


use Davajlama\AntLog\Storage\StorageInterface;
use Davajlama\AntLog\Utils\ArrayHelper;
use Davajlama\AntLog\Utils\Colorizer;
use Davajlama\AntLog\Utils\Output;

class Viewer
{
    const COUNT = 5;

    /** @var StorageInterface */
    private $storage;

    /** @var ArrayHelper|Record[] */
    private $list;

    /** @var Output */
    private $output;

    /** @var Colorizer */
    private $colorizer;

    /** @var Formatter */
    private $formatter;

    /**
     * Viewer constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function view()
    {
        $this->viewStats();
        $this->viewTopRunners();
        $this->viewTopQueries();
        $this->viewTopSameQueries();
    }

    public function viewStats()
    {
        $list = $this->load();

        $output = $this->getOutput();
        $output->writeHeadline("STATS");

        $output->writeLine("Total queries count: " . $this->getColorizer()->red($list->count()));

        $uniqueQueries = $list->map(function(Record $record){ return $record->query; })->unique();
        $output->writeLine("Unique queries: " . $this->getColorizer()->red($uniqueQueries->count()));

        $uniqueSessions = $list->map(function(Record $record){ return $record->session; })->unique();
        $output->writeLine("Unique sessions: " . $this->getColorizer()->red($uniqueSessions->count()));

        $uniqueRunners = $list->map(function(Record $record){ return $record->runner; })->unique();
        $output->writeLine("Unique runners: " . $this->getColorizer()->red($uniqueRunners->count()));

        $uniqueApis = $list->map(function(Record $record){ return $record->api; })->unique();
        $output->writeLine("Unique apis: " . $this->getColorizer()->red($uniqueApis->count()));

        $uniquePatterns = $list->map(function(Record $record){
            return $this->getFormatter()->format($this->getFormatter()->clean($record->query));
        });

        $output->writeLine("Unique patterns: " . $this->getColorizer()->red($uniquePatterns->count()));
    }

    public function viewTopRunners()
    {
        $list = $this->load();

        $output = $this->getOutput();
        $output->writeHeadline("TOP RUNNERS");

        $aggregated = new ArrayHelper();
        foreach($list as $item) {
            if(!isset($aggregated[$item->runner])) {
                $aggregated[$item->runner] = (object)[
                    'runner'    => $item->runner,
                    'api'       => $item->api,
                    'time'      => 0,
                    'count'     => 0,
                ];
            }

            $aggregated[$item->runner]->count++;
            $aggregated[$item->runner]->time += $item->time;
        }
        
        $aggregated = $aggregated->sort(function($a, $b){ return $a->time < $b->time;})->part(self::COUNT);
        foreach($aggregated as $item) {
            $time   = $this->round($item->time);
            $runner = $item->runner;
            $api    = $this->getColorizer()->yellow(rawurldecode($item->api));
            $count  = $item->count;

            $output->writeLine($this->getColorizer()->red($time . 's / ' . $count . 'q') . ' ' . $runner . ' ' . $api);
        }
    }

    public function viewTopQueries()
    {
        $list = $this->load();

        $output = $this->getOutput();
        $output->writeHeadline('TOP QUERIES');

        $aggregated = $list->sortDesc('time')->part(self::COUNT);
        foreach($aggregated as $item) {
            $output->write($this->getColorizer()->red($this->round($item->time) . 's') . ' : ')
                    ->write($item->query . ' ')
                    ->write($this->getColorizer()->yellow(rawurldecode($item->api)))
                    ->writeBreak()
                    ->writeBreak();
        }
    }

    public function viewTopSameQueries()
    {
        $list = $this->load();

        $aggregated = new ArrayHelper();
        foreach($list as $item) {
            $query = $this->getFormatter()->format($this->getFormatter()->clean($item->query));
            if(!isset($aggregated[$query])) {
                $aggregated[$query] = (object)[
                    'query' => $query,
                    'time'  => 0,
                    'count' => 0,
                ];
            }

            $aggregated[$query]->count++;
            $aggregated[$query]->time += $item->time;
        }

        $output = $this->getOutput();
        $output->writeHeadline('TOP SAME QUERIES by time');
        
        $sorted = $aggregated->sortDesc('time');
        foreach($sorted as $item) {
            $output->write($this->getColorizer()->red($this->round($item->time). 's') . ' : ')
                        ->write($item->query)
                        ->writeBreak()
                        ->writeBreak();

        }

        $output->writeHeadline('TOP SAME QUERIES by count');

        $sorted = $aggregated->sortDesc('count');
        foreach($sorted as $item) {
            $output->write($this->getColorizer()->red($item->count . 'x') . ' : ')
                ->write($item->query)
                ->writeBreak()
                ->writeBreak();

        }

    }
    
    /**
     * @param float|int $number
     * @return float
     */
    protected function round($number)
    {
        return round($number, 4);
    }

    /**
     * @param $runner
     * @return $this
     */
    public function filterByRunner($runner)
    {
        $this->load();
        $this->list = $this->list->filter(function(Record $record) use($runner){
            return $record->runner === $runner;
        });

        return $this;
    }

    /**
     * @return Record[]
     */
    protected function load()
    {
        if($this->list === null) {
            $this->list = new ArrayHelper();
            foreach($this->storage->load('sql') as $item) {
                $this->list[] = new Record($item);
            }
        }

        return $this->list;
    }

    /**
     * @return Colorizer
     */
    public function getColorizer()
    {
        if($this->colorizer === null) {
            $this->colorizer = new Colorizer();
        }

        return $this->colorizer;
    }

    /**
     * @param Colorizer $colorizer
     * @return $this
     */
    public function setColorizer(Colorizer $colorizer)
    {
        $this->colorizer = $colorizer;
        return $this;
    }

    /**
     * @return Formatter
     */
    public function getFormatter()
    {
        if($this->formatter === null) {
            $this->formatter = new Formatter();
        }

        return $this->formatter;
    }

    /**
     * @return Output
     */
    public function getOutput()
    {
        if($this->output === null) {
            $this->output = new Output();
        }

        return $this->output;
    }

    /**
     * @param Output $output
     * @return $this
     */
    public function setOutput(Output $output)
    {
        $this->output = $output;
        return $this;
    }

}