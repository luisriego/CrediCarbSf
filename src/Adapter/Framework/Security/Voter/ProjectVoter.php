<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security\Voter;

use App\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

class ProjectVoter extends Voter
{
    public const GET_PROJECT = 'GET_PROJECT';
    public const GET_PROJECTS = 'GET_PROJECTS';
    public const CREATE_PROJECT = 'CREATE_PROJECT';
    public const UPDATE_PROJECT = 'UPDATE_PROJECT';
    public const DELETE_PROJECT = 'DELETE_PROJECT';

    public function __construct(
        private readonly Security $security,
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, $this->allowedAttributes(), true);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $tokenUser */
        $tokenUser = $token->getUser();

        if (!$tokenUser instanceof User) {
            return false;
        }

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->accessDecisionManager->decide($token, ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'])) {
            return true;
        }

        if (in_array($attribute, $this->allowedAttributes(), true)) {
            return $tokenUser->getCompany()->getId() === $subject->getCompany()->getId();
        }

        if (self::GET_PROJECT === $attribute) {
            return $this->security->isGranted('ROLE_USER');
        }

        if (self::GET_PROJECTS === $attribute) {
            return $this->security->isGranted('ROLE_USER');
        }

        if (self::CREATE_PROJECT === $attribute) {
            return in_array('ROLE_OPERATOR', $tokenUser->getRoles(), true);
        }

        if (self::UPDATE_PROJECT === $attribute) {
            return $tokenUser->getCompany() === $subject;
        }

        return false;
    }

    private function allowedAttributes(): array
    {
        return [
            self::GET_PROJECT,
            self::GET_PROJECTS,
            self::CREATE_PROJECT,
            self::UPDATE_PROJECT,
            self::DELETE_PROJECT,
        ];
    }
}
