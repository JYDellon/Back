<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Entity\Traits\CreatedAtTrait;
use App\Service\PasswordGenerator;
use App\DTO\Request\LoginUserDTO;
use App\DTO\Request\RegisterUserDTO;
use App\DTO\Request\ResetPasswordDTO;
use App\DTO\Request\ModifyAccountDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Exception\EmailConfirmationException;
use App\Exception\UserResetPasswordException;
use App\Exception\EmailSendException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\RedirectResponse;




class UserService
{
    use CreatedAtTrait;

    private $passwordHasher;
    private $entityManager;
    private $mailer;
    private $urlGenerator;
    private $logger;
    private $passwordGenerator;


    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger,
        PasswordGenerator $passwordGenerator
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
        $this->passwordGenerator = $passwordGenerator;
    }

    //....................................REGISTER...................................................

    //Gestion d'inscription
    public function registerUser(RegisterUserDTO $registerUserDTO, ValidatorInterface $validator): ServiceResult
    {
        try {
            $this->logger->info('Received DTO data:', ['data' => [
                'firstName' => $registerUserDTO->firstName,
                'lastName' => $registerUserDTO->lastName,
                'email' => $registerUserDTO->email,
                'password' => $registerUserDTO->password,
            ]]);

            // Validation the DTO
            $validator->validate($registerUserDTO, new Assert\Collection([
                'firstName' => new Assert\NotBlank(),
                'lastName' => new Assert\NotBlank(),
                'email' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
                'password' => new Assert\Length(['min' => 8]),
            ]));


            // Recherche de l'utilisateur par e-mail
            $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $registerUserDTO->email]);

            // Si l'utilisateur existe déjà, renvoyer une erreur
            if ($existingUser) {
                return ServiceResult::createError
                ('Un utilisateur avec cette adresse e-mail existe déjà.', 400); // 400 Conflict
            }

            // On continue avec la logique d'inscription
            $user = new User();
            $user->setFirstName($registerUserDTO->firstName);
            $user->setLastName($registerUserDTO->lastName);
            $user->setEmail($registerUserDTO->email);
            $user->setRoles(['ROLE_USER']);

            // Hasher et définir le mot de passe
            $password = $this->passwordHasher->hashPassword($user, $registerUserDTO->password);
            $user->setPassword($password);

            // On défini le jeton de confirmation
            $confirmationToken = bin2hex(random_bytes(32));
            $user->setConfirmationToken($confirmationToken);

            // Persister l'utilisateur dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Envoi d'e-mail de confirmation
            $this->sendConfirmationEmail($user, $this->mailer, $this->urlGenerator, $this->logger);

            return ServiceResult::createSuccess();
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'inscription: ' . $e->getMessage());
            // Renvoie une réponse d'erreur ou lève une exception si nécessaire
            return ServiceResult::createError('Échec de l\'enregistrement', 500);
        }
    }


    public function sendConfirmationEmail(User $user, MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator, LoggerInterface $logger): ServiceResult
    {
        $this->logger->info('Tentative d\'envoi d\'un e-mail de confirmation à l\'utilisateur: ' . $user->getEmail());

        // On s'assure que l'utilisateur dispose d'un jeton de confirmation
        $confirmationToken = $user->getConfirmationToken();

        if (!$confirmationToken) {
            throw new EmailConfirmationException('L\'utilisateur n\'a pas de jeton de confirmation.', 500);
        }

        $this->logger->info('Jeton de confirmation pour ' . $user->getEmail() . ': ' . $confirmationToken);

        //Génération de l'URL de confirmation à l'aide du jeton existant
        $confirmationUrl = $urlGenerator->generate(
            'confirm_email',
            ['token' => $confirmationToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // $confirmationUrl = 'http://localhost:5173/confirmer-email/' . $confirmationToken;

        // Envoi d'e-mail de confirmation
        $email = (new Email())
            ->from('confirm.email@aquaelixir.com')
            ->to($user->getEmail())
            ->subject('Confirmation d\'e-mail')
            ->html("Cliquez sur le lien suivant pour confirmer votre e-mail: <a href=
            '{$confirmationUrl}'>Confirmer votre e-mail</a>");

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
            $logger->error('Failed to send the confirmation email.', ['exception' => $e]);

            // On passe l'exception comme troisième paramètre
            throw new EmailConfirmationException('Échec de l\'envoi de l\'e-mail de confirmation', 0, $e);
        }

        return ServiceResult::createSuccess('E-mail de confirmation envoyé.');
    }


    public function confirmEmail(string $token, LoggerInterface $logger): ServiceResult
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            $logger->error('Email confirmation failed. Token not found.');
            throw new EmailConfirmationException('Email confirmation failed. Token not found.');
        }

        $user->setIsEmailConfirmed(true);
        $user->setConfirmationToken(null);
        $user->setAccountStatus('active');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Supposons que ServiceResult est une classe que vous avez qui peut indiquer le succès ou l'échec
        return new ServiceResult(true, 'E-mail confirmé.'); // Vous devez créer cette classe si elle n'existe pas déjà
    }






    //....................................LOGIN...................................................

    // Gestion de Login
    public function loginUser(LoginUserDTO $loginUserDTO): ServiceResult
    {
        // Charger l'utilisateur par email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $loginUserDTO->email]);
        if (!$user) {
            return ServiceResult::createError('User not found', 404);
        }

        // Vérifier si l'utilisateur existe et si l'email a été confirmé
        if (!$user || !$user->getIsEmailConfirmed()) {
            // Gérer le cas où l'utilisateur n'existe pas ou l'email n'est pas confirmé
            return ServiceResult::createError('E-mail non confirmé ou utilisateur introuvable.');
        }

        // Pour vérifier si le mot de passe est valide
        if (!$this->passwordHasher->isPasswordValid($user, $loginUserDTO->password)) {
            return ServiceResult::createError('Invalid credentials', 401);
        }

        // Implémentation de la logique d'authentification ici.

        return ServiceResult::createSuccess('Connexion réussie.');
    }

    //....................................RESET PASSEWORD...................................................

    // Gestion d'envoi de reinitialisation de mot de passe
    public function resetPassword(
        ResetPasswordDTO $resetPasswordDTO,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator
    ): ServiceResult {
        $email = $resetPasswordDTO->email;

        $this->logger->info('Attempting to reset password for email: ' . $email);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $this->logger->error('User with email ' . $email . ' not found.');
            throw new UserResetPasswordException('User not found');
        }

        // Générer un jeton de réinitialisation unique
        $resetToken = bin2hex(random_bytes(32));
        $user->setResetToken($resetToken);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        //Vérifiez si le jeton a été défini
        if (!$user->getResetToken()) {
            throw new UserResetPasswordException('Failed to generate the reset token');
        }
        // Générer l'URL de réinitialisation du mot de passe
        $resetUrl = $urlGenerator->generate(
            'reset_password_from_link',
            ['token' => $resetToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $resetUrl = str_replace('http://localhost:5173/confirm-reset/',
         'http://localhost:8002/api/reset-password/', $resetUrl);

        // Envoyer un e-mail à l'utilisateur avec l'URL de réinitialisation du mot de passe
        $email = (new Email())
            ->from('reset.password@aquaelixir.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation du mot de passe')
            ->html("Cliquez sur le lien suivant pour réinitialiser votre mot de passe:
                <a href='{$resetUrl}'>Réinitialisé votre mot de passe</a");

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
            throw new EmailSendException('Failed to send the reset email', $e);
        }

        return ServiceResult::createSuccess('E-mail de réinitialisation du mot de passe envoyé.');
    }

    // Gestion de reinitialisation de mot de passe
    public function findUserByResetToken(string $resetToken): ?User
    {
        // Utilisez Doctrine pour rechercher l'utilisateur par le jeton de réinitialisation
        return $this->entityManager->getRepository(User::class)->findOneBy(['resetToken' => $resetToken]);
    }

    public function resetUserPassword(User $user,): void
    {
        // Générez un nouveau mot de passe sécurisé en utilisant PasswordGenerator
        $newPassword = $this->passwordGenerator->generateRandomPassword();

        $newPasswordHash = $this->passwordHasher->hashPassword($user, $newPassword);

        // Mettez à jour l'utilisateur dans la base de données avec le nouveau mot de passe
        $user->setPassword($newPasswordHash);
        $user->setResetToken(null); // Supprimez le jeton de réinitialisation
        $this->entityManager->flush();

        // Envoyer un e-mail au nouvel utilisateur
        $email = (new Email())
            ->from('new.password@aquaelixir.com')
            ->to($user->getEmail())
            ->subject('Nouveau mot de passe')
            ->text('Votre nouveau mot de passe est : ' . $newPassword);

        $this->mailer->send($email);
    }

    //..........................MODIFY ACCOUNT.............................................
    // Gestion de modification de compte
    public function modifyAccount(User $user, ModifyAccountDTO $modifyAccountDTO): ServiceResult
    {
        try {
            $this->logger->debug('DTO Data: ' . print_r($modifyAccountDTO, true));

            // Log received password
            $password = $modifyAccountDTO->getPassword();
            $this->logger->debug('Received Password: ' . $password);

            // Check if the token is still valid
        if ($password === null || !$this->passwordHasher->isPasswordValid($user, $password)) {
            // Log the hashed password for comparison
            $hashedPassword = $user->getPassword();
            $this->logger->debug('Invalid Password. Hashed Password in DB: ' . $hashedPassword);

            return ServiceResult::createError('Mot de passe actuel invalide', 401);
        }
            // Modify the account details
            $this->updateAccountDetails($user, $modifyAccountDTO);

            // Persist changes to the database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return ServiceResult::createSuccess('Compte modifié avec succès');
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la modification du compte: ' . $e->getMessage());

            // Renvoie une réponse d'erreur d\'exception si nécessaire
            return ServiceResult::createError('Échec de la modification du compte', 500);
        }
    }
    private function updateAccountDetails(User $user, ModifyAccountDTO $modifyAccountDTO): void
    {
        try {
            // Verify the current password
            $password = $modifyAccountDTO->getPassword();
            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                $hashedPassword = $user->getPassword();
                $this->logger->debug('Invalid Password. Hashed Password in DB: ' . $hashedPassword);
                throw new \Exception('Mot de passe actuel invalide');
            }

            // Update first name if provided
            $firstName = $modifyAccountDTO->getFirstName();
            if ($firstName !== null) {
                $this->validateAndSetFirstName($user, $modifyAccountDTO);
            }

            // Update last name if provided
            $lastName = $modifyAccountDTO->getLastName();
            if ($lastName !== null) {
                $this->validateAndSetLastName($user, $modifyAccountDTO);
            }

            // Update password only if a new password is provided
            $newPassword = $modifyAccountDTO->getNewPassword();
            if ($newPassword !== null) {
                // Check if the password has changed
                if ($this->passwordHasher->isPasswordValid($user, $newPassword)) {
                    // Password hasn't changed, no need to update
                } else {
                    $this->updatePassword($user, $newPassword);
                }
            }

            // Check if any changes have been made before flushing
            if ($this->entityManager->getUnitOfWork()->getEntityChangeSet($user)) {
                $this->entityManager->flush();
            }

        } catch (\Exception $e) {
            // Log or handle the exception if needed
            throw $e;
        }
    }

    
    private function validateAndSetFirstName(User $user, ModifyAccountDTO $modifyAccountDTO): void
    {
        $firstName = $modifyAccountDTO->getFirstName();
        if ($firstName !== null) {
            // Trim
            $trimmedFirstName = trim($firstName);
            $this->validateAndSetProperty($user, 'FirstName', $trimmedFirstName, 'Le prénom');
        }
    
        // Set the validated and trimmed first name
        $user->setFirstName($trimmedFirstName);
    }
    
    private function validateAndSetLastName(User $user, ModifyAccountDTO $modifyAccountDTO): void
    {
        $lastName = $modifyAccountDTO->getLastName();
        if ($lastName !== null) {
            // Trim
            $trimmedLastName = trim($lastName);
    
            // Validate last name
            $this->validateAndSetProperty($user, 'LastName', $trimmedLastName, 'Le nom');
        }
    
        // Set the validated and trimmed last name
        $user->setLastName($trimmedLastName);
    }
    

    private function updatePassword(User $user, ?string $newPassword): void
    {
        if ($newPassword !== null) {
            // Hash the new password
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);

            // Set the hashed password to the user
            $user->setPassword($hashedPassword);

            // Log the updated password details
            $this->logger->debug('Nouveau mot de passe: ' . $newPassword);
            $this->logger->debug('Mot de passe actuel haché: ' . $user->getPassword());
        }
    }



    private function validateAndSetProperty(User $user, string $property, string $value, string $fieldName): void
    {
        if ($value === '') {
            throw new \Exception($fieldName . ' ne peut pas être vide', 400);
        }

        if (preg_match('/[0-9]/', $value)) {
            throw new \Exception($fieldName . ' ne peut pas contenir de chiffres', 400);
        }

        $length = strlen($value);
        if ($length < 2 || $length > 50) {
            throw new \Exception($fieldName . ' doit contenir entre 2 et 50 caractères', 400);
        }

        // Dynamically set the validated and trimmed property
        $setterMethod = 'set' . $property;
        $user->$setterMethod($value);
    }

}
