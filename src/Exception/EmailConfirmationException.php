<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class EmailConfirmationException extends HttpException
{
    private $recipientEmail;

    public function __construct($recipientEmail,
     $message = 'Échec de l\'envoi de l\'e-mail de confirmation',
      $code = 0, \Throwable $previous = null)
    {
        // Assurez-vous que $code est un entier
        $code = (int) $code;

        parent::__construct(500, $message, $previous, [], $code);
        $this->recipientEmail = $recipientEmail;
    }

    public function getRecipientEmail()
    {
        return $this->recipientEmail;
    }
}
