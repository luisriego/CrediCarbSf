<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Repository\ProjectRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use App\Domain\ValueObjects\Uuid;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

#[ORM\Entity(repositoryClass: ProjectRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    public const DESCRIPTION_MIN_LENGTH = 10;
    public const DESCRIPTION_MAX_LENGTH = 1500;
    public const AREA_MIN_LENGTH = 0.5;
    public const QUANTITY_MIN_LENGTH = 1;

    #[Column(type: 'string', length: 1500, nullable: true)]
    private ?string $description = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private string $areaHa;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private string $quantity; // Quantity in tons of CO2

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price; // Current Price of the CO2 Project ton unit

    #[Column(type: 'string', enumType: ProjectType::class)]
    private ProjectType $projectType;

    private DateTime $startDate;

    private DateTime $endDate;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'ownedProjects')]
    private ?Company $owner = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'boughtProjects')]
    private ?Company $buyer = null;

    private function __construct(
        ?string $description,
        float $areaHa,
        float $quantity,
        float $price,
        ProjectType $projectType,
        DateTime $startDate,
        DateTime $endDate,
        Company $owner,
        ?Company $buyer = null,
    ) {
        $this->id = Uuid::random()->value();
        $this->description = $description;
        $this->areaHa = $areaHa;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->projectType = $projectType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->owner = $owner;
        $this->buyer = $buyer;
        $this->isActive = false;
        $this->createdOn = new DateTimeImmutable();
    }

    public static function create(
        $description,
        $areaHa,
        $quantity,
        $price,
        $projectType,
        $startDate,
        $endDate,
        $owner,
        $buyer = null,
    ): self {
        return new static(
            $description,
            $areaHa,
            $quantity,
            $price,
            $projectType,
            $startDate,
            $endDate,
            $owner,
            $buyer,
        );
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getAreaHa(): float
    {
        return $this->areaHa;
    }

    public function setAreaHa(float $areaHa): void
    {
        $this->areaHa = $areaHa;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getOwner(): Company
    {
        return $this->owner;
    }

    public function setOwner(Company $owner): void
    {
        $this->owner = $owner;
    }

    public function getBuyer(): ?Company
    {
        return $this->buyer;
    }

    public function setBuyer(?Company $buyer): void
    {
        $this->buyer = $buyer;
    }

    public function getProjectType(): ProjectType
    {
        return $this->projectType;
    }

    public function setProjectType(ProjectType $projectType): void
    {
        $this->projectType = $projectType;
    }
}
