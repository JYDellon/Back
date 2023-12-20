<?php

namespace App\DTO\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ModifyAccountDTO
{
    /**
     * @Assert\NotBlank(message="First name is required")
     * @Assert\Length(min=2, max=50
     */
    private ?string $firstName;

    /**
     * @Assert\NotBlank(message="Last name is required")
     * @Assert\Length(min=2, max=50
     */
    private ?string $lastName;

    /**
     * @Assert\NotBlank(message="Password is required")
     * @Assert\Password()
     * @Assert\Length(min=8)
     */
    private ?string $password;

    /**
     * @Assert\NotBlank(message="Current password is required for modification")
     * @Assert\Password()
     * @Assert\Length(min=8)
     */
    private ?string $newPassword;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function __construct(
        ?string $firstName,
        ?string $lastName,
        ?string $password,
        ?string $newPassword
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
        $this->newPassword = $newPassword;
    }


    public static function createFromRequest(Request $request): ModifyAccountDTO
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['firstName'] ?? null,
            $data['lastName'] ?? null,
            $data['password'] ?? null,
            $data['newPassword'] ?? null
        );
    }
}
