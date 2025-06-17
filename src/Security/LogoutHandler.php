<?php
// src/Security/LogoutHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutHandler
{
    public function onLogout(LogoutEvent $event): void
    {
        $request = $event->getRequest();
        $request->getSession()->remove('2fa_verified');
    }
}