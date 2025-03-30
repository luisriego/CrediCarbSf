<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security\Voter;

use App\Domain\Model\Discount;
use App\Domain\Model\User;
use App\Domain\Repository\DiscountRepositoryInterface;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;
use function is_string;

class DiscountVoter extends Voter
{
    public const APPROVE_DISCOUNT = 'approve_discount';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
        private readonly DiscountRepositoryInterface $discountRepository,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::APPROVE_DISCOUNT
            && ($subject instanceof Discount || is_string($subject));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $discount = $subject instanceof Discount
        ? $subject
        : $this->discountRepository->findOneByIdOrFail($subject);

        if (!$discount instanceof Discount) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return true;
        }

        if (!in_array($attribute, $this->allowedAttributes(), true)) {
            return false;
        }

        try {
            return $discount->canBeApprovedBy($user);
        } catch (Exception $e) {
            return false;
        }
    }

    private function allowedAttributes(): array
    {
        return [
            self::APPROVE_DISCOUNT,
        ];
    }
}
