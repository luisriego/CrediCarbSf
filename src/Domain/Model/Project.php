<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Common\ProjectStatus;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Repository\ProjectRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

use function mb_strlen;
use function sprintf;

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

    #[Column(type: 'integer')]
    private ?int $areaHa;

    #[Column(type: 'integer')]
    private ?int $quantityInKg;

    #[Column(type: 'integer')]
    private ?int $priceInCents;

    #[Column(type: 'string', nullable: true)]
    private ?string $projectType;

    #[Column(type: 'string', length: 20, nullable: true, enumType: ProjectStatus::class)]
    private ProjectStatus $status;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTime $startDate;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTime $endDate;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'ownedProjects')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Company $owner = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'boughtProjects')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Company $buyer = null;

    public function __construct(
        ?string $name,
        ?string $description,
        ?int $areaHa,
        ?int $quantityInKg,
        ?int $priceInCents,
        ?string $projectType,
        ?Company $owner,
    ) {
        if (empty($name)) {
            throw InvalidArgumentException::createFromMessage('Name cannot be null');
        }

        if (
            empty($description)
            || mb_strlen($description) < self::DESCRIPTION_MIN_LENGTH
            || mb_strlen($description) > self::DESCRIPTION_MAX_LENGTH) {
            throw InvalidArgumentException::createFromMessage('Description cannot be null');
        }

        if (empty($areaHa) || $areaHa < self::AREA_MIN_LENGTH) {
            throw InvalidArgumentException::createFromMessage(
                sprintf('The area must be at least %d ha', self::AREA_MIN_LENGTH),
            );
        }

        if (empty($quantityInKg) || $quantityInKg < self::QUANTITY_MIN_LENGTH) {
            throw InvalidArgumentException::createFromMessage(
                sprintf('The quantityInKg must be at least %d tons', self::QUANTITY_MIN_LENGTH),
            );
        }

        if (empty($priceInCents)) {
            throw InvalidArgumentException::createFromMessage('Price cannot be null');
        }

        if (!$owner instanceof Company) {
            throw InvalidArgumentException::createFromMessage('Owner must be an instance of Company');
        }

        $this->initializeId();
        $this->name = $name;
        $this->description = $description;
        $this->areaHa = $areaHa;
        $this->quantityInKg = $quantityInKg;
        $this->priceInCents = $priceInCents;
        $this->projectType = $projectType;
        $this->status = ProjectStatus::PLANNED;
        $this->owner = $owner;
        $this->isActive = true;
        $this->startDate = new DateTime();
        $this->endDate = new DateTime();
        $this->createdOn = new DateTimeImmutable();
    }

    public static function create(
        $name,
        $description,
        $areaHa,
        $quantityInKg,
        $priceInCents,
        $projectType,
        $owner,
    ): self {
        return new static(
            $name,
            $description,
            $areaHa,
            $quantityInKg,
            $priceInCents,
            $projectType,
            $owner,
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

    public function areaHa(): int
    {
        return $this->areaHa;
    }

    public function quantityInKg(): int
    {
        return $this->quantityInKg;
    }

    public function priceInCents(): int
    {
        return $this->priceInCents;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
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

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function changePhase(ProjectStatus $newPhase): void
    {
        $this->status = $newPhase;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'areaHa' => $this->areaHa,
            'quantityInKg' => $this->quantityInKg,
            'priceInCents' => $this->priceInCents,
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
                'completed' => $this->status === ProjectStatus::COMPLETED,
            ],
            'startDate' => $this->startDate?->format('Y-m-d'),
            'endDate' => $this->endDate?->format('Y-m-d'),
            'completionPercentage' => $this->status->getCompletionPercentage(),
        ];
    }
}
