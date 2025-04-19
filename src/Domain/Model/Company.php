<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use App\Domain\Validation\Traits\AssertTaxpayerValidatorTrait;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

use function mb_strlen;
use function preg_replace;
use function sprintf;

#[ORM\Entity(repositoryClass: CompanyRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class Company
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;
    use AssertTaxpayerValidatorTrait;

    public const TAXPAYER_MIN_LENGTH = 14;
    public const TAXPAYER_MAX_LENGTH = 14;
    public const NAME_MIN_LENGTH = 5;
    public const NAME_MAX_LENGTH = 100;

    private const STATUS_ACTIVE = true;
    private const STATUS_INACTIVE = false;

    #[ORM\Column(type: 'string', length: 14, options: ['fixed' => true])]
    private string $taxpayer;

    #[ORM\Column(type: 'string', length: 100)]
    private string $fantasyName;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'company', orphanRemoval: false)]
    private Collection $users;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'owner', orphanRemoval: false)]
    private Collection $ownedProjects;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'buyer', orphanRemoval: false)]
    private Collection $boughtProjects;

    private function __construct(string $id, string $taxpayer, string $fantasyName)
    {
        $this->validateFantasyName($fantasyName);

        $this->id = CompanyId::fromString(id: $id)->value();
        $this->taxpayer = CompanyTaxpayer::fromString(taxpayer: $taxpayer)->value();
        $this->fantasyName = CompanyName::fromString($fantasyName)->value();

        $this->users = new ArrayCollection();
        $this->ownedProjects = new ArrayCollection();
        $this->boughtProjects = new ArrayCollection();
        $this->isActive = true;
        $this->initializeCreatedOn();
    }

    public static function create(CompanyId $id, CompanyTaxpayer $taxpayer, CompanyName $fantasyName): self
    {
        return new self(
            $id->value(),
            $taxpayer->value(),
            $fantasyName->value(),
        );
    }

    public function fantasyName(): string
    {
        return $this->fantasyName;
    }

    public function taxpayer(): string
    {
        return $this->taxpayer;
    }

    public function updateFantasyName(string $fantasyName): void
    {
        $this->validateFantasyName($fantasyName);
        $this->fantasyName = $fantasyName;
        $this->markAsUpdated();
    }

    public function activate(): self
    {
        if ($this->isActive === self::STATUS_ACTIVE) {
            throw new DomainException(
                sprintf('Company %s is already active', $this->id),
            );
        }

        $this->isActive = self::STATUS_ACTIVE;
        $this->markAsUpdated();

        return $this;
    }

    public function deactivate(): self
    {
        if ($this->isActive === self::STATUS_INACTIVE) {
            throw new DomainException(
                sprintf('Company %s is already inactive', $this->id),
            );
        }

        $this->isActive = self::STATUS_INACTIVE;
        $this->markAsUpdated();

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive === self::STATUS_ACTIVE;
    }

    public function assignUserToCompany(User $user): void
    {
        if (!$this->isActive) {
            throw new DomainException('Cannot assign user to inactive company');
        }

        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->assignToCompany($this);
            //            $user->assignToCompany($this);
        }
    }

    public function removeUserFromCompany(User $user): void
    {
        if ($this->users->removeElement($user)) {
            $user->assignToCompany(null);
            //            $user->removeFromCompany($this); // more semantic sentence
        }
    }

    public function registerOwnedProject(Project $project): void
    {
        if (!$this->isActive) {
            throw new DomainException('Cannot register project for inactive company');
        }

        if (!$this->ownedProjects->contains($project)) {
            $this->ownedProjects->add($project);
            $project->setOwner($this);
            //            $project->assignOwner($this); // more semantic sentence
        }
    }

    public function purchaseProject(Project $project): void
    {
        if (!$this->isActive) {
            throw new DomainException('Cannot purchase project with inactive company');
        }

        if (!$this->boughtProjects->contains($project)) {
            $this->boughtProjects->add($project);
            $project->setBuyer($this);
            //            $project->assignBuyer($this); // more semantic sentence
        }
    }

    public function formattedTaxpayer(): string
    {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->taxpayer);
    }

    public function hasUser(User $user): bool
    {
        return $this->users->contains($user);
    }

    public function hasProject(Project $project): bool
    {
        return $this->ownedProjects->contains($project) || $this->boughtProjects->contains($project);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'taxpayer' => $this->formattedTaxpayer(),
            'fantasyName' => $this->fantasyName,
            'isActive' => $this->isActive,
            'createdOn' => $this->createdOn->format('Y-m-d H:i:s'),
            'updatedOn' => $this->updatedOn?->format('Y-m-d H:i:s'),
        ];
    }

    private function validateFantasyName(?string $fantasyName): void
    {
        if ($fantasyName !== null
            && (mb_strlen($fantasyName) < self::NAME_MIN_LENGTH || mb_strlen($fantasyName) > self::NAME_MAX_LENGTH)
        ) {
            throw new InvalidArgumentException(
                sprintf('Fantasy name must be between %d and %d characters', self::NAME_MIN_LENGTH, self::NAME_MAX_LENGTH),
            );
        }
    }
}
