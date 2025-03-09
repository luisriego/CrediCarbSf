<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Repository\DiscountRepositoryInterface;
use App\Domain\Trait\CreatedByTrait;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Random\RandomException;
use Symfony\Component\Validator\Constraints as Assert;

use function max;
use function mb_strlen;
use function mb_strtoupper;
use function random_int;

#[ORM\Entity(repositoryClass: DiscountRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class Discount
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;
    use CreatedByTrait;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\Regex(pattern: '/^[A-Z0-9]+$/', message: 'Discount code must be alphanumeric and uppercase')]
    private string $code;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'boolean')]
    private bool $isPercentage;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $expiresAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $approvedBy;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Project $targetProject = null;

    /**
     * @throws RandomException
     */
    private function __construct(User $user, int $amount, DateTimeImmutable $expiresAt, bool $isPercentage = true, string $code = '')
    {
        $this->initializeId();
        $this->initializeCreatedOn();
        $this->validateAmount($amount);
        $this->amount = $amount;
        $this->expiresAt = $this->validateExpirationDate($expiresAt);
        $this->code = empty($code) ? self::codeGenerator() : mb_strtoupper($code);
        $this->isPercentage = $isPercentage;
        $this->isActive = false;
        $this->setCreator($user);
    }

    /**
     * @throws RandomException
     */
    public static function createWithAmountAndExpirationDate($user, $amount, $expiresAt): self
    {
        return new static(
            $user,
            $amount,
            $expiresAt,
        );
    }

    /**
     * @throws RandomException
     */
    public static function createWithAmountAndExpirationDateNotPercentage($user, $amount, $expiresAt): self
    {
        return new static(
            $user,
            $amount,
            $expiresAt,
            false,
        );
    }

    /**
     * @throws RandomException
     */
    public static function createWithProjectToApply(User $user, int $amount, DateTimeImmutable $expiresAt, Project $project): self
    {
        $discount = new static($user, $amount, $expiresAt);
        $discount->setTargetProject($project);

        return $discount;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function isPercentage(): bool
    {
        return $this->isPercentage;
    }

    public function expiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function updateExpirationDate(?DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getTargetProject(): ?Project
    {
        return $this->targetProject;
    }

    public function setTargetProject(?Project $targetProject): void
    {
        $this->targetProject = $targetProject;
    }

    public function applyToAmount(int $originalAmount, ?Project $project = null): float
    {
        if ($this->targetProject !== $project && $this->targetProject !== null) {
            return $originalAmount;
        }

        $discountAmount = $this->amount / 100;

        if ($this->isPercentage) {
            return $originalAmount * (1 - $discountAmount / 100);
        }

        return max(0, $originalAmount - $discountAmount);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'amount' => $this->amount,
            'isPercentage' => $this->isPercentage,
            'isActive' => $this->isActive,
            'expiresAt' => $this->expiresAt?->format('c'),
            'targetProjectId' => $this->targetProject?->getId(),
        ];
    }

    public function isValid(): bool
    {
        if (!$this->isActive) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt < new DateTimeImmutable()) {
            return false;
        }

        return true;
    }

    /**
     * Generates a random alphanumeric uppercase discount code.
     *
     * @param int $length The length of the generated code (default is 10)
     *
     * @return string The generated discount code
     *
     * @throws RandomException
     */
    private static function codeGenerator(int $length = 10): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, mb_strlen($characters) - 1)];
        }

        return $code;
    }

    private function validateExpirationDate(DateTimeImmutable $expiresAt): DateTimeImmutable
    {
        $now = new DateTimeImmutable();
        $minExpirationDate = $now->add(new DateInterval('P1D'));

        if ($expiresAt < $minExpirationDate) {
            throw new InvalidArgumentException('The expiration date must be at least +1 day from today.');
        }

        return $expiresAt;
    }

    private function validateAmount(int $amount): void
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('The discount amount cannot be negative.');
        }
    }
}
