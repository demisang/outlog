<?php

namespace demi\outlog;

class ExceptionProvider
{
    /**
     * @var \Exception
     */
    public $exception;
    private $_type;
    private $_title;
    private $_url;
    private $_message;
    /**
     * @var TraceItem[]
     */
    private $_trace = [];

    public function __construct(\Exception $exception, $type = Outlog::TYPE_INFO)
    {
        $this->exception = $exception;

        $this->_type = $type;
        $this->_title = get_class($exception);
        $this->_message = $exception->getMessage();
        if (!empty($_SERVER['REQUEST_URI'])) {
            $this->_url = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'];
        } else {
            $this->_url = $this->exception->getFile() . ':' . $this->exception->getLine();
        }

        foreach ($exception->getTrace() as $trace) {
            $file = isset($trace['file']) ? $trace['file'] : $exception->getFile();
            $line = isset($trace['line']) ? $trace['line'] : $exception->getLine();
            $function = isset($trace['function']) ? $trace['function'] : null;
            $class = isset($trace['class']) ? $trace['class'] : null;
            $args = isset($trace['args']) ? $trace['args'] : [];

            $this->_trace[] = new TraceItem($file, $line, $function, $class, $args);
        }
    }

    public function getData()
    {
        $result = [
            'type' => $this->_type,
            'title' => $this->_title,
            'message' => $this->_message,
            'url' => $this->_url,
            'hash' => $this->generateHash(),
            'trace' => array(),
        ];

        foreach ($this->_trace as $traceItem) {
            $result['trace'][] = $traceItem->getData();
        }

        return $result;
    }

    /**
     * Generate unique exception hash based on file lastmod & filepath+line
     *
     * @return string
     */
    protected function generateHash()
    {
        $filesList = [];
        foreach ($this->_trace as $traceItem) {
            $filesList[] = $traceItem->file;
        }

        $veryLastTime = 0;
        $filesList = array_unique($filesList);
        foreach ($filesList as $filePath) {
            $lastmodTime = @filemtime($filePath);
            if ($lastmodTime && $lastmodTime > $veryLastTime) {
                $veryLastTime = $lastmodTime;
            }
        }

        return md5($this->exception->getFile() . $this->exception->getLine() . $veryLastTime);
    }
}