<?php

namespace demi\outlog;

class ExceptionProvider
{
    public $exception;
    private $_title;
    private $_url;
    private $_message;
    private $_trace;

    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function getData()
    {
        
    }
}