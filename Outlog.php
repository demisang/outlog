<?php

namespace demi\outlog;

class Outlog
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';

    public static function error()
    {

    }

    public static function warning()
    {

    }

    public static function info()
    {

    }

    public static function log($message = '', $type = self::TYPE_INFO)
    {

    }

    public static function logException(\Exception $exception, $type = self::TYPE_INFO)
    {
        $provider = new ExceptionProvider($exception, $type);

        return static::submit($provider);
    }

    protected static function submit(ExceptionProvider $exceptionProvider)
    {
        \demi\helpers\VD::dump($exceptionProvider->getData());

        return true;
    }
}