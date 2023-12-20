<?php

namespace App\DTO\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    /**
     * @Assert\NotBlank(message="Le nom est requis.")
     * @Assert\Length(min=2, max=50)
     */
    public string $lastName;

    /**
     * @Assert\NotBlank(message="Le prénom est requis.")
     * @Assert\Length(min=2, max=50)
     */
    public string $firstName;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer l'objet de votre message.")
     */
    public string $object = '';

    /**
     * @Assert\NotBlank(message="Veuillez remplir votre message.")
     */
    public string $message;

    /**
     * @Assert\Email(message="Invalid email format")
     * @Assert\NotBlank(message="L'e-mail est requis.")
     * @Assert\Email()
     */
    public string $email;

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    private function __construct(string $lastName,
     string $firstName, string $object, string $message, string $email)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->object = $object;
        $this->message = $message;
        $this->email = $email;
    }

    public static function createFromRequest(Request $request): self
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            $data['lastName'] ?? '',
            $data['firstName'] ?? '',
            $data['object'] ?? '',
            $data['message'] ?? '',
            $data['email'] ?? '',
            
        );
    }
}
