<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

use function number_format;
use function sprintf;

#[ORM\Embeddable]
class Money
{
    #[ORM\Column(type: 'integer')]
    private int $amountInCents;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    public function __construct(int $amountInCents, string $currency)
    {
        if ($amountInCents < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        $this->amountInCents = $amountInCents;
        $this->currency = $currency;
    }

    public function __toString(): string
    {
        return sprintf('%.2f %s', $this->formattedAmount(), $this->currency);
    }

    public function amountInCents(): int
    {
        return $this->amountInCents;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function formattedAmount(): string
    {
        return number_format($this->amountInCents / 100, 2) . ' ' . $this->currency;
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot add money with different currencies');
        }

        return new self($this->amountInCents + $other->amountInCents, $this->currency);
    }

    public function isEqual(Money $other): bool
    {
        if ($this->currency !== $other->currency) {
            return false;
        }

        return $this->amountInCents === $other->amountInCents;
    }

    public function isGreaterThan(Money $budgetedAmount): bool
    {
        return $this->amountInCents > $budgetedAmount->amountInCents;
    }

    public function subtract(?Money $actualAmount): self
    {
        return new self($this->amountInCents - ($actualAmount ? $actualAmount->amountInCents : 0), $this->currency);
    }
}
