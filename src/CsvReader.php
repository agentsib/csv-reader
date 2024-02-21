<?php

namespace AgentSIB\CsvReader;

class CsvReader implements \Iterator
{
    /** @var resource */
    private $handle;
    /** @var string */
    private $delimiter;
    /** @var string */
    private $enclosure;
    /** @var string */
    private $escape;
    /** @var boolean */
    private $parseHeaders;
    /** @var bool */
    private $clearBom;

    /** @var string[] */
    private $current;
    /** @var integer */
    private $position;
    /** @var string[] */
    private $headers;

    /**
     * @param resource $handle
     * @param bool|string[] $headers
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($handle, $headers = true, $delimiter = ',', $enclosure = '"', $escape = '\\', $clearBom = false)
    {
        if (!is_resource($handle)) {
            throw new \LogicException('Handler is not resource');
        }
        $this->handle = $handle;
        $this->parseHeaders = $headers === true;
        if (is_array($headers)) {
            $this->headers = $headers;
        } elseif ($headers === false) {
            $this->headers = [];
        }
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->clearBom = $clearBom;

        $this->rewind();
    }

    /**
     * @param array $headers
     */
    public function replaceHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $header
     *
     * @return bool
     */
    public function hasHeader($header)
    {
        return in_array($header, $this->headers);
    }

    /**
     * @return CsvLine|bool
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->current ? new CsvLine($this->current, $this->headers) : false;
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->current = $this->readLine();

        if ($this->current !== false) {
            $this->position++;
        }
    }

    /**
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->current !== false;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        rewind($this->handle);

        $this->position = -1;
        if ($this->parseHeaders) {
            $parsedHeaders = $this->readLine();
            if (is_null($this->headers)) {
                $this->headers = $parsedHeaders;
            }
        }

        $this->next();
    }

    /**
     * @return array|false|null
     */
    protected function readLine()
    {
        if ($this->clearBom && $this->position < 0) {
            $line = fgets($this->handle);
            $line = str_getcsv($this->removeBom($line), $this->delimiter, $this->enclosure, $this->escape);
        } else {
            $line = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape);
        }

        return $line;
    }

    private function removeBom($string)
    {
        $bom = pack('H*', 'EFBBBF');

        return preg_replace('/^' . $bom . '/', '', $string);
    }
}
