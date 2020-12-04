<?php


namespace Davajlama\AntLog\Sql;


use Davajlama\AntLog\Storage\StorageInterface;

class Logger
{
    /** @var StorageInterface */
    private $storage;

    /**
     * Logger constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Record $record
     */
    public function log(Record $record)
    {
        $this->storage->store($record->type, $record->runner, $record->toArray());
    }
}