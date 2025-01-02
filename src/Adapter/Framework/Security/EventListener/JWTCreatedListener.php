<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security\EventListener;

use App\Domain\Model\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class JWTCreatedListener
{
    public function __construct(
        private RequestStack $requestStack,
    ) {}

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        $payload = $event->getData();
        $payload['id'] = $user->getId();

        $event->setData($payload);
    }
}
