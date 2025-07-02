<?php
// src/EventListener/SecurityEventsSubscriber.php
namespace App\EventListener;

use App\Service\AuditLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SecurityEventsSubscriber implements EventSubscriberInterface
{
    public function __construct(private AuditLogger $logger) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLoginSuccess(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->logger->log(
            'CONNEXION',
            sprintf('Connexion réussie - IP: %s', $_SERVER['REMOTE_ADDR']),
            $user
        );
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $this->logger->log(
            'DÉCONNEXION',
            sprintf('Déconnexion du système - IP: %s', $_SERVER['REMOTE_ADDR']),
            $user
        );
    }
}