<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class EmailSendException extends HttpException
{
    public function __construct(string $message = null,
     \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        parent::__construct(500, $message, $previous, $headers, $code);
    }
}
