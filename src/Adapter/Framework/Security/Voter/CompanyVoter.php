<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security\Voter;

use App\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

final class CompanyVoter extends Voter
{
    public const ACTIVATE_COMPANY = 'ACTIVATE_COMPANY';
    public const UPDATE_COMPANY = 'UPDATE_COMPANY';
    public const DELETE_COMPANY = 'DELETE_COMPANY';
    public const GET_COMPANY = 'GET_COMPANY';
    public const GET_COMPANIES = 'GET_COMPANIES';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, $subject): bool
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
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (in_array($attribute, $this->allowedAttributes(), true)) {
            return $tokenUser->getCompany()->getId() === $subject;
        }

        if (self::GET_COMPANIES === $attribute) {
            return in_array('ROLE_USER', $tokenUser->getRoles(), true);
        }

        if (self::DELETE_COMPANY === $attribute) {
            return $tokenUser->getCompany() === $subject;
        }

        return false;
    }

    private function allowedAttributes(): array
    {
        return [
            self::ACTIVATE_COMPANY,
            self::UPDATE_COMPANY,
            self::DELETE_COMPANY,
            self::GET_COMPANY,
            self::GET_COMPANIES,
        ];
    }
}
