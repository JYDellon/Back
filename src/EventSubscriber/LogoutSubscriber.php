<?php


namespace App\EventSubscriber;

use App\Service\TokenBlacklistService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    private TokenBlacklistService $tokenBlacklistService;

    public function __construct(TokenBlacklistService $tokenBlacklistService)
    {
        $this->tokenBlacklistService = $tokenBlacklistService;
    }

    public function onLogout(LogoutEvent $event): void
    {
        // Get the token from the event
        $token = $event->getRequest()->headers->get('Authorization');

        if ($token) {
            // Add the token to the blacklist
            $this->tokenBlacklistService->addToBlacklist($token);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
