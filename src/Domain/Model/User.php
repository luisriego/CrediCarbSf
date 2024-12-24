<?php

namespace App\Domain\Model;

use App\Adapter\Database\ORM\Doctrine\Repository\DoctrineUserRepository;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;
    public const MIN_AGE = 18;
    public const NAME_MIN_LENGTH = 2;
    public const NAME_MAX_LENGTH = 80;

    #[ORM\Column(type: 'string', length: 80)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private readonly ?string $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private ?string $token;

    #[ORM\Column(type: 'string', length: 255, options: [
        'comment' => 'The hashed password',
    ])]
    private ?string $password;

    #[ORM\Column(type: 'smallint')]
    private int $age;

    #[ORM\ManyToOne(targetEntity: Condo::class, inversedBy: 'users')]
    private ?Condo $condo = null;

    private function __construct(
        string $id,
        ?string $name,
        ?string $email,
        ?string $password,
        ?string $token,
        int $age,
        bool $isActive,
        \DateTimeImmutable $createdOn,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
        $this->age = $age;
        $this->isActive = $isActive;
        $this->createdOn = $createdOn;
        $this->markAsUpdated();
    }

    public static function create($id, $name, $email, $password, $age): self
    {
        return new static(
            $id,
            $name,
            $email,
            $password,
            \sha1(\uniqid()),
            $age,
            false,
            new \DateTimeImmutable()
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getCondo(): ?Condo
    {
        return $this->condo;
    }

    public function setCondo(?Condo $condo): void
    {
        $this->condo = $condo;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function equals(User $user): bool
    {
        return $this->getId() === $user->getId();
    }
}
