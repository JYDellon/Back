<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserResetPasswordException extends HttpException
{
    public function __construct(string $message = null,
     \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        parent::__construct(400, $message, $previous, $headers, $code);
    }
}

