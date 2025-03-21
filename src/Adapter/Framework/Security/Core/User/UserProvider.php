<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Security\Core\User;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function sprintf;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function __call(string $name, array $arguments): void
    {
        // TODO: Implement @method void upgradePassword(PasswordAuthenticatedUserInterface|UserInterface $user, string $newHashedPassword)
        // TODO: Implement @method UserInterface loadUserByIdentifier(string $identifier)
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of %s are not supported', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneByEmail($identifier);

        if (null === $user) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
        }

        return $user;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $user->setPassword($newHashedPassword);

        $this->userRepository->save($user);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        try {
            return $this->userRepository->findOneByEmail($username);
        } catch (UserNotFoundException) {
            throw new UserNotFoundException('User %s not found, $username');
        }
    }
}
