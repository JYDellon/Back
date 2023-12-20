<?php

namespace App\Controller;

use App\Entity\NewsletterSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NewsletterSubscriberRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class NewsletterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/newsletter/subscribe", name="api_newsletter_subscribe", methods={"POST"})
     */
    public function subscribeToNewsletter(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Invalid email address.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Check if the email is already subscribed (optional)

        $subscriber = new NewsletterSubscriber();
        $subscriber->setEmail($email);

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Inscription à la Newsletter réussi!']);
    }

    //....................NEWSLETTER CRUD..........................................

        /**
     * @Route("/api/newsletter/all", name="api_newsletter_all", methods={"GET"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function getAllNewsletterSubscriber(NewsletterSubscriberRepository $newsletterSubscriberRepository,
        SerializerInterface $serializer,): JsonResponse

    {
        $newsletterSubscribersList=$newsletterSubscriberRepository->findAll();
        $jsonNewsletterSubscriberList = $serializer->serialize($newsletterSubscribersList, 'json');

        return new JsonResponse($jsonNewsletterSubscriberList, Response::HTTP_OK,[], true);
    }

        /**
     * @Route("/api/newsletterSubscriber/delete/{id}", name="api_newsletterSubscriber_delete", methods={"DELETE"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function deleteNewsletterSubscriber(NewsletterSubscriber $newsletterSubscriber,
        EntityManagerInterface $em): JsonResponse
    {
        if (!$newsletterSubscriber) {
            return new JsonResponse(['error' => 'Cet email n\existe pas'], Response::HTTP_NOT_FOUND);
        }
       
        $em->remove($newsletterSubscriber);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

        /**
     * @Route("/api/newsletterSubscriber/update/{id}", name="api_NewsletterSubscriber_update", methods={"PUT"})
     * #[IsGranted("ROLE_ADMIN")]
     */
    public function updateNewsletterSubscriber(Request $request, SerializerInterface $serializer,
        NewsletterSubscriber $newsletterSubscriber, EntityManagerInterface $em): JsonResponse
   {
       $updatedNewsletterSubscriber = $serializer->deserialize($request->getContent(),
            NewsletterSubscriber::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $newsletterSubscriber]);
       
       $em->persist($updatedNewsletterSubscriber);
       $em->flush();
       
       return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
