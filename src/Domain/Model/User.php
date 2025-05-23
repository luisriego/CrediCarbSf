<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Adapter\Database\ORM\Doctrine\Repository\DoctrineUserRepository;
use App\Domain\Common\AggregateRoot;
use App\Domain\Common\UserRole;
use App\Domain\Event\CreateUserDomainEvent;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Security\PasswordHasherInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertPasswordValidatorTrait;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Uuid;
use App\Domain\ValueObject\UserName;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function array_unique;
use function in_array;
use function sha1;
use function uniqid;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;
    use AssertPasswordValidatorTrait;
    use AssertLengthRangeTrait;

    public const MIN_AGE = 18;
    public const NAME_MIN_LENGTH = 2;
    public const NAME_MAX_LENGTH = 80;
    public const MIN_PASSWORD_LENGTH = 6;
    public const MAX_PASSWORD_LENGTH = 55;
    public const ID_LENGTH = 36;

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
    private ?int $age;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Company $company = null;

    public function __construct(
        string $id,
        string $name,
        string $email,
        string $password,
    ) {
        $this->id = UserId::fromString(id: $id)->value();
        $this->name = UserName::fromString(name: $name)->value();
        $this->email = Email::fromString(email: $email)->value();
        $this->password = Password::fromString(password: $password)->value();
        $this->token = sha1(uniqid('', true));
        $this->age = 18;
        $this->isActive = false;
        $this->createdOn = new DateTimeImmutable();
        $this->markAsUpdated();
    }

    public static function create(UserId $id, UserName $name, Email $email, Password $password): self
    {
        $user = new self(
            $id->value(),
            $name->value(),
            $email->value(),
            $password->value(),
        );

        $user->record(
            new CreateUserDomainEvent(
                $user->id(),
                $user->getName(),
                $user->getEmail(),
                $user->getPassword(),
                Uuid::random()->value(),
                (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
            ));

        return $user;
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
        if (!$this->isValueRangeLengthValid($name, self::NAME_MIN_LENGTH, self::NAME_MAX_LENGTH)) {
            throw InvalidArgumentException::createFromMinAndMaxLength(self::NAME_MIN_LENGTH, self::NAME_MAX_LENGTH);
        }

        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        if ($age < self::MIN_AGE) {
            throw InvalidArgumentException::createFromMin(self::MIN_AGE);
        }
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function assignToCompany(?Company $company): void
    {
        $this->company = $company;
    }
    //
    //    // for Nelmio\Alice\Fixtures\Fixture purposes
    //    public function setCompany(?Company $company): void
    //    {
    //        $this->company = $company;
    //    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function belongsToCompany(string $companyId): bool
    {
        if ($this->company === null) {
            return false;
        }

        return $this->company->getId() === $companyId;
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

    public function hasRole(UserRole $role): bool
    {
        return in_array($role->value, $this->getRoles(), true);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password, PasswordHasherInterface $hasher): self
    {
        if (!$this->assertPassword($password)) {
            throw InvalidArgumentException::createFromArgument('password');
        }

        $hashed = $hasher->hashPasswordForUser($this, $password);

        $this->password = $hashed;

        return $this;
    }

    public function changePassword(string $newPassword, PasswordHasherInterface $hasher): void
    {
        $this->setPassword($newPassword, $hasher);

        $this->markAsUpdated(); // I think this is already doing in a Doctrine listener, see it later
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
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'age' => $this->age,
            'roles' => $this->roles,
            'isActive' => $this->isActive,
            'createdOn' => $this->createdOn,
            'updatedOn' => $this->updatedOn,
        ];
    }

    public function equals(User $user): bool
    {
        return $this->getId() === $user->getId();
    }
}
