<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    /**
     * @Assert\NotBlank(message="First name is required")
     * @Assert\Length(min=2, max=50)
     */
    public string $firstName;

    /**
     * @Assert\NotBlank(message="Last name is required")
     * @Assert\Length(min=2, max=50)
     */
    public string $lastName;

    /**
     * @Assert\Email(message="Invalid email format")
     * @Assert\NotBlank(message="Email is required")
     * @Assert\Email()
     */
    public string $email;

    /**
     * @Assert\NotBlank(message="Password is required")
     * @Assert\Password()
     * @Assert\Length(min=8)
     */
    public string $password;

    private function __construct(string $firstName,
     string $lastName, string $email, string $password, string $confirmPassword)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
    }

    public ?string $confirmPassword = null;


    public static function createFromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        // Ajoutez des validations supplémentaires si nécessaire.

        return new self(
            $data['firstName'] ?? '',
            $data['lastName'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['confirmPassword'] ?? null
        );
    }
}
