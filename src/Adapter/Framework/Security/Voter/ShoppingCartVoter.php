<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security\Voter;

use App\Domain\Model\ShoppingCart;
use App\Domain\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ShoppingCartVoter extends Voter
{
    private const MODIFY = 'modify';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::MODIFY && $subject instanceof ShoppingCart;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $tokenUser */
        $tokenUser = $token->getUser();

        if (!$tokenUser instanceof User) {
            return false;
        }

        /** @var ShoppingCart $shoppingCart */
        $shoppingCart = $subject;

        return $shoppingCart->isOwner($tokenUser->getCompany()->getId());
    }
}
