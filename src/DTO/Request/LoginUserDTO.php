<?php


namespace App\DTO\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class LoginUserDTO
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=8)
     */
    public ?string $password = null;


    public static function createFromRequest(Request $request): LoginUserDTO
    {
        $data = json_decode($request->getContent(), true);

        // Validez et définissez les propriétés du DTO ici.
        $loginUserDTO = new self();
        $loginUserDTO->email = $data['email'];
        $loginUserDTO->password = $data['password'];

        return $loginUserDTO;
    }
}
