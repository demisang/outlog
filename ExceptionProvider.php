<?php

namespace demi\outlog;

class ExceptionProvider
{
    /**
     * @var \Exception
     */
    public $exception;
    public $basePath = '';
    private $_type;
    private $_title;
    private $_url;
    private $_message;
    /**
     * @var TraceItem[]
     */
    private $_trace = array();

    public function __construct(\Exception $exception, $type = Outlog::TYPE_INFO, $basePath = '')
    {
        $this->basePath = str_replace('\\', '/', $basePath);
        $this->exception = $exception;

        $this->_type = $type;
        $this->_title = get_class($exception);
        $this->_message = $exception->getMessage();
        if (!empty($_SERVER['REQUEST_URI'])) {
            $this->_url = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'];
        } else {
            $this->_url = $this->prepareFilePath($this->exception->getFile()) . ':' . $this->exception->getLine();
        }

        foreach ($exception->getTrace() as $trace) {
            $file = isset($trace['file']) ? $trace['file'] : $exception->getFile();
            $line = isset($trace['line']) ? $trace['line'] : $exception->getLine();
            $function = isset($trace['function']) ? $trace['function'] : null;
            $class = isset($trace['class']) ? $trace['class'] : null;
            $args = isset($trace['args']) ? $trace['args'] : array();

            $this->_trace[] = new TraceItem($file, $line, $function, $class, $args, $this->basePath);
        }
    }

    public function getData()
    {
        $result = array(
            'type' => $this->_type,
            'title' => $this->_title,
            'message' => $this->_message,
            'url' => $this->_url,
            'hash' => $this->generateHash(),
            'user_id' => null,
            'user_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
            'time' => time(),
            'trace' => array(),
        );

        foreach ($this->_trace as $traceItem) {
            $result['trace'][] = $traceItem->getData();
        }

        return $result;
    }

    /**
     * Prepate file path for submit
     *
     * @param string $originalFilePath
     *
     * @return string
     */
    protected function prepareFilePath($originalFilePath)
    {
        $originalFilePath = str_replace('\\', '/', $originalFilePath);

        return ltrim(str_replace($this->basePath, '', $originalFilePath), '/');
    }

    /**
     * Generate unique exception hash based on file lastmod & filepath+line
     *
     * @return string
     */
    protected function generateHash()
    {
        $filesList = array();
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

        return md5($this->_title . $this->_message . $this->_url);
    }
}