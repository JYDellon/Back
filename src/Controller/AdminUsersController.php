<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class AdminUsersController extends AbstractController
{
    /**
     * @Route("/api/users", name="api_users_list", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getUsername(),
                'lastName' => $user->getLastName(),
                'firstName' => $user->getFirstName(),
                'accountStatus' => $user->getAccountStatus(),
                'createdAt' => $user->getCreatedAt()->format('d-m-Y H:i:s'),
                'roles' => !empty($user->getRoles()) ? current($user->getRoles()) : null,
            ];
        }

        return $this->json($formattedUsers);
    }

    /**
     * @Route("/api/users/{id}", name="api_user_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function deleteUser(UserRepository $userRepository, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Supprimez l'utilisateur de la base de données
        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur supprimé avec succès']);
    }


    /**
     * @Route("/api/users/{id}/ban", name="api_user_ban", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function banUser(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Vérifiez si l'utilisateur est déjà banni
        if ($user->getAccountStatus() === 'suspend') {
            // Si oui, réactivez l'utilisateur
            $user->setAccountStatus('active');
        } else {
            // Sinon, bannissez l'utilisateur
            $user->setAccountStatus('suspend');
        }

        // Enregistrez les modifications dans la base de données 
        $entityManager->flush($user);
        $message = $user->getAccountStatus() === 'suspend'
            ? 'Utilisateur banni avec succès'
            : 'Bannissement annulé avec succès';
        return $this->json(['message' => $message, 'accountStatus' => $user->getAccountStatus()]);
    }

    /**
     * @Route("/api/users/{id}/change-role", name="api_user_change_role", methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function changeUserRole(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        int $id
    ): JsonResponse {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $data = json_decode($request->getContent(), true);

        // Vérifiez si le rôle est valide (ajuste selon tes besoins)
        $validRoles = ['ROLE_USER', 'ROLE_ADMIN'];
        if (!isset($data['newRole']) || !in_array($data['newRole'], $validRoles)) {
            return $this->json(['message' => 'Rôle invalide'], 400);
        }

        // Mettez à jour le rôle de l'utilisateur
        $user->setRoles([$data['newRole']]);

        // Enregistrez les modifications dans la base de données 
        $entityManager->flush($user);

        return $this->json(['message' => 'Rôle de l\'utilisateur mis à jour avec succès', 'roles' => $user->getRoles()]);
    }
}
