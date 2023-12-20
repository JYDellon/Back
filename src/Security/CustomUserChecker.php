<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Entity\User;

class CustomUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getIsEmailConfirmed()) {
            throw new CustomUserMessageAuthenticationException('Votre email n\'a pas été confirmé.');
        }

        // Vérifier si le compte est suspendu
        if ($user->getAccountStatus() === 'suspend') {
            throw new CustomUserMessageAuthenticationException('Votre compte est suspendu.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        //En attente de ....
    }
}
