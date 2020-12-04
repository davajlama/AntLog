<?php


namespace Davajlama\AntLog\Sql;


use Davajlama\AntLog\Storage\StorageInterface;
use Davajlama\AntLog\Utils\ArrayHelper;
use Davajlama\AntLog\Utils\Colorizer;
use Davajlama\AntLog\Utils\Output;

class Viewer
{
    /** @var StorageInterface */
    private $storage;

    /** @var ArrayHelper|Record[] */
    private $list;

    /** @var Output */
    private $output;

    /** @var Colorizer */
    private $colorizer;

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
    }

    public function viewStats()
    {
        $list = $this->load();
        
        $output = $this->getOutput();
        $output->writeHeadline("STATS");

        $output->writeLine("Queries count: " . $this->getColorizer()->green($list->count()));


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