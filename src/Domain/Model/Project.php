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
use App\Domain\Common\ProjectStatus;

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

    #[Column(type: 'string', length: 100, nullable: false)]
    private ?string $name;

    #[Column(type: 'string', length: 1500, nullable: true)]
    private ?string $description = null;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $areaHa;

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $quantity; // Quantity in tons of CO2

    #[Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $price; // Current Price of the CO2 Project ton unit

    #[Column(type: 'string', nullable: true)]
    private ?string $projectType;

    #[Column(type: 'string', enumType: ProjectStatus::class, length: 20, nullable: true)]
    private ProjectStatus $status;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTime $startDate;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTime $endDate;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'ownedProjects')]
    private ?Company $owner = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'boughtProjects')]
    private ?Company $buyer = null;

    public function __construct(
        ?string $name,
        ?string $description,
        ?string $areaHa,
        ?string $quantity,
        ?string $price,
        ?string $projectType,
    ) {
        $this->id = Uuid::random()->value();
        $this->name = $name;
        $this->description = $description;
        $this->areaHa = $areaHa;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->projectType = $projectType;
        $this->status = ProjectStatus::PLANNED;
        $this->isActive = true;
        $this->startDate = new DateTime();
        $this->endDate = new DateTime();
        $this->createdOn = new DateTimeImmutable();
    }

    public static function create(
        $name,
        $description,
        $areaHa,
        $quantity,
        $price,
        $projectType
    ): self {
        return new static(
            $name,
            $description,
            $areaHa,
            $quantity,
            $price,
            $projectType,
        );
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getAreaHa(): string
    {
        return $this->areaHa;
    }

    public function setAreaHa(string $areaHa): void
    {
        $this->areaHa = $areaHa;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
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

    public function getProjectType(): string
    {
        return $this->projectType;
    }

    public function setProjectType(string $projectType): void
    {
        $this->projectType = $projectType;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(ProjectStatus $status): void
    {
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'areaHa' => $this->areaHa,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'startDate' => $this->startDate?->format('Y-m-d H:i:s'),
            'endDate' => $this->endDate?->format('Y-m-d H:i:s'),
            'projectType' => $this->projectType,
            'status' => $this->status->value,
            'isActive' => $this->isActive,
            'createdOn' => $this->createdOn->format('Y-m-d H:i:s'),
        ];
    }

    public function trackProgress(): array
    {
        return [
            'currentStatus' => $this->status->getValue(),
            'milestones' => [
                'planning' => $this->status === ProjectStatus::PLANNED,
                'development' => $this->status === ProjectStatus::IN_DEVELOPMENT,
                'execution' => $this->status === ProjectStatus::IN_EXECUTION,
                'completed' => $this->status === ProjectStatus::COMPLETED
            ],
            'startDate' => $this->startDate?->format('Y-m-d'),
            'endDate' => $this->endDate?->format('Y-m-d'),
            'completionPercentage' => $this->status->getCompletionPercentage()
        ];
    }
}
