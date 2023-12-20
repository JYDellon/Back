<?php

namespace App\Controller;

use App\Entity\Contact;
use App\DTO\Request\ContactDTO;
use App\Service\ContactService;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Attributes\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route("/api/contacts", name: "api_contacts_")]
class ContactController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route("", name: "create", methods: ["POST"])]
    public function create(
        Request $request,
        ContactService $contactService,
        ValidatorInterface $validator
    ): JsonResponse {
        // Désérialisez la requête JSON vers votre DTO
        $contactDTO = $this->deserializeRequest($request, ContactDTO::class);

        // Valider le DTO
        $errors = $validator->validate($contactDTO);
        if (count($errors) > 0) {
            // Gérer les erreurs de validation
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        // Créer une entité Contact à partir du DTO
        $contact = new Contact();
        $contact->setLastName($contactDTO->getLastName());
        $contact->setFirstName($contactDTO->getFirstName());
        $contact->setObject($contactDTO->getObject());
        $contact->setMessage($contactDTO->getMessage());
        $contact->setEmail($contactDTO->getEmail());

        $contact->setStatus('En-Attente');

        // Effectuer toute logique métier ou validation supplémentaire dans le service
        $result = $contactService->processContact($contact, $validator);

        if ($result->isSuccess()) {
            return $this->json(['message' => 'Votre demande a éte envoyé avec succès!'], Response::HTTP_CREATED);
        } else {
            return $this->json(['error' => 'Echec de l\'envoi de la demande'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function deserializeRequest(Request $request, string $dtoClass)
    {
        return $this->serializer->deserialize($request->getContent(), $dtoClass, 'json');
    }

    //..........................Contact CRUD..........................................

    /**
     * @Route("/all", name="api_contacts_all", methods={"GET"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function getAllContacts(ContactRepository $contactRepository, SerializerInterface $serializer,): JsonResponse

    {
        $contactsList = $contactRepository->findAll();
        $jsonContactList = $serializer->serialize($contactsList, 'json');

        return new JsonResponse($jsonContactList, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/detail/{id}", name="api_contacts_detail", methods={"GET"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function getDetailContact(SerializerInterface $serializer, Contact $contact,): JsonResponse
    {
        if (!$contact) {
            throw new NotFoundHttpException('Ce contact n\'existe pas.');
        }

        $jsonContact = $serializer->serialize($contact, 'json');

        return new JsonResponse($jsonContact, Response::HTTP_OK);
    }

    /**
     * @Route("/delete/{id}", name="api_contacts_delete", methods={"DELETE"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function deleteContact(Contact $contact, EntityManagerInterface $em): JsonResponse
    {
        if (!$contact) {
            return new JsonResponse(['error' => 'Ce contact n\existe pas'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($contact);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/update/{id}", name="api_contacts_update", methods={"PUT"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function updateContact(
        Request $request,
        SerializerInterface $serializer,
        Contact $currentContact,
        EntityManagerInterface $em
    ): JsonResponse {
        $updatedContact = $serializer->deserialize(
            $request->getContent(),
            Contact::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentContact]
        );

        $em->persist($updatedContact);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/contact/update-status/{id}", name="api_contact_update_status", methods={"PATCH"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function updateContactStatus(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em
    ): JsonResponse {
        $status = $request->get('status');

        $currentContact = $em->getRepository(Contact::class)->find($id);

        if (!$currentContact) {
            return new JsonResponse(['error' => 'Contact not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $updatedContact = $serializer->deserialize(
            $request->getContent(),
            Contact::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentContact]
        );

        $updatedContact->setStatus($status);

        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
