<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\User;
use OTPHP\TOTP;

class AuthenticatorService
{
    public function __construct(
        private readonly ParameterBagInterface $parameters,
        private readonly EntityManagerInterface $em
    )
    {
    }
    public function getQrCodeUri(User $user)
    {
        $totp=TOTP::generate();
        $totp->setIssuer($this->parameters->get('app.issuer'));
        $totp->setLabel($user->getUserIdentifier());
        $qrCodeUri = $totp->getQrCodeUri('https://api.qrserver.com/v1/create-qr-code/?color=000&bgcolor=FFF&data=[DATA]&qzone=2&margin=0&size=300x300&ecc=M',
            '[DATA]'
        );
        return [$qrCodeUri, $totp->getSecret()];

    }
    public function validatePairing(User $user, string $secret)
    {
        if(!$secret) {
            return;
        }
        $user->setSecret($secret);
        $this->em->flush();
    }
}