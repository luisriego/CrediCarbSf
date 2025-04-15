<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Repository\BudgetExpenseRepository;
use App\Domain\Trait\TimestampableTrait;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Uuid;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetExpenseRepository::class)]
#[ORM\Table(name: 'budget_expenses')]
#[ORM\HasLifecycleCallbacks]
class BudgetExpense
{
    use TimestampableTrait;

    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, options: ['fixed' => true])]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Embedded(class: Money::class, columnPrefix: "budgeted_")]
    private Money $budgetedAmount;

    #[ORM\Embedded(class: Money::class, columnPrefix: "actual_")]
    private ?Money $actualAmount = null;

    #[ORM\Column(type: "string", length: 100)]
    private string $category;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "budgetExpenses")]
    #[ORM\JoinColumn(name: "project_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?Project $project;

    #[ORM\Column(type: "string", length: 20)]
    private string $status;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: "receipt_url", type: "string", length: 255, nullable: true)]
    private ?string $receiptUrl = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $tags = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeImmutable $completedAt = null;


    public function __construct(
        Uuid $id,
        string $title,
        string $description,
        Money $budgetedAmount,
        string $category,
        ?Project $project = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->budgetedAmount = $budgetedAmount;
        $this->category = $category;
        $this->createdOn = new DateTimeImmutable();
        $this->status = self::STATUS_PLANNED;
        $this->project = $project;
    }

    public function complete(?Money $actualAmount = null): self
    {
        if ($actualAmount !== null) {
            $this->actualAmount = $actualAmount;
        }

        $this->completedAt = new DateTimeImmutable();
        $this->status = self::STATUS_COMPLETED;

        return $this;
    }

    public function cancel(): self
    {
        $this->status = self::STATUS_CANCELLED;
        return $this;
    }

    /**
     * Calculate the variance between budgeted and actual amounts
     */
    public function getVariance(): ?Money
    {
        if ($this->actualAmount === null) {
            return null;
        }

        return $this->budgetedAmount->subtract($this->actualAmount);
    }

    /**
     * Check if the expense is over budget
     */
    public function isOverBudget(): bool
    {
        if ($this->actualAmount === null) {
            return false;
        }

        return $this->actualAmount->isGreaterThan($this->budgetedAmount);
    }
}