<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteAccountDTO
{
    /**
     * @Assert\NotBlank(message="Password is required")
     */
    private string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isDataValid(): bool
    {
        // You might want to add additional validation logic here if needed
        // For now, it checks if the password is not empty
        return !empty($this->password);
    }
}
