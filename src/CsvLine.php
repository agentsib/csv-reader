<?php

namespace AgentSIB\CsvReader;

class CsvLine implements \ArrayAccess, \Iterator
{
    /** @var string[] */
    private $values;
    /** @var string[] */
    private $headers;
    /** @var int */
    private $position;

    /**
     * @param string[] $values
     * @param string[] $headers
     */
    public function __construct(array $values = [], array $headers = [])
    {
        $this->values = $values;
        $this->headers = $headers;
        $this->position = 0;
    }

    /**
     * @return string[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string|integer $offset
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return !is_null($this->findValueIndex($offset));
    }

    /**
     * @param string|integer $offset
     * @return string|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $index = $this->findValueIndex($offset);

        return !is_null($index) && isset($this->values[$index]) ? $this->values[$index] : null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $index = $this->findValueIndex($offset);

        if (!is_null($index) && isset($this->values[$index])) {
            $this->values[$index] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->offsetSet($offset, null);
    }

    private function findValueIndex($offset)
    {
        $index = null;
        if (in_array($offset, $this->headers)) {
            $index = array_search($offset, $this->headers);
        }
        if (is_null($index)) {
            $index = $offset;
        }

        return $index;
    }

    /**
     * @return string|null
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->values[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->position++;
    }

    /**
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return isset($this->headers[$this->position]) ? $this->headers[$this->position] : $this->position;
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        $countHeaders = count($this->headers);
        if ($countHeaders > 0 && $this->position >= $countHeaders) {
            return false;
        }
        return isset($this->values[$this->position]);
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }
}
