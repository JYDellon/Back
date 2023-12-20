<?php

namespace App\Service;

class ServiceResult
{
    private $success;
    private $message;
    private $statusCode;
    private $data;

    public function __construct($success, $message = null, $statusCode = null, $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    public function isSuccess()
    {
        return $this->success;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getData()
    {
        return $this->data;
    }

    public static function createSuccess($message = null, $statusCode = null, $data = null)
    {
        return new self(true, $message, $statusCode, $data);
    }

    public static function createError($message = null, $statusCode = null, $data = null)
    {
        return new self(false, $message, $statusCode, $data);
    }
}
