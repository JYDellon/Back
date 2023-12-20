<?php

namespace App\DTO\Response;

class SuccessResponseDTO
{
    private $message;
    private $statusCode;

    public function __construct($message, $statusCode)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
