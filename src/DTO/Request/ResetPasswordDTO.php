<?php

namespace App\DTO\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordDTO
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email = null;

    public static function createFromRequest(Request $request): ResetPasswordDTO
    {
        $data = json_decode($request->getContent(), true);

        $resetPasswordDTO = new self();

        // Vérifiez si la clé 'email' existe dans le tableau $data avant d'y accéder
        if (isset($data['email'])) {
            $resetPasswordDTO->email = $data['email'];
        } else {
            // Gère le cas où 'email' n'est pas défini,
            // éventuellement en lançant une exception ou en renvoyant une réponse d'erreur.
        }

        return $resetPasswordDTO;
    }
    
}
