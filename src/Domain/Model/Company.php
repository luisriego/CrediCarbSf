<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use App\Domain\ValueObjects\Uuid;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class Company
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    public const TAXPAYER_MIN_LENGTH = 14; // Brazilian Taxpayer Identification Number (CNPJ);
    public const TAXPAYER_MAX_LENGTH = 14;
    public const NAME_MIN_LENGTH = 5;
    public const NAME_MAX_LENGTH = 100;

    #[ORM\Column(type: 'string', length: 14, options: ['fixed' => true])]
    private ?string $taxpayer = '';

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $fantasyName = '';

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'company', orphanRemoval: false)]
    private Collection $users;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'owner', orphanRemoval: false)]
    private Collection $ownedProjects;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'buyer', orphanRemoval: false)]
    private Collection $boughtProjects;

    private function __construct(
        ?string $taxpayer,
        ?string $fantasyName,
    ) {
        $this->id = Uuid::random()->value();
        $this->taxpayer = $taxpayer;
        $this->fantasyName = $fantasyName;
        $this->users = new ArrayCollection();
        $this->ownedProjects = new ArrayCollection();
        $this->boughtProjects = new ArrayCollection();
        $this->isActive = false;
        $this->createdOn = new DateTimeImmutable();
    }

    public static function create($taxpayer, $fantasyName): self
    {
        return new static(
            $taxpayer,
            $fantasyName,
        );
    }

    public function getTaxpayer(): ?string
    {
        return $this->taxpayer;
    }

    public function setTaxpayer(?string $taxpayer): void
    {
        $this->taxpayer = $taxpayer;
    }

    public function getFantasyName(): ?string
    {
        return $this->fantasyName;
    }

    public function setFantasyName(?string $fantasyName): void
    {
        $this->fantasyName = $fantasyName;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    public function getOwnedProjects(): Collection
    {
        return $this->ownedProjects;
    }

    public function addOwnedProject(Project $project): self
    {
        if (!$this->ownedProjects->contains($project)) {
            $this->ownedProjects[] = $project;
            $project->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedProject(Project $project): self
    {
        if ($this->ownedProjects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getOwner() === $this) {
                $project->setOwner(null);
            }
        }

        return $this;
    }

    public function getBoughtProjects(): Collection
    {
        return $this->boughtProjects;
    }

    public function addBoughtProject(Project $project): self
    {
        if (!$this->boughtProjects->contains($project)) {
            $this->boughtProjects[] = $project;
            $project->setBuyer($this);
        }

        return $this;
    }

    public function removeBoughtProject(Project $project): self
    {
        if ($this->boughtProjects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getBuyer() === $this) {
                $project->setBuyer(null);
            }
        }

        return $this;
    }
}
