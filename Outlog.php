<?php

namespace demi\outlog;

class Outlog
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    public $token;
    public $basePath;

    public function error()
    {

    }

    public function warning()
    {

    }

    public function info()
    {

    }

    public function log($message = '', $type = self::TYPE_INFO)
    {

    }

    public function logException(\Exception $exception, $type = self::TYPE_INFO)
    {
        $provider = new ExceptionProvider($exception, $type, $this->basePath);

        return static::submit($provider);
    }

    /**
     * Store error on the Outlog API
     *
     * @param \demi\outlog\ExceptionProvider $exceptionProvider
     *
     * @return bool
     */
    protected function submit(ExceptionProvider $exceptionProvider)
    {
        \demi\helpers\VD::dump($exceptionProvider->getData());

        $handle = curl_init('http://outlog.loc/log/submit');
        // curl_setopt($handle, CURLOPT_HTTPHEADER, []);
        curl_setopt($handle, CURLOPT_TIMEOUT, 20);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, [
            'project_token' => $this->token,
            'data' => json_encode($exceptionProvider->getData(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ]);

        return curl_exec($handle) !== false;
    }
}