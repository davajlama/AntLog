<?php

namespace Davajlama\AntLog\Utils;

use Traversable;

class ArrayHelper implements \ArrayAccess, \IteratorAggregate
{
    /** @var array */
    private $list = [];

    /**
     * ArrayHelper constructor.
     * @param array $list
     */
    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    /**
     * @param int $count
     * @return ArrayHelper
     */
    public function part($count)
    {
        return new self(array_slice($this->list, 0, $count));
    }

    /**
     * @param callable $callback
     * @return ArrayHelper
     */
    public function sort(callable $callback)
    {
        $list = $this->list;
        usort($list, $callback);
        return new self($list);
    }

    /**
     * @param string $column
     * @return ArrayHelper
     */
    public function sortDesc($column)
    {
        return $this->sort(function($a, $b) use($column){
            return $a->$column < $b->$column;
        });
    }

    /**
     * @param callable $callback
     * @return ArrayHelper
     */
    public function filter(callable $callback)
    {
        return new self(array_filter($this->list, $callback));
    }

    /**
     * @param callable $callback
     * @return ArrayHelper
     */
    public function map(callable $callback)
    {
        return new self(array_map($callback, $this->list));
    }

    /**
     * @return ArrayHelper
     */
    public function unique()
    {
        return new self(array_unique($this->list));
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * @param ArrayHelper $array
     * @return $this
     */
    public function append(ArrayHelper $array)
    {
        foreach($array as $item) {
            $this[] = $item;
        }

        return $this;
    }

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->list);
    }

    /**
     * @param mixed $offset
     * @return bool|void
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->list);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->list[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if($offset === null) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
    }

}