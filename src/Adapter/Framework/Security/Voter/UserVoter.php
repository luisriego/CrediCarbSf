<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security\Voter;

use App\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

final class UserVoter extends Voter
{
    public const GET_USERS_COMPANY = 'GET_USERS_COMPANY';
    public const UPDATE_USER = 'UPDATE_USER';
    public const ADD_NEW_USER = 'ADD_NEW_USER ';
    public const DELETE_USER = 'DELETE_USER ';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, $subject): bool
    {
        return in_array(
            $attribute,
            $this->allowedAttributes(),
            true,
        );
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        /** @var User $tokenUser */
        $tokenUser = $token->getUser();

        if (self::GET_USERS_COMPANY === $attribute) {
            foreach ($subject as $user) {
                if ($tokenUser->getId() === $user->getId()) {
                    return true;
                }
            }
        }

        if (self::ADD_NEW_USER === $attribute) {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }

            if ($this->security->isGranted('ROLE_SYNDIC')) {
                return $tokenUser->getCompany()->getId() === $subject;
            }

            return false;
        }

        // ROLE_SYNDIC can update another users in his own Companyminium and itself
        if (in_array($attribute, $this->allowedAttributes(), true)) {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }

            if ($this->security->isGranted('ROLE_SYNDIC')) {
                return $tokenUser->getCompany() === $subject->getCompany();
            }

            return $tokenUser->getId() === $subject->getId();
        }

        return false;
    }

    private function allowedAttributes(): array
    {
        return [
            self::GET_USERS_COMPANY,
            self::UPDATE_USER,
            self::ADD_NEW_USER,
            self::DELETE_USER,
        ];
    }
}
