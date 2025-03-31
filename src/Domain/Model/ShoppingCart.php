<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Common\ShoppingCartStatus;
use App\Domain\Event\DiscountCodeApplied;
use App\Domain\Event\DomainEventInterface;
use App\Domain\Event\EventSourcedEntityInterface;
use App\Domain\Event\ShoppingCartCheckedOut;
use App\Domain\Exception\ShoppingCart\InvalidDiscountException;
use App\Domain\Exception\ShoppingCart\ShoppingCartWorkflowException;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use App\Domain\Service\TaxCalculator;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use function array_reduce;
use function end;
use function number_format;
use function round;

#[ORM\Entity(repositoryClass: ShoppingCartRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class ShoppingCart implements EventSourcedEntityInterface
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'shoppingCarts')]
    private Company $owner;

    #[ORM\OneToMany(targetEntity: ShoppingCartItem::class, mappedBy: 'shoppingCart', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $tax;

    #[ORM\Column(type: 'string', enumType: ShoppingCartStatus::class)]
    private ShoppingCartStatus $status;

    /** @var array<int, DomainEventInterface> */
    private array $domainEvents = [];

    public function __construct(Company $Owner)
    {
        $this->initializeId();
        $this->owner = $Owner;
        $this->isActive = true;
        $this->initializeCreatedOn();
        $this->items = new ArrayCollection();
        $this->status = ShoppingCartStatus::ACTIVE;
        $this->total = '0.00';
        $this->tax = '0.00';
    }

    public static function createWithOwner(Company $Owner): self
    {
        return new self($Owner);
    }

    public function getOwner(): Company
    {
        return $this->owner;
    }

    public function setOwner(Company $Owner): void
    {
        $this->owner = $Owner;
    }

    public function isOwner(string $ownerId): bool
    {
        return $this->owner->getId() === $ownerId;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function setTotal(string $total): void
    {
        $this->total = $total;
    }

    public function getTax(): string
    {
        return $this->tax;
    }

    public function setTax(string $tax): void
    {
        $this->tax = $tax;
    }

    public function getStatus(): ShoppingCartStatus
    {
        return $this->status;
    }

    public function setStatus(ShoppingCartStatus $status): void
    {
        $this->status = $status;
    }

    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function setStatusValue(string $status): void
    {
        $this->status = ShoppingCartStatus::from($status);
    }

    public function addItem(ShoppingCartItem $item): void
    {
        $this->items->add($item);
        $item->setShoppingCart($this);
        $this->calculateTotal();
    }

    public function removeItem(ShoppingCartItem $item): void
    {
        $this->items->removeElement($item);
        $item->setShoppingCart(null);
        $this->calculateTotal();
    }

    public function removeAllItems(): void
    {
        $this->items->clear();
        $this->calculateTotal();
    }

    /**
     * @throws InvalidDiscountException
     */
    public function applyDiscount(?Discount $discount = null): void
    {
        if ($discount === null) {
            return;
        }

        if (!$discount->isValid()) {
            throw InvalidDiscountException::createWithMessage('Invalid discount code');
        }

        $discountAmount = $discount->applyToAmount((int) $this->total);
        $this->total = number_format((float) $this->total - $discountAmount, 2);

        $this->recordEvent(new DiscountCodeApplied(
            $this->id,
            $discount->code(),
            $discountAmount,
        ));
    }

    public function calculateTaxWithCalculator(TaxCalculator $calculator): void
    {
        $total = (float) $this->total;
        $this->tax = number_format($calculator->calculateTaxForAmount($total), 2, '.', '');
    }

    /**
     * @throws ShoppingCartWorkflowException
     */
    public function checkout(?Discount $discount = null): void
    {
        if (!$this->canBeCheckedOut()) {
            throw ShoppingCartWorkflowException::createWithMessage('Shopping cart cannot be checked out', 422);
        }

        $this->calculateTotal($discount);
        $this->calculateTax();

        $this->recordEvent(new ShoppingCartCheckedOut(
            $this->id,
            $this->total,
            $this->tax,
            $this->owner->id(),
        ));
    }

    public function cancel(): void
    {
        $this->removeAllItems();
    }

    /**
     * @return array<int, DomainEventInterface>
     */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    public function applyValidatedDiscount(Discount $discount): void
    {
        $this->calculateTotal($discount);
    }

    public function calculateTotal(?Discount $discount = null): void
    {
        $total = array_reduce($this->getItems()->toArray(), static function ($sum, ShoppingCartItem $item) use ($discount) {
            $itemTotal = (float) $item->getTotalPrice();

            if ($discount !== null && $discount->getTargetProject() !== null
                && $item->getProject() !== null
                && $discount->getTargetProject()->getId() === $item->getProject()->getId()) {
                $itemTotal = $discount->applyToAmount((int) $itemTotal); // the right way here is multiply by 100 to get cents
            } elseif ($discount !== null && $discount->getTargetProject() === null) {
                $itemTotal = $discount->applyToAmount((int) $itemTotal * 100);
            }

            return $sum + $itemTotal;
        }, 0.0);

        $this->total = number_format($total, 2, '.', '');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'owner' => $this->owner->toArray(),
            'items' => $this->items->map(fn (ShoppingCartItem $item) => $item->toArray())->toArray(),
            'total' => $this->total,
            'tax' => $this->tax,
            'status' => $this->status,
            'createdOn' => $this->createdOn,
            'updatedOn' => $this->updatedOn,
        ];
    }

    /**
     * Registra un evento de dominio.
     */
    protected function recordEvent(DomainEventInterface $event): void
    {
        $this->domainEvents[] = $event;
    }

    private function canBeCheckedOut(): bool
    {
        return !$this->items->isEmpty() && $this->status === ShoppingCartStatus::ACTIVE;
    }

    private function calculateTax(): void
    {
        $total = (float) $this->total;
        $tax = $this->calculateTaxForAmount($total);
        $this->tax = number_format($tax, 2, '.', '');
    }

    private function calculateTaxForAmount(float $amount): float
    {
        // Determina el régimen fiscal aplicable
        $taxRegime = $this->determineTaxRegime();

        if ($taxRegime === 'simples_nacional') {
            return $amount * $this->determineSimplesRate(100000)['effectiveRate'];
        }

        // Para otros regímenes, calculamos los impuestos individualmente
        $federalTax = $this->roundTaxValue($this->calculateFederalTaxes($amount));
        $stateTax = $this->roundTaxValue($this->calculateStateTax($amount));
        $municipalTax = $this->roundTaxValue($this->calculateMunicipalTax($amount));

        return $federalTax + $stateTax + $municipalTax;
    }

    /**
     * Calcula los impuestos federales aplicables.
     */
    private function calculateFederalTaxes(float $amount): float
    {
        // Tasas simplificadas para demostración
        $federalRates = [
            'ipi' => 0.10,   // 10% IPI
            'pis' => 0.0165, // 1.65% PIS
            'cofins' => 0.076, // 7.6% COFINS
        ];

        $totalFederalTax = 0;

        foreach ($federalRates as $tax => $rate) {
            $totalFederalTax += $amount * $rate;
        }

        return $totalFederalTax;
    }

    private function calculateStateTax(): float
    {
        return 0.18;
    }

    private function calculateMunicipalTax(float $serviceAmount, ?string $municipality = null): float
    {
        return 0.05;
    }

    /**
     * Determina el régimen fiscal aplicable.
     */
    private function determineTaxRegime(): string
    {
        // Aquí implementarías la lógica para determinar el régimen fiscal
        // Esto podría basarse en el tipo de empresa, ingresos anuales, etc.
        return 'normal'; // o 'simples_nacional', 'lucro_presumido', etc.
    }

    /**
     * Determines the Simples Nacional tax rate based on the company's revenue.
     *
     * @param float  $annualRevenue Company's annual revenue in BRL
     * @param string $activityType  Type of activity (commerce, industry, services)
     *
     * @return array Applicable rates according to Simples Nacional
     */
    private function determineSimplesRate(float $annualRevenue, string $activityType = 'commerce'): array
    {
        // Simples Nacional annexes by activity type
        $activityAnnexes = [
            'commerce' => 'annex1',
            'industry' => 'annex2',
            'general_services' => 'annex3',
            'technical_services' => 'annex4',
            'professional_services' => 'annex5',
        ];

        $annex = $activityAnnexes[$activityType] ?? 'annex1';

        // Simplified tables for demonstration (approximate values for 2023)
        $taxRanges = [
            'annex1' => [ // Commerce
                ['limitBRL' => 180000, 'aliquot' => 0.04, 'deduction' => 0],
                ['limitBRL' => 360000, 'aliquot' => 0.073, 'deduction' => 5940],
                ['limitBRL' => 720000, 'aliquot' => 0.095, 'deduction' => 13860],
                ['limitBRL' => 1800000, 'aliquot' => 0.107, 'deduction' => 22500],
                ['limitBRL' => 3600000, 'aliquot' => 0.143, 'deduction' => 87300],
                ['limitBRL' => 4800000, 'aliquot' => 0.19, 'deduction' => 378000],
            ],
            'annex3' => [ // General services
                ['limitBRL' => 180000, 'aliquot' => 0.06, 'deduction' => 0],
                ['limitBRL' => 360000, 'aliquot' => 0.112, 'deduction' => 9360],
                ['limitBRL' => 720000, 'aliquot' => 0.135, 'deduction' => 17640],
                ['limitBRL' => 1800000, 'aliquot' => 0.16, 'deduction' => 35640],
                ['limitBRL' => 3600000, 'aliquot' => 0.21, 'deduction' => 125640],
                ['limitBRL' => 4800000, 'aliquot' => 0.33, 'deduction' => 648000],
            ],
            // Other annexes would be added here as needed
        ];

        // Determine the applicable range based on annual revenue
        $applicableRange = null;

        foreach ($taxRanges[$annex] as $range) {
            if ($annualRevenue <= $range['limitBRL']) {
                $applicableRange = $range;
                break;
            }
        }

        // If revenue exceeds all ranges, use the last one
        if ($applicableRange === null) {
            $applicableRange = end($taxRanges[$annex]);
        }

        // Calculate effective rate using Simples Nacional formula
        $effectiveRate = (($annualRevenue * $applicableRange['aliquot']) - $applicableRange['deduction']) / $annualRevenue;

        // Approximate breakdown of included taxes (varies by annex)
        $taxBreakdown = [];

        if ($annex === 'annex1') { // Commerce
            $taxBreakdown = [
                'irpj' => $effectiveRate * 0.055,      // Corporate Income Tax
                'csll' => $effectiveRate * 0.05,       // Social Contribution on Net Income
                'cofins' => $effectiveRate * 0.277,     // Contribution for Social Security Financing
                'pis_pasep' => $effectiveRate * 0.06,   // Social Integration Program
                'cpp' => $effectiveRate * 0.417,       // Employer's Social Security Contribution
                'icms' => $effectiveRate * 0.141,       // State Value-Added Tax
            ];
        } elseif ($annex === 'annex3') { // General services
            $taxBreakdown = [
                'irpj' => $effectiveRate * 0.04,
                'csll' => $effectiveRate * 0.035,
                'cofins' => $effectiveRate * 0.216,
                'pis_pasep' => $effectiveRate * 0.047,
                'cpp' => $effectiveRate * 0.428,
                'iss' => $effectiveRate * 0.234,        // Municipal Service Tax
            ];
        }

        return [
            'effectiveRate' => $effectiveRate,
            'breakdown' => $taxBreakdown,
            'annex' => $annex,
            'annualRevenue' => $annualRevenue,
        ];
    }

    /**
     * Rounds tax values according to Brazilian tax legislation.
     *
     * @param float $value     The value to be rounded
     * @param int   $precision Number of decimal places (default: 2)
     *
     * @return float Rounded value
     */
    private function roundTaxValue(float $value, int $precision = 2): float
    {
        return round($value, $precision);
    }

    private function canBeProcessed(): bool
    {
        // Any business rules about when processing is valid
        return $this->status === ShoppingCartStatus::PROCESSING;
    }
}
