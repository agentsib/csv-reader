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
    public function __construct($handle, $headers = true, $delimiter = ',', $enclosure = '"', $escape = '\\')
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
     * @return CsvLine
     */
    public function current()
    {
        return $this->current ? new CsvLine($this->current, $this->headers) : false;
    }

    /**
     *
     */
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
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->current !== false;
    }

    /**
     *
     */
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
        return fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape);
    }
}