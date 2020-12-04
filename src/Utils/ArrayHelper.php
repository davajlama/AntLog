<?php


namespace Davajlama\AntLog\Utils;


use Exception;
use Traversable;

class ArrayHelper implements \ArrayAccess, \IteratorAggregate
{
    /** @var array */
    private $list = [];

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