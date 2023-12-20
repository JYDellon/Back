<?php

namespace App\Service;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactService
{
    private $entityManager;
    private $mailer;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,
     MailerInterface $mailer, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->validator = $validator;
    }

    public function processContact(Contact $contact): ServiceResult
    {
        // On valide l'entité Contact
        $validationErrors = $this->validator->validate($contact);

        if (count($validationErrors) > 0) {
            // Gestion des erreurs de validation
            return ServiceResult::createError('Validation échouée', $validationErrors);
        }

        try {
            // On enregistre dans la base de données
            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            // Envoi d'un e-mail de notification à l'administrateur ou responsable
            $this->sendNotificationEmail($contact);

            // On envoi un no-reply e-mail à l'utilisateur
            $this->sendUserEmail($contact);

            // Envoi d'un ServiceResult avec des informations de réussite
            return ServiceResult::createSuccess('Contact traité avec succès');
        } catch (\Exception $e) {
            dump($e->getMessage());
            // Gestion des exceptions qui peuvent survenir au cours du processus
            return ServiceResult::createError('Error pendant le precessus', null, $e->getMessage());
        }
    }

    private function sendNotificationEmail(Contact $contact): void
    {
        $email = (new Email())
        ->from('contact@aquaelixir.com')
        ->to('admin@aquaelixir.com')
        ->subject('Soumission d\'un nouveau contact')
        ->html('<p>Nouvelle soumission de contact:</p>'
            . '<p>Identité: ' . $contact->getFirstName() . ' ' . $contact->getLastName() . '</p>'
            . '<p>Objet: ' . $contact->getObject() . '</p>'
            . '<p>Message: ' . $contact->getMessage() . '</p>');

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // Gérer les erreurs d'envoi d'e-mails
            // Enregistrez ou effectuez des actions supplémentaires si nécessaire
        }
    }

    private function sendUserEmail(Contact $contact): void
    {
        
        $userEmail = $contact->getEmail();

        $userNotificationEmail = (new Email())
        ->from('noreply@aquaelixir.com')
        ->to($userEmail)
        ->subject('Merci pour votre soumission de contact')
        ->html('<p>Bonjour ' . $contact->getFirstName() . ' ' . $contact->getLastName() . ',</p>'
            . '<p>Merci de nous contacter! Nous avons reçu votre message
             et vous répondrons dans les plus brefs délais.</p>'
            . '<p>Cordialement,<br>Aqua Elixir</p>');

        try {
            $this->mailer->send($userNotificationEmail);
        } catch (TransportExceptionInterface $e) {
            // On peut gérer les erreurs d'envoi d'e-mails ici
            // Ou bien on enregistre et on effectue des actions supplémentaires si nécessaire
        }
    }
}
