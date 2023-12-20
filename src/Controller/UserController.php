<?php

namespace App\Controller;

use App\Service\UserService;
use Psr\Log\LoggerInterface;
use App\DTO\Request\LoginUserDTO;
use App\DTO\Request\RegisterUserDTO;
use App\DTO\Request\DeleteAccountDTO;
use App\DTO\Request\ModifyAccountDTO;
use App\DTO\Request\ResetPasswordDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
       
    }

    //..................................REGISTER.................................................

    /**
     * @Route('/api/register', name: 'register', methods: ['POST'])
     */
    public function register(Request $request, UserService $userService, ValidatorInterface $validator): JsonResponse
    {
        $registerUserDTO = RegisterUserDTO::createFromRequest($request);
        $violations = $validator->validate($registerUserDTO);

        if (count($violations) > 0) {
            // Si la validation échoue, renvoyez un message d'erreur avec les violations.
            return new JsonResponse(['message' => 'Validation failed', 'errors' => $violations], 400);
        }

        // Si la validation réussit, on appelle le UserService pour créer le compte.
        $result = $userService->registerUser($registerUserDTO, $validator);

        if ($result->isSuccess()) {
            return new JsonResponse(['message' => 'Inscription réussie. Un e-mail de confirmation a été envoyé.'], 200);
        } else {
            return new JsonResponse(['message' => 'Échec de l\'inscription'], 400);
        }
    }


    /**
     * @Route("/api/confirm-email/{token}", name:"confirm_email", methods:{"GET"})
     */
    public function confirmEmail(string $token, UserService $userService, LoggerInterface $logger): RedirectResponse
    {
        $result = $userService->confirmEmail($token, $logger);

        if ($result->isSuccess()) {
            // Redirection vers le frontend
            // Exemple de redirection avec paramètre de requête côté serveur en PHP
            return new RedirectResponse('http://localhost:5173/authentification/login');
        } else {
            // Gérer l'échec de la confirmation ici, éventuellement rediriger vers une page d'erreur
        }
    }





    //................................LOGIN...............................................

    /**
     * @Route('/api/login', name: 'login', methods: ['POST'])
     */
    public function login(Request $request, UserService $userService): JsonResponse
    {
        // Pour validez et gérez la connexion de l'utilisateur à l'aide de LoginUserDTO.
        $loginUserDTO = LoginUserDTO::createFromRequest($request);
        $result = $userService->loginUser($loginUserDTO);

        if ($result->isSuccess()) {
            return new JsonResponse(['message' => 'Login successful'], 200);
        } else {
            return new JsonResponse(['message' => 'Login failed'], 401);
        }
    }

    //................................RESET PASSEWORD...................................................

    /**
     * @Route('/api/reset-password', name: 'reset_password', methods: ['GET', 'POST'])
     */
    public function resetPassword(
        Request $request,
        UserService $userService,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger
    ) {  // Retirez ": JsonResponse" de la signature de la méthode
        $resetPasswordDTO = ResetPasswordDTO::createFromRequest($request);

        $result = $userService->resetPassword($resetPasswordDTO, $mailer, $urlGenerator);

        if ($result->isSuccess()) {
            $logger->info('E-mail de réinitialisation du mot de passe envoyé avec succès.', [
                'email' => $resetPasswordDTO->email,
            ]);

            return new RedirectResponse('http://localhost:5173/authentification/login');
        } else {
            $logger->error('Échec de l\'envoi de l\'e-mail de réinitialisation du mot de passe.', [
                'email' => $resetPasswordDTO->email,
            ]);

            return new RedirectResponse('http://localhost:5173/some-error-page');
        }
    }


    /**
     * @Route('/api/reset-password/{token}', name: 'reset_password_from_link', methods: [GET, POST] )
     * @IsGranted("ROLE_USER")
     */
    public function resetPasswordFromLink(
        string $token,
        UserService $userService,
        LoggerInterface $logger
    ): JsonResponse {
        // Log the matched route and token for debugging
        $this->logger->info('Matched route "reset_password_from_link" with token: ' . $token);

        // Recherchez l'utilisateur par le jeton de réinitialisation
        $user = $userService->findUserByResetToken($token);

        if ($user) {
            // Réinitialisez le mot de passe de l'utilisateur
            $userService->resetUserPassword($user);

            return new JsonResponse(['message' => 'Password reset successfully']);
        }

        return new JsonResponse(['message' => 'Password reset failed'], 400);
    }

    //...............................MODIFY ACCOUNT.......................................

    /**
     * @Route("/api/modify-account", name:"modify_account", methods:{"PUT"})
     * @IsGranted("ROLE_USER")
     */
    public function modifyAccount(Request $request,UserService $userService,
    ValidatorInterface $validator,Security $security
    ): JsonResponse {
        // Get the currently authenticated user
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Create ModifyAccountDTO from the request
        $modifyAccountDTO = ModifyAccountDTO::createFromRequest($request);
        $this->logger->debug('ModifyAccountDTO: ' . print_r($modifyAccountDTO, true));

        $errors = $validator->validate($modifyAccountDTO);

        if (count($errors) > 0) {
            return new JsonResponse(['message' => 'Invalid request data',
             'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        // Delegate to UserService for account modification logic
        $result = $userService->modifyAccount($user, $modifyAccountDTO);

        if ($result->isSuccess()) {
            return new JsonResponse(['message' => $result->getMessage()], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => $result->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    //................................DELETE ACCOUNT...................................................

    /**
     * @Route("/api/delete-account", name:"delete_account", methods:{"DELETE"})
     */
    public function deleteAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security
    ): JsonResponse {
        // Decode the JSON content of the request body
        $data = json_decode($request->getContent(), true);

        // Check if the 'password' key is present in the JSON data
        if (!isset($data['password'])) {
            return new JsonResponse(['message' => 'Password cannot be null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Get the password from the JSON data
        $password = $data['password'];

        // Create DeleteAccountDTO with the password
        $deleteAccountDTO = new DeleteAccountDTO($password);

        // Check if the provided data matches user data (e.g., password confirmation)
        if (!$deleteAccountDTO->isDataValid()) {
            error_log('Unauthorized: ' . var_export(['message' => 'Unauthorized'], true));
            return new JsonResponse(['message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Implement the logic for deleting the user's account
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        // Invalidate the user's authentication token if needed

        return new JsonResponse(['message' => 'Votre compte a été supprimé avec succès!'], JsonResponse::HTTP_OK);
    }

    //............................LOGOUT......................................

    /**
     * @Route('/api/logout', name: 'logout', methods: ['GET'])
     */
    public function logout(TokenStorageInterface $tokenStorage): Response
    {
        // Clear the security token
        $tokenStorage->setToken(null);

        return new Response('Logout successful', Response::HTTP_OK);
    }
}