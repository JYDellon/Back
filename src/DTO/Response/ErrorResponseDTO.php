<?php


namespace App\DTO\Response;

class ErrorResponseDTO
{
    private $message;
    private $errorCode;
    private $statusCode;

    public function __construct($message, $errorCode, $statusCode)
    {
        $this->message = $message;
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
